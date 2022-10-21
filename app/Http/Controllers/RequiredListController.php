<?php

namespace App\Http\Controllers;

use App\Events\NewRequiredListCreatedEvent;
use App\Http\Requests\CreateNewEmptyListRequest;
use App\Http\Requests\DeleteRequiredListRequest;
use App\Http\Requests\EditRequiredListRequest;
use App\Http\Requests\RequiredListAddNewProductRequest;
use App\Http\Requests\RequiredListFileColumnValidation;
use App\Http\Requests\RequiredListUploadRequest;
use App\Models\NewItemScan\ScannedSn;
use App\Models\RequiredList;
use App\Models\RequiredListItem;
use App\Models\RequiredListPallet;
use App\Models\Setting;
use App\Utilities\Audios;
use App\Utilities\Constants;
use App\Utilities\GoodsFlow;
use App\Utilities\Lock;
use App\Utilities\PrivilegeUtilities;
use App\Utilities\WarehouseLabel;
use App\Utilities\XlsxGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class RequiredListController extends Controller
{
    private array $allRazerItemsFromGoodsFlow;

    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_MANAGE_REQUIRED_LISTS]);
    }

    public function createWarehouseLabelForRequiredList(Request $request, WarehouseLabel $label)
    {
        if ($generated_label = $label->generate($request->text)) {
            return response()->json(['warehouse_label_key' => $generated_label->warehouse_key]);
        } else {
            return response()->json(['message' => 'Failed to get warehouse label'], 422);
        }

    }

    public function requiredList()
    {
        return view('requiredList.requiredList');
    }

    public function requiredListUpload(RequiredListUploadRequest $request)
    {
        Lock::db(['required_list_items', 'required_lists']);
        $list = $this->createRequiredList($request);

        if ($list['validator']['status'] === false) {
            $this->removeIfFails($list['requiredList']->id);
            Lock::remove();
            return response()->json(['errors' => $list['validator']['errors']], 422);
        }

        if (!$this->writeToDatabase($list['arrayForDatabaseInsert'])) {
            $this->removeIfFails($list['requiredList']->id);
            Lock::remove();
            return response()->json(['message' => 'Upload failed'], 422);
        } else {
            Lock::remove();
            broadcast(new NewRequiredListCreatedEvent());
            return response()->json(['message' => 'List created']);
        }

    }

    public function getActiveRequiredLists()
    {
        return response()->json(RequiredList::whereActive(true)->orderBy('priority', 'ASC')->get()->toArray());
    }

    public function getActiveRequiredListIds()
    {
        return response()->json(RequiredList::whereActive(true)->get(['id'])->toArray());
    }

    public function getDisabledRequiredLists()
    {
        return response()->json(RequiredList::whereActive(false)->orderBy('created_at', 'DESC')->limit(15)->get()->toArray());
    }

    public function closeRequiredPallet(Request $request)
    {
        if (RequiredListPallet::whereId($request->id)->first()->update(['closed' => true])) {
            return response()->json(['message' => 'Pallet closed']);
        } else {
            return response()->json(['message' => "Failed to close pallet"], 422);
        }

    }

    public function downloadRequiredPalletXlsx($id)
    {
        $pallet = RequiredListPallet::whereId($id)->with(['requiredListPalletItems'])->first()->toArray();

        if (!$pallet['closed']) {
            return response()->json(['message' => "You need to close the pallet"], 422);
        }

        $generator = new XlsxGenerator($pallet);

        $spreadSheet = new Spreadsheet();
        $spreadSheet->getDefaultStyle()->getFont()->setName('Consolas');
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->freezePane('A2');
        $sheet->setTitle('Pallet');
        $sheet->fromArray($generator->getRequiredPalletHeaders(), "", 'A1');
        $spreadSheet->getSheetByName('Pallet')->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->fromArray($generator->getRequiredPallet(), "", 'A2');

        $spreadSheet->getSheetByName('Pallet')->getColumnDimensionByColumn(1)->setWidth(20);
        $spreadSheet->getSheetByName('Pallet')->getColumnDimensionByColumn(2)->setWidth(20);
        $spreadSheet->getSheetByName('Pallet')->getColumnDimensionByColumn(3)->setWidth(22);
        $spreadSheet->getSheetByName('Pallet')->getColumnDimensionByColumn(4)->setWidth(20);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadSheet);
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($pallet['text'] . ".xlsx") . '"');
        $writer->save("php://output");

    }

    public function openRequiredPallet(Request $request)
    {
        Lock::db('required_list_pallets');
        if (RequiredListPallet::whereRequiredListId($request->listId)->whereClosed(false)->exists()) {
            return response()->json(['message' => "Only one pallet can be opened"], 422);
        } else {
            RequiredListPallet::whereId($request->id)->update(['closed' => false]);
            Lock::remove();
            return response()->json(['message' => 'Pallet opened']);
        }

    }

    public function deleteRequiredPallet(Request $request)
    {
        Lock::db('required_list_pallets');
        $pallet = RequiredListPallet::whereId($request->id)->first();
        if ($pallet->requiredListPalletItems->count() > 0) {
            Lock::remove();
            return response()->json(['message' => "You can delete only empty pallets"], 422);
        } else {
            $pallet->delete();
            Lock::remove();
            return response()->json(['message' => 'Pallet deleted']);
        }

    }

    public function getSelectedListPallets(Request $request)
    {
        return response()->json(RequiredListPallet::whereRequiredListId($request->listId)->orderBy('created_at', 'DESC')->paginate($request->rows, ['*'], 'page', $request->page));
    }

    public function savePriorities(Request $request)
    {
        foreach ($request->lists as $key => $list) {
            $requiredList = RequiredList::whereId($list['id'])->first();
            $requiredList->priority = $key + 1;
            $requiredList->save();
        }
        return response()->json(['message' => 'Priorities updated']);
    }

    public function selectRequiredListToDisplay(Request $request): JsonResponse
    {
        return response()->json(RequiredList::whereId($request->id)->with(['requiredListItems'])->first()->toArray());
    }

    public function editRequiredListQuantity(Request $request): JsonResponse
    {
        $item = RequiredListItem::whereId($request->input('product')['id'])->first();
        $item->count = $request->input('product')['count'];
        if ($item->save()) {
            return response()->json(['message' => 'Quantity updated for ' . $request->input('product')['rz']]);
        } else {
            return response()->json(['message' => 'Quantity update failed'], 422);
        }
    }

    public function deleteRequiredListProduct(Request $request): JsonResponse
    {
        $item = RequiredListItem::whereId($request->input('product')['id'])->first();
        if ($item->delete()) {
            return response()->json(['message' => $item->rz . " deleted from required lists"]);
        } else {
            return response()->json(['message' => 'Try again'], 422);
        }
    }

    public function deleteRequiredListProducts(Request $request): JsonResponse
    {
        $count = 0;
        foreach ($request->input('products') as $product) {
            if (!RequiredListItem::whereId($product['id'])->delete()) {
                return response()->json(['message' => 'Request failed somewhere'], 422);
            }
            $count = $count + 1;
        }
        return response()->json(['message' => $count . ' items deleted']);
    }

    public function RequiredListAddNewProduct(RequiredListAddNewProductRequest $request): JsonResponse
    {
        $item = new RequiredListItem();
        $item->required_list_id = $request->listId;
        $item->rz = $request->rz;
        $item->count = $request->count;
        $item->name = GoodsFlow::getArtikelNameByRz($item->rz);
        if ($item->save()) {
            return response()->json(['message' => 'New item added to list']);
        } else {
            return response()->json(['message' => 'Unable to add new item to required list'], 422);
        }

    }

    public function editRequiredList(EditRequiredListRequest $request): JsonResponse
    {
        $list = RequiredList::whereId($request->listId)->first();

        if ($list->name != $request->name) {
            $this->editScannedSnResults($list->name, $request->name);
        }

        $needToOverwrite = !($list->audioText == $request->audioText);

        $list->name = $request->name;
        $list->audioText = $request->audioText;

        if ($list->save()) {

            if ($needToOverwrite) {
                Audios::deleteAllAudiosForRequiredListById($list->id);
                broadcast(new NewRequiredListCreatedEvent([$list->id]));
            }

            return response()->json(['message' => 'Update Successful']);
        } else {
            return response()->json(['message' => 'Update failed'], 422);
        }

    }

    private function editScannedSnResults($oldName, $newName)
    {
        ScannedSn::whereResult($oldName)->whereHas('newItemScan', function ($query) {
            $query->where('status', '=', [Constants::SCAN_STATUS_NEW, Constants::SCAN_STATUS_DONE]);
        })->update(['result' => $newName]);
    }

    public function createNewEmptyList(CreateNewEmptyListRequest $request): JsonResponse
    {
        $list = new RequiredList();
        $list->audioText = $request->audioText;
        $list->name = $request->name;
        if ($list->save()) {
            return response()->json(['message' => 'New list created']);
        } else {
            return response()->json(['message' => 'Failed to create new list']);
        }
    }

    public function getNewItemScanLockStatus(): JsonResponse
    {
        return response()->json(['status' => (boolean)Setting::whereName(Constants::NEW_ITEM_SCAN_LOCK_STATUS)->first()->value]);
    }

    public function lockNewItemScan(): JsonResponse
    {
        $setting = Setting::whereName(Constants::NEW_ITEM_SCAN_LOCK_STATUS)->first();
        $setting->value = true;
        if ($setting->save()) {
            return response()->json(['message' => 'New item scan is now locked']);
        } else {
            return response()->json(['message' => 'Failed to change lock status']);
        }

    }

    public function unlockNewItemScan(): JsonResponse
    {
        $setting = Setting::whereName(Constants::NEW_ITEM_SCAN_LOCK_STATUS)->first();
        $setting->value = false;
        if ($setting->save()) {
            return response()->json(['message' => 'New item scan is now unlocked']);
        } else {
            return response()->json(['message' => 'Failed to change lock status']);
        }

    }

    public function deactivateRequiredList(Request $request): JsonResponse
    {
        $list = RequiredList::whereId($request->listId)->first();
        $list->active = false;
        if ($list->save()) {
            return response()->json(['message' => 'List deactivated']);
        } else {
            return response()->json(['message' => 'Failed to deactivate list']);
        }
    }


    public function activateRequiredList(Request $request): JsonResponse
    {
        $list = RequiredList::whereId($request->listId)->first();
        $list->active = true;
        $list->priority = $this->getNextPriority();
        if ($list->save()) {
            broadcast(new NewRequiredListCreatedEvent());
            return response()->json(['message' => 'List activated']);
        } else {
            return response()->json(['message' => 'Failed to activate list']);
        }
    }

    public function getRequiredListActivityStatus(Request $request): JsonResponse
    {
        return response()->json(['active' => (boolean)RequiredList::whereId($request->listId)->first()->active]);
    }

    public function deleteRequiredList(DeleteRequiredListRequest $request): JsonResponse
    {
        $list = RequiredList::whereId($request->listId)->first();
        if ($list->requiredListPallets()->exists()) {
            return response()->json(['message' => "You can't delete required list if it has pallets in it"], 422);
        }
        $list->delete();
        RequiredListItem::whereRequiredListId($request->listId)->delete();
        Audios::deleteAllAudiosForRequiredListById($request->listId);
        return response()->json(['message' => 'List deleted']);
    }

    private function writeToDatabase($databaseArrays): bool
    {
        $chunks = 0;
        foreach ($databaseArrays as $bdArray) {
            if (!RequiredListItem::insert($bdArray)) {
                return false;
            }
            $chunks++;
        }
        return true;

    }

    private function xlsxRowsToArray($filePath): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly(['List']);
        $reader->setReadEmptyCells(false);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getSheetByName("List");


        return $sheet->toArray();
    }

    private function createDatabaseArray($xlsxArray, $listId): JsonResponse|array
    {
        $this->allRazerItemsFromGoodsFlow = GoodsFlow::getAllRazerArtikelsFromGoodsFlow();
        $databaseArrays = [];
        $rows = [];
        $chunks = 1;
        $rowsInChunk = 0;
        foreach ($xlsxArray as $row) {
            if (in_array($row[0], $rows)) {
                return abort(422, 'Duplicated rows (products)');
            } else {
                $rows[] = $row[0];
                $databaseArrays[$chunks][] = [
                    'required_list_id' => $listId,
                    'rz' => $row[0],
                    'count' => $row[1],
                    'name' => $this->getNameFromGoodsFlow($row[0]),
                ];
                $rowsInChunk++;
                if ($rowsInChunk >= 1500) {
                    $rowsInChunk = 0;
                    $chunks++;
                }
            }
        }
        return $databaseArrays;
    }

    private function getNameFromGoodsFlow($rz)
    {
        $name = 'Item not Found in GoodsFlow database';
        if (array_key_exists($rz, $this->allRazerItemsFromGoodsFlow)) {
            $name = $this->allRazerItemsFromGoodsFlow[$rz];
        }
        return $name;
    }

    private function createRequiredList(RequiredListUploadRequest $request): array
    {
        $requiredList = new RequiredList();
        $requiredList->name = $request->name;
        $requiredList->active = true;
        $requiredList->priority = $this->getNextPriority();
        $requiredList->audioText = $request->audioText;
        $requiredList->save();

        $xlsxArray = $this->xlsxRowsToArray($request->file('file'));
        $arrayForDatabaseInsert = $this->createDatabaseArray($xlsxArray, $requiredList->id);
        $rows = new RequiredListFileColumnValidation();
        $validator = $this->validateArray($rows, $arrayForDatabaseInsert);

        return ['requiredList' => $requiredList, 'validator' => $validator, 'arrayForDatabaseInsert' => $arrayForDatabaseInsert];
    }

    private function removeIfFails($listId)
    {
        RequiredList::whereId($listId)->delete();
        RequiredListItem::whereRequiredListId($listId)->delete();
    }

    private function getNextPriority()
    {
        $nextPriority = 1;
        $activeLists = RequiredList::whereActive(true)->orderBy('priority', 'DESC')->first();
        if ($activeLists) {
            $nextPriority = $activeLists->priority + 1;
        }
        return $nextPriority;
    }

    private function validateArray(RequiredListFileColumnValidation $row, $arrayForDatabaseInsert): array
    {
        foreach ($arrayForDatabaseInsert as $chunk) {
            foreach ($chunk as $chunkRow) {

                $row->setValues($chunkRow);

                $validator = Validator::make($row->all(), $row->rules(), $row->messages());

                if ($validator->fails()) {
                    return ['status' => false, 'errors' => $validator->errors()];
                }
            }
        }
        return ['status' => true];
    }
}

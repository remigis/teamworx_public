<?php

namespace App\Http\Controllers;

use App\Http\Requests\RAZERBatteryGoodBadAddConfirmationRequest;
use App\Http\Requests\RAZERBatteryGoodBadAddRequest;
use App\Http\Requests\RAZERBatteryGoodBadDeleteRequest;
use App\Http\Requests\RAZERBatteryGoodBadEditRequest;
use App\Http\Requests\RAZERBatteryGoodBadEditSearchRequest;
use App\Http\Requests\RAZERBatteryGoodBadUploadRequest;
use App\Models\RazerBatteryGoodBad as RazerBatteryGoodBadModel;
use App\Utilities\GoodsFlow;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class RazerBatteryGoodBad extends Controller
{
    private array $allRazerItemsFromGoodsFlow;

    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_EDIT_BATTERY_SCRAP_EAN_LIST]);
    }

    public function RAZERBatteryGoodBad()
    {
        return view('tools.RAZERBatteryGoodBad');
    }

    public function RAZERBatteryGoodBadUpload(RAZERBatteryGoodBadUploadRequest $request)
    {
        $this->allRazerItemsFromGoodsFlow = GoodsFlow::getAllRazerArtikelsFromGoodsFlow();

        $listItems = $this->xlsxRowsToArray($request->file('file'));
        if ($this->writeToDatabase($this->createDatabaseArray($listItems))) {
            return response()->json(['message' => 'Upload successful, list generated']);
        } else {
            return response()->json(['message' => 'Upload failed'], 422);
        }
    }

    public function RAZERBatteryGoodBadDownload(Request $request)
    {
        $items = RazerBatteryGoodBadModel::all(['battery', 'scrap', 'rz', 'ean'])->toArray();
        $spreadSheet = new Spreadsheet();
        $spreadSheet->getDefaultStyle()->getFont()->setName('Consolas');
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setTitle("List");
        $sheet->fromArray($items, "", 'A1');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadSheet);
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode('Battery/ScrapList.xlsx') . '"');
        $writer->save("php://output");

    }


    private function writeToDatabase($databaseArrays): bool
    {
        DB::raw('LOCK TABLES `razer_battery_good_bads` WRITE');
        RazerBatteryGoodBadModel::truncate();
        $chunks = 0;
        foreach ($databaseArrays as $bdArray) {
            if (!RazerBatteryGoodBadModel::insert($bdArray)) {
                return false;
            }
            $chunks++;
        }
        DB::raw('UNLOCK TABLES');
        return true;

    }

    protected function createDatabaseArray($rowsArray): array
    {
        $databaseArrays = [];
        $chunks = 1;
        $rowsInChunk = 0;
        foreach ($rowsArray as $row) {
            $databaseArrays[$chunks][] = [
                'battery' => $row[0],
                'scrap' => $row[1],
                'rz' => $row[2],
                'ean' => $row[3],
                'name' => $this->getNameFromGoodsFlow($row[2]),
            ];
            $rowsInChunk++;
            if ($rowsInChunk >= 1500) {
                $rowsInChunk = 0;
                $chunks++;
            }
        }
        return $databaseArrays;
    }


    /**
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    private function xlsxRowsToArray($filePath): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly(['List']);
        $reader->setReadEmptyCells(false);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getSheetByName("List");
        $sheet->getStyle('A:E')->getNumberFormat()->setFormatCode('0');

        return $sheet->toArray();
    }

    public function RAZERBatteryGoodBadAddConfirmation(RAZERBatteryGoodBadAddConfirmationRequest $request)
    {
        if (RazerBatteryGoodBadModel::whereRz($request->rz)->exists()) {
            return response()->json(["message" => 'Item already in the list use edit'], 422);
        } else {
            return response()->json(["message" => 'Item can be added to list']);
        }
    }

    public function RAZERBatteryGoodBadAdd(RAZERBatteryGoodBadAddRequest $request): \Illuminate\Http\JsonResponse
    {
        $newListItem = new RazerBatteryGoodBadModel();
        $newListItem->rz = $request->rz;
        $newListItem->ean = $request->ean;
        $newListItem->scrap = $this->convertYesNo($request->scrap);
        $newListItem->battery = $this->convertYesNo($request->battery);
        $this->allRazerItemsFromGoodsFlow = GoodsFlow::getAllRazerArtikelsFromGoodsFlow();
        $newListItem->name = $this->getNameFromGoodsFlow($request->rz);
        DB::raw('LOCK TABLES `razer_battery_good_bads` WRITE');
        if ($newListItem->save()) {
            DB::raw('UNLOCK TABLES');
            return response()->json(["message" => 'Item added to the list']);
        } else {
            DB::raw('UNLOCK TABLES');
            return response()->json(["message" => 'Failed to save the record'], 422);
        }

    }

    private function convertYesNo($string): \Illuminate\Http\JsonResponse|bool|null
    {
        if ($string === 'Yes') {
            return true;
        } elseif ($string === 'No') {
            return null;
        } else {
            return response()->json(["message" => 'Battery/Scarp fields should be Yes or No'], 422);
        }
    }

    private function convertToYesNo($value): string
    {
        if ($value === null) {
            return "No";
        } else {
            return "Yes";
        }
    }

    public function RAZERBatteryGoodBadEditSearch(RAZERBatteryGoodBadEditSearchRequest $request): \Illuminate\Http\JsonResponse
    {
        $itemsWithLabel = [];
        $items = RazerBatteryGoodBadModel::where('rz', 'LIKE', "%" . $request->rz . "%")->orWhere('name', 'LIKE', "%" . $request->rz . "%")->get(['rz', 'name'])->toArray();
        foreach ($items as $key => $item) {
            $itemsWithLabel[$key]['label'] = $item['rz'] . ": " . $item['name'];
            $itemsWithLabel[$key]['rz'] = $item['rz'];
            $itemsWithLabel[$key]['name'] = $item['name'];
        }

        return response()->json($itemsWithLabel);
    }

    public function RAZERBatteryGoodBadEditRZSelected(RAZERBatteryGoodBadEditSearchRequest $request): \Illuminate\Http\JsonResponse
    {
        $item = RazerBatteryGoodBadModel::where('rz', '=', $request->rz)->first(['rz', 'battery', 'scrap', 'ean']);
        $item->scrap = $this->convertToYesNo($item->scrap);
        $item->battery = $this->convertToYesNo($item->battery);
        return response()->json($item->toArray());
    }

    public function RAZERBatteryGoodBadSubmitEditedRZ(RAZERBatteryGoodBadEditRequest $request): \Illuminate\Http\JsonResponse
    {

        $ListItem = RazerBatteryGoodBadModel::whereRz($request->rz)->first();
        $ListItem->rz = $request->rz;
        $ListItem->ean = $request->ean;
        $ListItem->scrap = $this->convertYesNo($request->scrap);
        $ListItem->battery = $this->convertYesNo($request->battery);
        DB::raw('LOCK TABLES `razer_battery_good_bads` WRITE');
        if ($ListItem->save()) {
            DB::raw('UNLOCK TABLES');
            return response()->json(["message" => 'Item Update successful']);
        } else {
            DB::raw('UNLOCK TABLES');
            return response()->json(["message" => 'Failed to edit the record'], 422);
        }
    }

    public function RAZERBatteryGoodBadSubmitRZForDelete(RAZERBatteryGoodBadDeleteRequest $request): \Illuminate\Http\JsonResponse
    {
        $ListItem = RazerBatteryGoodBadModel::whereRz($request->rz)->first();
        DB::raw('LOCK TABLES `razer_battery_good_bads` WRITE');
        if ($ListItem->delete()) {
            DB::raw('UNLOCK TABLES');
            return response()->json(["message" => 'Item deleted']);
        } else {
            DB::raw('UNLOCK TABLES');
            return response()->json(["message" => 'Failed to delete the record'], 422);
        }
    }

    private function getNameFromGoodsFlow($rz)
    {
        $name = 'Item not Found in GoodsFlow database';
        if (array_key_exists($rz, $this->allRazerItemsFromGoodsFlow)) {
            $name = $this->allRazerItemsFromGoodsFlow[$rz];
        }
        return $name;
    }

}

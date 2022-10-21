<?php

namespace App\Http\Controllers;

use App\Events\CheckRequiredListStatusChange;
use App\Events\RazerAPIStatusChange;
use App\Http\Requests\ClosePalletRequest;
use App\Http\Requests\DeleteScannedProductRequest;
use App\Http\Requests\GenerateXlsxRequest;
use App\Http\Requests\GetNeedToScanSNsRequest;
use App\Http\Requests\GoToScanRequest;
use App\Http\Requests\MakeScanDoneRequest;
use App\Http\Requests\MultipleNoSnSubmitRequest;
use App\Http\Requests\OpenPalletRequest;
use App\Http\Requests\RAZERBatteryGoodBadEditSearchRequest;
use App\Http\Requests\ScannedSnEditRequest;
use App\Http\Requests\SNEanSubmitRequest;
use App\Http\Requests\SNSubmitRequest;
use App\Models\NewItemScan\ItemToScan;
use App\Models\NewItemScan\NewItemScan as NewItemScanModel;
use App\Models\NewItemScan\ScannedSn;
use App\Models\NewItemScan\ScrapItem;
use App\Models\NewItemScanPallet;
use App\Models\NewItemScanPalletItem;
use App\Models\RazerBatteryGoodBad as RazerBatteryGoodBadModel;
use App\Models\RequiredList;
use App\Models\RequiredListPallet;
use App\Models\RequiredListPalletItem;
use App\Models\Setting;
use App\Utilities\AudioMaker;
use App\Utilities\Constants;
use App\Utilities\GoodsFlow;
use App\Utilities\Lock;
use App\Utilities\PrivilegeUtilities;
use App\Utilities\RegularPallet;
use App\Utilities\RequiredListsCheck;
use App\Utilities\WarehouseLabel;
use App\Utilities\XlsxGenerator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class  NewItemScanProcessController extends Controller
{
    private string $rz = '';

    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_SCAN_NEW_ITEMS]);
    }

    public function GoToScan(GoToScanRequest $request): Factory|View|Application
    {
        $this->makeScanAudios();
        $data['scan'] = NewItemScanModel::whereId($request->id)->first();
        return view('newItemScan.scan', $data);
    }

    public function createWarehouseLabelForPalletInScan(Request $request, WarehouseLabel $label)
    {
        if ($generated_label = $label->generate($request->text, $request->number)) {
            return response()->json(['warehouse_label_key' => $generated_label->warehouse_key]);
        } else {
            return response()->json(['message' => 'Failed to get warehouse label'], 422);
        }

    }

    public function SNSubmit(SNSubmitRequest $request): JsonResponse
    {
        if ($this->scanIsClosed($request->scanId)) {
            return response()->json(['message' => 'This scan is closed'], 422);
        }

        if ($this->newItemScanIsLocked()) {
            return response()->json(['message' => 'New item scan is LOCKED (Required list editing in progress)'], 422);
        }

        $sn = new ScannedSn();
        $sn->sn = $request->sn;
        $sn->user_id = Auth::user()->id;
        $sn->new_item_scan_id = $request->scanId;

        $this->setRzFromRequest($request);

        if (empty($this->rz)) {
            if (Setting::whereName(Constants::SETTINGS_RAZER_API_STATUS)->first()->value) {
                return response()->json(['message' => 'RAZER API not available'], 422); // needs to be updated if RAZER will give us API
            }
        }

        if (empty($this->rz)) {
            $this->getRzFromFile($request);
        }

        if (empty($this->rz)) {
            return response()->json(['askingForRz' => true, 'allInputs' => $request->all()]);
        }

        $sn->rz = $this->rz;

        $sn->direct_scarp = $request->putToDirectScrap;

        if ($this->notInBatteryScrapList($sn->rz)) {
            return response()->json(['message' => 'You need to add ' . $sn->rz . ' to "Battery/Scrap" list'], 422);
        }


        $sn->direct_scarp_result = $this->setDirectScrapResult($sn->direct_scarp, $sn->rz);

        $sn->check_required = Setting::whereName(Constants::SETTINGS_CHECK_REQUIRED_LIST)->first()->value;

        $sn->save();

        if ($sn->direct_scarp_result) {

            $battery = $this->isBattery($sn->rz);
            $pallet = new \App\Utilities\ScrapPallet();
            $pallet->addToRazerScrapPallet($sn, $battery);

            $sn->result = $battery ? 'Scrap battery' : 'Scrap';
            $sn->save();

            return response()->json(['directScrap' => true, 'battery' => $battery]);
        }

        if ($sn->check_required) {
            Lock::db(['required_list_items', 'required_list_pallets', 'required_list_pallet_items']);

            $requiredListsCheck = new RequiredListsCheck($sn->rz);
            $listCheck = $requiredListsCheck->answer();

            if ($listCheck['need']) {
                $requiredPallet = new \App\Utilities\RequiredPallet();
                $requiredPallet->addToRequiredPallet($sn, $listCheck['idOfList']);
                $requiredPallet->subtract($listCheck['idOfList'], $listCheck['scannedRz']);

                $sn->result = RequiredListPallet::whereClosed(false)->whereRequiredListId($listCheck['idOfList'])->first()->text;
                $sn->save();

                Lock::remove();
                return response()->json($listCheck);
            } else {
                Lock::remove();
                $regularPallet = new RegularPallet();
                $regularPallet->addToRegularPallet($sn);

                $sn->result = $sn->regularItem->newItemScanPallet->text . "_" . $sn->regularItem->newItemScanPallet->pallet_number;
                $sn->save();

                return response()->json(['message' => 'Regular']);
            }

        } else {
            $regularPallet = new RegularPallet();
            $regularPallet->addToRegularPallet($sn);

            $sn->result = $sn->regularItem->newItemScanPallet->text . "_" . $sn->regularItem->newItemScanPallet->pallet_number;
            $sn->save();

            return response()->json(['message' => 'Regular']);
        }

    }

    public function SNEanSubmit(SNEanSubmitRequest $request)
    {

        if ($this->newItemScanIsLocked()) {
            return response()->json(['message' => 'New item scan is LOCKED (Required list editing in progress)'], 422);
        }

        if ($this->scanIsClosed($request->scanId)) {
            return response()->json(['message' => 'This scan is closed'], 422);
        }

        $sn = new ScannedSn();
        $sn->sn = $request->sn;
        $sn->user_id = Auth::user()->id;
        $sn->new_item_scan_id = $request->scanId;
        $sn->rz = RazerBatteryGoodBadModel::whereEan($request->ean)->first()->rz;
        $sn->direct_scarp = $request->putToDirectScrap;
        $sn->check_required = Setting::whereName(Constants::SETTINGS_CHECK_REQUIRED_LIST)->first()->value;
        $sn->direct_scarp_result = $this->setDirectScrapResult($sn->direct_scarp, $sn->rz);
        $sn->save();

        if ($sn->direct_scarp_result) {

            $battery = $this->isBattery($sn->rz);
            $pallet = new \App\Utilities\ScrapPallet();
            $pallet->addToRazerScrapPallet($sn, $battery);

            $sn->result = $battery ? 'Scrap battery' : 'Scrap';
            $sn->save();

            return response()->json(['directScrap' => true, 'battery' => $battery]);
        }

        if ($sn->check_required) {

            Lock::db(['required_list_items', 'required_list_pallets', 'required_list_pallet_items']);

            $requiredListsCheck = new RequiredListsCheck($sn->rz);
            $listCheck = $requiredListsCheck->answer();

            if ($listCheck['need']) {
                $requiredPallet = new \App\Utilities\RequiredPallet();
                $requiredPallet->addToRequiredPallet($sn, $listCheck['idOfList']);
                $requiredPallet->subtract($listCheck['idOfList'], $listCheck['scannedRz']);

                $sn->result = RequiredList::whereId($listCheck['idOfList'])->first()->name;
                $sn->save();

                Lock::remove();
                return response()->json($listCheck);
            } else {
                Lock::remove();
                $regularPallet = new RegularPallet();
                $regularPallet->addToRegularPallet($sn);

                $sn->result = $sn->regularItem->newItemScanPallet->text . "_" . $sn->regularItem->newItemScanPallet->pallet_number;
                $sn->save();

                return response()->json(['message' => 'Regular']);
            }


        } else {
            $regularPallet = new RegularPallet();
            $regularPallet->addToRegularPallet($sn);

            $sn->result = $sn->regularItem->newItemScanPallet->text . "_" . $sn->regularItem->newItemScanPallet->pallet_number;
            $sn->save();

            return response()->json(['message' => 'Regular']);
        }

    }

    public function scannedSnEdit(ScannedSnEditRequest $request): JsonResponse
    {
        $scanned = ScannedSn::whereId($request->id)->first();
        $scanned->sn = $request->sn;

        if ($scanned->scrapItem()->exists()) {
            $scanned->scrapItem->sn = $request->sn;
        }

        if ($scanned->requiredItem()->exists()) {
            $scanned->requiredItem->sn = $request->sn;
        }

        if ($scanned->regularItem()->exists()) {
            $scanned->regularItem->sn = $request->sn;
        }


        if ($scanned->push()) {
            return response()->json(['message' => 'Serial number updated']);
        } else {
            return response()->json(['message' => 'Failed to update serial number'], 422);
        }
    }

    public function deleteScannedProduct(DeleteScannedProductRequest $request): JsonResponse
    {
        $scanned = ScannedSn::whereId($request->id)->first();

        if (NewItemScanModel::whereId($scanned->new_item_scan_id)->first()->status !== Constants::SCAN_STATUS_NEW) {
            return response()->json(['message' => "You can't delete item now (scan status must be 'New')"], 422);
        }


        if ($scanned->scrapItem()->exists()) {
            $scanned->scrapItem->delete();
            $scanned->delete();
            return response()->json(['message' => 'Item deleted from Scanned SNs and Scrap Items']);
        }

        if ($scanned->requiredItem()->exists()) {

            if (empty($scanned->requiredItem->requiredList)) {
                return response()->json(['message' => "This item can't be deleted, (Required List is removed)"], 422);
            }

            Lock::db('required_list_items');
            $requiredPallet = new \App\Utilities\RequiredPallet();
            $requiredPallet->add($scanned->requiredItem->requiredList->id, $request->rz);

            $scanned->requiredItem->delete();
            $scanned->delete();
            Lock::remove();

            return response()->json(['message' => 'Item deleted from Scanned SNs and Required pallet']);
        }

        if ($scanned->regularItem()->exists()) {
            $scanned->regularItem->delete();
            $scanned->delete();
            return response()->json(['message' => 'Item deleted from Scanned SNs and Regular pallet']);
        }

        return response()->json(["message" => "Item not deleted"], 422);

    }

    public function deleteScannedItems(Request $request): JsonResponse
    {

        $submited = count($request->items);
        $deleted = 0;

        foreach ($request->items as $item) {

            $scanned = ScannedSn::whereId($item['id'])->first();

            if ($scanned->scrapItem()->exists()) {
                $scanned->scrapItem->delete();
                $scanned->delete();
                $deleted++;
            }

            if ($scanned->requiredItem()->exists()) {

                if (empty($scanned->requiredItem->requiredList)) {
                    continue;
                }

                Lock::db('required_list_items');
                $requiredPallet = new \App\Utilities\RequiredPallet();
                $requiredPallet->add($scanned->requiredItem->requiredList->id, $item['rz']);

                $scanned->requiredItem->delete();
                $scanned->delete();
                $deleted++;
                Lock::remove();

            }

            if ($scanned->regularItem()->exists()) {
                $scanned->regularItem->delete();
                $scanned->delete();
                $deleted++;
            }

        }

        return response()->json(['message' => $deleted . ' items deleted out of ' . $submited . ' submitted']);

    }


    public function multipleNoSnSubmit(MultipleNoSnSubmitRequest $request): JsonResponse
    {
        $responseArray = [
            'toRequired' => false,
            'toGood' => false,
            'toGoodCount' => 0,
            'toRequiredList' => [],
            'directScrap' => $this->setDirectScrapResult($request->putToDirectScrap, $request->rz),
            'battery' => $this->isBattery($request->rz),
            'count' => $request->snCount,
            'product' => $request->rz,
        ];

        if ($this->scanIsClosed($request->scanId)) {
            return response()->json(['message' => 'This scan is closed'], 422);
        }

        if ($this->newItemScanIsLocked()) {
            return response()->json(['message' => 'New item scan is LOCKED (Required list editing in progress)'], 422);
        }

        if ($this->notInBatteryScrapList($request->rz)) {
            return response()->json(['message' => 'You need to add ' . $request->rz . ' to "Battery/Scrap" list'], 422);
        }

        $checkRequired = Setting::whereName(Constants::SETTINGS_CHECK_REQUIRED_LIST)->first()->value;

        if ($checkRequired) {
            Lock::db(['required_list_items', 'required_list_pallets', 'required_list_pallet_items']);
        }

        for ($i = 1; $i <= $request->snCount; $i++) {
            $sn = new ScannedSn();
            $sn->sn = $request->sn;
            $sn->rz = $request->rz;
            $sn->user_id = Auth::user()->id;
            $sn->new_item_scan_id = $request->scanId;
            $sn->direct_scarp = $request->putToDirectScrap;
            $sn->direct_scarp_result = $responseArray['directScrap'];
            $sn->check_required = $checkRequired;
            $sn->save();

            if ($sn->direct_scarp_result) {
                $battery = $responseArray['battery'];
                $pallet = new \App\Utilities\ScrapPallet();
                $pallet->addToRazerScrapPallet($sn, $battery);

                $sn->result = $battery ? 'Scrap battery' : 'Scrap';
                $sn->save();

            } elseif ($sn->check_required) {

                $requiredListsCheck = new RequiredListsCheck($sn->rz);
                $listCheck = $requiredListsCheck->answer();

                if ($listCheck['need']) {

                    $list = RequiredList::whereId($listCheck['idOfList'])->first();

                    $responseArray['toRequired'] = true;
                    $responseArray['toRequiredList'][$listCheck['idOfList']]['id'] = !empty($responseArray['toRequiredList'][$listCheck['idOfList']]['id']) ? $responseArray['toRequiredList'][$listCheck['idOfList']]['id'] : $list->id;
                    $responseArray['toRequiredList'][$listCheck['idOfList']]['name'] = !empty($responseArray['toRequiredList'][$listCheck['idOfList']]['name']) ? $responseArray['toRequiredList'][$listCheck['idOfList']]['name'] : $list->name;
                    $responseArray['toRequiredList'][$listCheck['idOfList']]['count'] = empty($responseArray['toRequiredList'][$listCheck['idOfList']]['count']) ? 1 : $responseArray['toRequiredList'][$listCheck['idOfList']]['count'] + 1;

                    $requiredPallet = new \App\Utilities\RequiredPallet();
                    $requiredPallet->addToRequiredPallet($sn, $listCheck['idOfList']);
                    $requiredPallet->subtract($listCheck['idOfList'], $listCheck['scannedRz']);

                    $sn->result = RequiredList::whereId($listCheck['idOfList'])->first()->name;
                    $sn->save();
                } else {

                    $responseArray['toGood'] = true;
                    ++$responseArray['toGoodCount'];

                    $regularPallet = new RegularPallet();
                    $regularPallet->addToRegularPallet($sn);

                    $sn->result = $sn->regularItem->newItemScanPallet->text . "_" . $sn->regularItem->newItemScanPallet->pallet_number;
                    $sn->save();

                }
            } else {
                $responseArray['toGood'] = true;
                ++$responseArray['toGoodCount'];

                $regularPallet = new RegularPallet();
                $regularPallet->addToRegularPallet($sn);

                $sn->result = $sn->regularItem->newItemScanPallet->text . "_" . $sn->regularItem->newItemScanPallet->pallet_number;
                $sn->save();
            }
        }

        Lock::remove();

        return response()->json($responseArray);

    }

    public function getRazerAPIStatus(): JsonResponse
    {
        return response()->json(Setting::whereName(Constants::SETTINGS_RAZER_API_STATUS)->first(['value'])->toArray());
    }

    public function getRequiredListUsageStatus(): JsonResponse
    {
        return response()->json(Setting::whereName(Constants::SETTINGS_CHECK_REQUIRED_LIST)->first(['value'])->toArray());
    }

    public function toggleRazerAPIStatus()
    {
        Lock::db('settings');
        $item = Setting::whereName(Constants::SETTINGS_RAZER_API_STATUS)->first();
        $item->value = !$item->value;
        if ($item->save()) {
            Lock::remove();
            broadcast(new RazerAPIStatusChange());
        } else {
            Lock::remove();
            return response()->json(["message" => 'Failed to change RAZER API status'], 422);
        }
    }

    public function toggleRequiredListUsageStatus()
    {
        Lock::db('settings');
        $item = Setting::whereName(Constants::SETTINGS_CHECK_REQUIRED_LIST)->first();
        $item->value = !$item->value;
        if ($item->save()) {
            Lock::remove();
            broadcast(new CheckRequiredListStatusChange());
        } else {
            Lock::remove();
            return response()->json(["message" => 'Failed to change required list status'], 422);
        }
    }


    /**
     * Creates audio files for required lists.
     *If you need to overwrite existing file, provide required list ID in $needToOverwrite array
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createRequiredListAudioFiles(Request $request): JsonResponse
    {
        $lists = RequiredList::whereActive(true)->get();
        foreach ($lists as $list) {
            if (in_array($list->id, $request->needToOverwrite)) {
                (new AudioMaker($list->audioText, Auth::user()->userVoice->voice_name, Auth::user()->userVoice->pitch, Auth::user()->id, Constants::STRING_FOR_REQUIRED_LIST_AUDIO_NAME . $list->id))->make();
            } else {
                (new AudioMaker($list->audioText, Auth::user()->userVoice->voice_name, Auth::user()->userVoice->pitch, Auth::user()->id, Constants::STRING_FOR_REQUIRED_LIST_AUDIO_NAME . $list->id))->makeIfNotExists();
            }

        }
        return response()->json(['message' => 'Required list audio files are ready']);
    }

    public function generateXlsx(GenerateXlsxRequest $request)
    {
        Lock::db('new_item_scans');
        $scanData = NewItemScanModel::whereId($request->scanId)->with([
            'newItemScanPallets',
            'newItemScanPallets.newItemScanPalletItems',
            'requiredListPalletItems',
            'requiredListPalletItems.requiredListPallet',
            'scraps',
            'scraps.scrapPallet',
        ])->first()->toArray();

        if ($scanData['status'] !== 'confirmed') {
            return response()->json(['message' => "File can be generated only if scan status is 'confirmed'"], 422);
        }

        if ($scanData['export_file_path']) {
            return response()->json(['message' => "File already created"], 422);
        }

        $generator = new XlsxGenerator($scanData);

        $spreadSheet = new Spreadsheet();
        $spreadSheet->getDefaultStyle()->getFont()->setName('Consolas');
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setTitle("Pallets");
        $sheet->fromArray($generator->getPalletsHeaders(), "", 'A1');
        $spreadSheet->getSheetByName('Pallets')->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->fromArray($generator->getPallets(), "", 'A2');

        $spreadSheet->getSheetByName('Pallets')->getColumnDimensionByColumn(1)->setWidth(20);
        $spreadSheet->getSheetByName('Pallets')->getColumnDimensionByColumn(2)->setWidth(20);
        $spreadSheet->getSheetByName('Pallets')->getColumnDimensionByColumn(3)->setWidth(20);
        $spreadSheet->getSheetByName('Pallets')->getColumnDimensionByColumn(4)->setWidth(10);
        $spreadSheet->getSheetByName('Pallets')->getColumnDimensionByColumn(5)->setWidth(20);

        $palCount = 1;
        $sheet->freezePane('A2');
        $sheet->getCell('F2')->setValue('1 PL');
        foreach ($generator->getBorderPlaces() as $k => $place) {
            if (!empty($generator->getBorderPlaces()[$k + 1])) {


                if (!empty($generator->getBorderPlaces()[$k + 2])) {
                    $sheet->getCell('F' . $generator->getBorderPlaces()[$k + 1] + 1)->setValue(++$palCount . ' PL');
                }

                $spreadSheet
                    ->getSheetByName('Pallets')
                    ->getStyle('A' . $place . ':H' . $generator->getBorderPlaces()[$k + 1])
                    ->getBorders()
                    ->getBottom()
                    ->setBorderStyle(Border::BORDER_MEDIUM)
                    ->setColor(new Color());
            } else {
                break;
            }
        }

        $sheet2 = $spreadSheet->createSheet();
        $sheet2->freezePane('A2');
        $sheet2->setTitle("Scraps");
        $sheet2->fromArray($generator->getScraps(), "", 'A2');
        $sheet2->fromArray($generator->getScrapsHeaders(), "", 'A1');
        $spreadSheet->getSheetByName('Scraps')->getStyle('A1:E1')->getFont()->setBold(true);
        $spreadSheet->getSheetByName('Scraps')->getColumnDimensionByColumn(1)->setWidth(20);
        $spreadSheet->getSheetByName('Scraps')->getColumnDimensionByColumn(2)->setWidth(20);
        $spreadSheet->getSheetByName('Scraps')->getColumnDimensionByColumn(3)->setWidth(20);
        $spreadSheet->getSheetByName('Scraps')->getColumnDimensionByColumn(4)->setWidth(10);

        $sheet3 = $spreadSheet->createSheet();
        $sheet3->freezePane('A2');
        $sheet3->setTitle("Required");
        $sheet3->fromArray($generator->getRequired(), "", 'A2');
        $sheet3->fromArray($generator->getRequiredHeaders(), "", 'A1');
        $spreadSheet->getSheetByName('Required')->getStyle('A1:E1')->getFont()->setBold(true);
        $spreadSheet->getSheetByName('Required')->getColumnDimensionByColumn(1)->setWidth(20);
        $spreadSheet->getSheetByName('Required')->getColumnDimensionByColumn(2)->setWidth(20);
        $spreadSheet->getSheetByName('Required')->getColumnDimensionByColumn(3)->setWidth(20);
        $spreadSheet->getSheetByName('Required')->getColumnDimensionByColumn(4)->setWidth(30);


        $sheet4 = $spreadSheet->createSheet();
        $sheet4->freezePane('A2');
        $sheet4->setTitle("Differences");
        $sheet4->fromArray($generator->getDifferences(), "", 'A2');
        $sheet4->fromArray($generator->getDifferencesHeaders(), "", 'A1');
        $spreadSheet->getSheetByName('Differences')->getStyle('A1:E1')->getFont()->setBold(true);
        $spreadSheet->getSheetByName('Differences')->getColumnDimensionByColumn(1)->setWidth(120);
        $spreadSheet->getSheetByName('Differences')->getColumnDimensionByColumn(2)->setWidth(20);
        $spreadSheet->getSheetByName('Differences')->getColumnDimensionByColumn(3)->setWidth(10);
        $spreadSheet->getSheetByName('Differences')->getColumnDimensionByColumn(4)->setWidth(10);
        $spreadSheet->getSheetByName('Differences')->getColumnDimensionByColumn(5)->setWidth(10);


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadSheet);
        $writer->setPreCalculateFormulas(false);
        $writer->setOffice2003Compatibility(true);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($scanData['rma'] . ".xlsx") . '"');
        ob_start();
        $writer->save("php://output");
        $content = ob_get_contents();
        ob_end_clean();
        Storage::put($path = "exportScanFiles/" . Str::random(25) . ".xlsx", $content);

        NewItemScanModel::whereId($scanData['id'])->update(['export_file_path' => $path, 'export_file_name' => $scanData['sender'] . "_" . $scanData['rma'] . ".xlsx"]);

        Lock::remove();

        return response()->json(['message' => 'File generated']);

    }

    public function deleteXlsxFile(GenerateXlsxRequest $request): JsonResponse
    {
        $scan = NewItemScanModel::whereId($request->scanId)->first();
        Storage::delete($scan->export_file_path);
        $scan->export_file_path = null;
        $scan->export_file_name = null;
        $scan->save();
        return response()->json(['message' => 'File deleted']);
    }

    public function downloadXlsxFile($id)
    {
        $scan = NewItemScanModel::whereId($id)->first();

        return response()->download(storage_path('app/' . $scan->export_file_path), $scan->export_file_name, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'inline; filename="' . $scan->export_file_name . '"'
        ]);
    }

    private function makeScanAudios()
    {
        (new AudioMaker(Setting::whereName(Constants::AUDIO_BATTERY_SCRAP_SETTINGS)->first()->value, Auth::user()->userVoice->voice_name, Auth::user()->userVoice->pitch, Auth::user()->id, Constants::AUDIO_BATTERY_SCRAP_SETTINGS))->makeIfNotExists();
        (new AudioMaker(Setting::whereName(Constants::AUDIO_SCRAP_SETTINGS)->first()->value, Auth::user()->userVoice->voice_name, Auth::user()->userVoice->pitch, Auth::user()->id, Constants::AUDIO_SCRAP_SETTINGS))->makeIfNotExists();
        (new AudioMaker(Setting::whereName(Constants::AUDIO_ERROR_SETTINGS)->first()->value, Auth::user()->userVoice->voice_name, Auth::user()->userVoice->pitch, Auth::user()->id, Constants::AUDIO_ERROR_SETTINGS))->makeIfNotExists();
    }

    private function setDirectScrapResult($directScrapFromInput, $rz): bool
    {
        if ($directScrapFromInput) {
            return true;
        }

        if (RazerBatteryGoodBadModel::whereRz($rz)->first()->scrap) {
            return true;
        }

        return false;

    }


    public function getNeedToScanSNs(GetNeedToScanSNsRequest $request): JsonResponse
    {
        return response()->json(ItemToScan::whereNewItemScanId($request->id)->get()->toArray());
    }

    public function getScannedSNs(GetNeedToScanSNsRequest $request): JsonResponse
    {
        return response()->json(ScannedSn::whereNewItemScanId($request->id)->with('user')->orderBy('created_at', 'DESC')->get(['id', 'sn', 'rz', 'result', 'user_id'])->toArray());
    }

    public function getRegularPallets(GetNeedToScanSNsRequest $request): JsonResponse
    {
        return response()->json(NewItemScanPallet::whereNewItemScanId($request->id)->orderBy('created_at', 'DESC')->get()->toArray());
    }

    public function makeScanDone(MakeScanDoneRequest $request): JsonResponse
    {
        \App\Models\NewItemScan\NewItemScan::whereId($request->scanId)->first()->update(['status' => Constants::SCAN_STATUS_DONE]);
        return response()->json(['message' => 'Scan Status changed']);
    }

    public function makeScanNew(MakeScanDoneRequest $request): JsonResponse
    {
        \App\Models\NewItemScan\NewItemScan::whereId($request->scanId)->first()->update(['status' => Constants::SCAN_STATUS_NEW]);
        return response()->json(['message' => 'Scan Status changed']);
    }

    public function makeScanConfirmed(MakeScanDoneRequest $request): JsonResponse
    {
        $lastNumber = NewItemScanPallet::whereMonth('created_at', Carbon::now()->month)->whereNotNull('box_number')->count();
        $pallets = NewItemScanPallet::whereNewItemScanId($request->scanId)->whereNull('box_number')->get();

        NewItemScanModel::whereId($request->scanId)->first()->update(['status' => Constants::SCAN_STATUS_CONFIRMED]);

        foreach ($pallets as $pallet) {
            $pallet->box_number = $lastNumber + 1;
            $pallet->save();
            $lastNumber = $lastNumber + 1;
        }
        return response()->json(['message' => 'Scan Status changed']);
    }

    public function getScanData(MakeScanDoneRequest $request): JsonResponse
    {
        return response()->json(NewItemScanModel::whereId($request->scanId)->first()->toArray());
    }

    public function checkIfFileExists(MakeScanDoneRequest $request): JsonResponse
    {
        return response()->json((bool)NewItemScanModel::whereId($request->scanId)->first()->export_file_path);
    }

    public function getDifferences(GetNeedToScanSNsRequest $request): JsonResponse
    {
        $calculated = [];
        $required = ItemToScan::whereNewItemScanId($request->id)->get(['product_checked'])->toArray();
        $scanned = NewItemScanPalletItem::whereNewItemScanId($request->id)->get(['rz'])->toArray();
        $scannedScrap = ScrapItem::whereNewItemScanId($request->id)->get(['rz'])->toArray();
        $scannedRequiredItems = RequiredListPalletItem::whereNewItemScanId($request->id)->get(['rz'])->toArray();

        foreach ($required as $item) {

            $item = $item['product_checked'];

            if (array_key_exists($item, $calculated)) {
                $calculated[$item]['required'] = $calculated[$item]['required'] + 1;
            } else {
                $calculated[$item]['required'] = 1;
                $calculated[$item]['scanned'] = 0;
                $calculated[$item]['rz'] = $item;
            }

        }

        foreach ($scanned as $item) {

            $item = $item['rz'];

            if (array_key_exists($item, $calculated)) {
                $calculated[$item]['scanned'] = $calculated[$item]['scanned'] + 1;
            } else {
                $calculated[$item]['scanned'] = 1;
                $calculated[$item]['required'] = 0;
                $calculated[$item]['rz'] = $item;
            }

        }

        foreach ($scannedScrap as $item) {

            $item = $item['rz'];

            if (array_key_exists($item, $calculated)) {
                $calculated[$item]['scanned'] = $calculated[$item]['scanned'] + 1;
            } else {
                $calculated[$item]['name'] = '';
                $calculated[$item]['rz'] = $item;
                $calculated[$item]['required'] = 0;
                $calculated[$item]['scanned'] = 1;
            }

        }

        foreach ($scannedRequiredItems as $item) {

            $item = $item['rz'];

            if (array_key_exists($item, $calculated)) {
                $calculated[$item]['scanned'] = $calculated[$item]['scanned'] + 1;
            } else {
                $calculated[$item]['name'] = '';
                $calculated[$item]['rz'] = $item;
                $calculated[$item]['required'] = 0;
                $calculated[$item]['scanned'] = 1;
            }

        }

        $names = GoodsFlow::getAllRazerArtikelsFromGoodsFlow();

        foreach ($calculated as $key => $item) {
            $calculated[$key]['difference'] = $calculated[$key]['scanned'] - $calculated[$key]['required'];
            $calculated[$key]['name'] = !empty($names[$key]) ? $names[$key] : 'Item not Found in GoodsFlow database';
        }

        return response()->json($calculated);
    }

    public function closePallet(ClosePalletRequest $request): JsonResponse
    {
        Lock::db('new_item_scan_pallets');
        $pallet = NewItemScanPallet::whereClosed(false)->whereNewItemScanId($request->id)->first();
        if (!$pallet) {
            return response()->json(['message' => "All pallets are closed"], 422);
        }
        if ($pallet->exists()) {
            $pallet->closed = true;
            $pallet->save();
            Lock::remove();
            return response()->json(['message' => 'Pallet closed, New one will be created automatically when you scan new S/N']);
        }
        Lock::remove();
        return response()->json(['message' => "Can't find pallet to close"], 422);
    }

    public function rewritePalletNumbers(ClosePalletRequest $request): JsonResponse
    {
        Lock::db('new_item_scan_pallets');
        $pallets = NewItemScanPallet::whereNewItemScanId($request->id)->orderBy('created_at', 'DESC')->get();
        $biggestId = $pallets->count();
        foreach ($pallets as $pallet) {
            $pallet->pallet_number = $biggestId;
            $pallet->save();
            foreach ($pallet->newItemScanPalletItems as $newItemScanPalletItem) {
                $newItemScanPalletItem->scannedSn->update(['result' => $pallet->text . '_' . $pallet->pallet_number]);
            }
            $biggestId--;
        }
        Lock::remove();
        return response()->json(['message' => 'Pallets renumbered']);
    }

    public function openPallet(OpenPalletRequest $request): JsonResponse
    {
        Lock::db('new_item_scan_pallets');
        if (NewItemScanPallet::whereNewItemScanId($request->scanId)->whereClosed(false)->exists()) {
            return response()->json(['message' => "Only one pallet can be opened"], 422);
        } else {
            NewItemScanPallet::whereId($request->palletId)->update(['closed' => false]);
            Lock::remove();
            return response()->json(['message' => 'Pallet opened']);
        }
    }

    public function deletePallet(OpenPalletRequest $request): JsonResponse
    {
        Lock::db('new_item_scan_pallets');
        $pallet = NewItemScanPallet::whereId($request->palletId)->first();

        if ($pallet->box_number !== null) {
            return response()->json(['message' => "This pallet can't be deleted because it was confirmed"], 422);
        }

        if ($pallet->newItemScanPalletItems->count() > 0) {
            Lock::remove();
            return response()->json(['message' => "You can delete only empty pallets"], 422);
        } else {
            $pallet->delete();
            Lock::remove();
            return response()->json(['message' => 'Pallet deleted']);
        }
    }

    public function getResults(GetNeedToScanSNsRequest $request): JsonResponse
    {
        return response()->json(ScannedSn::whereNewItemScanId($request->id)->select('result')->distinct()->get()->toArray());
    }

    private function getRzFromFile($request)
    {
        $item = ItemToScan::whereSerialNumber($request->sn)->whereNewItemScanId($request->scanId)->get();
        if ($item->count() === 1) {
            $this->rz = $item[0]->product_checked;
        }
    }

    private function setRzFromRequest($request)
    {
        if ($request->rz) {
            $this->rz = $request->rz;
        }
    }

    public function searchProductIdForScan(RAZERBatteryGoodBadEditSearchRequest $request): JsonResponse
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

    private function isBattery($rz): bool
    {
        return (bool)RazerBatteryGoodBadModel::whereRz($rz)->first()->battery;
    }

    private function notInBatteryScrapList(string $rz): bool
    {
        return !RazerBatteryGoodBadModel::whereRz($rz)->exists();
    }

    private function newItemScanIsLocked(): bool
    {
        if (Setting::whereName(Constants::SETTINGS_CHECK_REQUIRED_LIST)->first()->value) {
            if (Setting::whereName(Constants::NEW_ITEM_SCAN_LOCK_STATUS)->first()->value) {
                return true;
            }
        }
        return false;
    }

    private function scanIsClosed($scanId): bool
    {
        $scanStatus = NewItemScanModel::whereId($scanId)->first()->status;
        if ($scanStatus != Constants::SCAN_STATUS_NEW) {
            return true;
        } else {
            return false;
        }

    }

}

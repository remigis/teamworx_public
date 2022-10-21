<?php

namespace App\Http\Controllers;

use App\Events\NewItemScanListChange;
use App\Events\NewScanFileUpload;
use App\Http\Requests\CreateNewEmptyItemScanRequest;
use App\Http\Requests\CreateNewItemScanRequest;
use App\Models\NewItemScan\NewItemScan as NewItemScanModel;
use App\Models\NewItemScan\NewItemScan as NewScan;
use App\Utilities\Constants;
use App\Utilities\NewItemScanGenerator;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

class NewItemScan extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_CREATE_NEW_ITEM_SCAN]);
    }

    public function newItemScan(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('newItemScan.createScan');
    }

    /**
     * @throws Exception
     */
    public function createNewItemScan(CreateNewItemScanRequest $request, NewScan $scan): JsonResponse
    {
        $scan->name = $request->sender . "_" . $request->rma;
        $scan->rma = $request->rma;
        $scan->sender = $request->sender;
        $scan->uploaded_file_path = $this->storeFile($request->file('file'));
        $scan->uploaded_file_name = basename($scan->uploaded_file_path);
        $scan->ext = $request->file('file')->extension();
        $broadcast = new NewScanFileUpload('fileUploaded', Auth::user());
        if ($scan->save()) {
            broadcast($broadcast);
        }


        $rows = $this->xlsxRowsToArray($scan->uploaded_file_path);
        broadcast($broadcast->next('fileRed'));

        $newItemScan = new NewItemScanGenerator($rows, $scan->id, $broadcast);

        $newItemScan->generate();

        $scan->show = true;
        if ($scan->save()) {
            broadcast($broadcast->next('scanCreated'));
            broadcast(new NewItemScanListChange());
        }

        return response()->json(['status' => 200, 'message' => 'success']);
    }

    public function createNewEmptyItemScan(CreateNewEmptyItemScanRequest $request, NewScan $scan)
    {
        $scan->name = $request->sender . "_" . $request->rma;
        $scan->rma = $request->rma;
        $scan->show = true;
        $scan->sender = $request->sender;
        if ($scan->save()) {
            broadcast(new NewItemScanListChange());
            return response()->json(['message' => 'New Scan Created']);
        } else {
            return response()->json(['message' => 'Failed to create new scan'], 422);
        }
    }

    private function storeFile($file): bool|string
    {
        return Storage::putFile('uploadScanFiles', $file);
    }

    /**
     * @throws Exception
     */
    private function xlsxRowsToArray($filePath): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setLoadSheetsOnly(['RMA']);
        $reader->setReadEmptyCells(false);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load(Storage::path($filePath));
        $sheet = $spreadsheet->getSheetByName("RMA");


        return array_slice($sheet->toArray(), 5);
    }

    public function getNewScans(Request $request): JsonResponse
    {
        return response()->json(NewItemScanModel::whereShow(true)->whereStatus(Constants::SCAN_STATUS_NEW)->orderBy('created_at', 'DESC')->paginate($request->perPage, ['*'], 'page', $request->pageNumber));
    }

    public function getDoneScans(Request $request): JsonResponse
    {
        return response()->json(NewItemScanModel::whereShow(true)->whereStatus(Constants::SCAN_STATUS_DONE)->orderBy('created_at', 'DESC')->paginate($request->perPage, ['*'], 'page', $request->pageNumber));
    }

    public function getConfirmedScans(Request $request): JsonResponse
    {
        return response()->json(NewItemScanModel::whereShow(true)->whereStatus(Constants::SCAN_STATUS_CONFIRMED)->orderBy('created_at', 'DESC')->paginate($request->perPage, ['*'], 'page', $request->pageNumber));
    }
}

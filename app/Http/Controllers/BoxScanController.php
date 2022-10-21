<?php

namespace App\Http\Controllers;

use App\Events\CloseScan;
use App\Events\CreateScan;
use App\Events\ItemScan;
use App\Http\Requests\BoxScanRequest;
use App\Http\Requests\CloseScanRequest;
use App\Http\Requests\CreateBoxScanRequest;
use App\Http\Requests\GetClosedScansForBoxRequest;
use App\Http\Requests\GetNeedToScanGFIdsRequest;
use App\Http\Requests\GetOpenScanIdForBoxRequest;
use App\Http\Requests\GetScannedGfIdsRequest;
use App\Http\Requests\GfSubmitForScanRequest;
use App\Models\BoxScan\NeedToScanGoodsFlow;
use App\Models\BoxScan\Scan;
use App\Models\BoxScan\ScannedGoodsFlowId;
use App\Models\Flow\Karton;
use App\Models\Flow\KartonArtikel;
use App\Utilities\PrivilegeUtilities;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BoxScanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_USE_BOX_SCAN]);
    }

    public function boxScan(BoxScanRequest $request)
    {
        $data['box'] = Karton::whereId($request->id)->first();
        return view('boxScan.scan', $data);
    }

    public function submitGf(GfSubmitForScanRequest $request)
    {
        $gf = new ScannedGoodsFlowId();
        $gf->user_id = Auth::id();
        $gf->goods_flow_id = $request->gf;
        $gf->box_id = $request->boxId;
        $gf->scan_id = $request->scanId;
        $gf->save();
        $user = $gf->getRelationValue('user')->toArray();
        $goodsflow = $gf->toArray();
        $goodsflow['user'] = $user;
        broadcast(new ItemScan($goodsflow, $request->boxId));

    }

    public function getScannedGfIds(GetScannedGfIdsRequest $request)
    {
        return response()->json(ScannedGoodsFlowId::with('user')->whereScanId($request->scanId)->whereBoxId($request->boxId)->orderBy('created_at', 'DESC')->get()->toArray());
    }

    public function createBoxScan(CreateBoxScanRequest $request)
    {
        DB::raw('LOCK TABLES `scans` WRITE');
        if ($this->thereIsAnOpenScan($request->boxId)) {
            DB::raw('UNLOCK TABLES');
            return response()->json(['message' => 'There is an open scan for this box'], 422);
        } else {
            $boxScan = new Scan();
            $boxScan->box_id = $request->boxId;
            $boxScan->name = Carbon::now()->toDateTimeString();
            $boxScan->user_id = Auth::user()->id;
            if ($boxScan->save()) {
                $this->makeNeedToScanGoodsFlows($boxScan->box_id, $boxScan->id);
                broadcast(new CreateScan($boxScan->toArray(), $request->boxId));
                DB::raw('UNLOCK TABLES');
                return response()->json($boxScan->toArray());
            } else {
                DB::raw('UNLOCK TABLES');
                return response()->json(['message' => 'Failed to create a scan (database save error)'], 422);
            }
        }
    }

    private function thereIsAnOpenScan($boxId)
    {
        return Scan::whereBoxId($boxId)->whereActive(true)->exists();

    }

    public function getOpenScanIdForBox(GetOpenScanIdForBoxRequest $request)
    {
        $scan = Scan::whereBoxId($request->boxId)->whereActive(true)->first();
        if ($scan != null) {
            return $scan->toArray();
        } else {
            return false;
        }
    }

    private function makeNeedToScanGoodsFlows($boxId, $scanId)
    {
        $insert = [];
        $items = KartonArtikel::with('statistiks.user')->whereKartonId($boxId)->get();

        foreach ($items as $item) {
            $insert[] = [
                'box_id' => $boxId,
                'scan_id' => $scanId,
                'tester' => $item->statistiks[0]['user']['name'],
                'zustand' => $item->zustand,
                'seriennummer' => $item->seriennummer,
                'kommentar' => $item->kommentar,
                'time' => $item->statistiks[0]['timestamp'],
                'goodsflow' => $item->gUID,
            ];
        }
        NeedToScanGoodsFlow::insert($insert);
    }

    public function getNeedToScanGFIds(GetNeedToScanGFIdsRequest $request)
    {
        return response()->json(NeedToScanGoodsFlow::whereScanId($request->scanId)->whereBoxId($request->boxId)->get()->toArray());

    }

    public function getClosedScansForBox(GetClosedScansForBoxRequest $request)
    {
        return response()->json(Scan::whereActive(false)->whereBoxId($request->boxId)->orderBy('created_at', 'DESC')->get()->toArray());
    }

    public function closeScan(CloseScanRequest $request)
    {
        $scan = Scan::whereId($request->scanId)->whereActive(true)->firstOrFail();
        $scan->name .= " - " . Carbon::now()->toDateTimeString();
        $scan->active = false;
        if ($scan->save()) {
            broadcast(new CloseScan($scan->toArray(), $request->scanId));
        } else {
            return response()->json(['message' => 'Failed to close this scan (database save error)']);
        }
    }
}

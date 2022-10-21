<?php

namespace App\Http\Controllers;

use App\Events\BoxBuildDirectScanCenterChangeEvent;
use App\Events\BoxBuildDirectScanStatusChangeEvent;
use App\Events\BoxBuildItemChangeEvent;
use App\Http\Requests\BoxBuildSubmitGoodsFlowIdRequest;
use App\Http\Requests\SetDirectScanFulfilmentCenterRequest;
use App\Models\BoxBuildBox;
use App\Models\BoxBuildBoxItem;
use App\Models\BoxBuildRequiredItems;
use App\Models\Flow\Karton;
use App\Models\Flow\KartonArtikel;
use App\Models\Setting;
use App\Models\WarehouseCenter;
use App\Utilities\AudioMaker;
use App\Utilities\BoxBuildItemSorter;
use App\Utilities\Constants;
use App\Utilities\GoodsFlow;
use App\Utilities\Lock;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoxBuildScanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_SCAN_BOX_BUILDS]);
    }

    public function boxBuildScan()
    {
        return view('boxBuild.boxBuildScan');
    }

    public function getOpenBoxBuildBoxes(): JsonResponse
    {
        return response()->json(BoxBuildBox::with(['items' => function ($query) {
            $query->orderBy('created_at', 'DESC');
        }])->whereActive(true)->orderBy('created_at', 'ASC')->get()->toArray());
    }

    public function deleteBoxBuildBoxItem(Request $request)
    {
        Lock::db(['box_build_required_items', 'box_build_box_items']);
        $item = BoxBuildBoxItem::with('boxBuildBox')->whereId($request->id)->first();

        if ($item->condition_not_important) {
            $productCondition = $item->product;
        } else {
            $productCondition = $item->product . "_" . $item->condition;
        }

        if ($item->boxBuildBox->box_build_id !== null) {
            $query = BoxBuildRequiredItems::whereWarehouseCenterId($item->boxBuildBox->warehouse_center_id)
                ->whereBoxBuildId($item->boxBuildBox->box_build_id)
                ->whereProductCondition($productCondition)
                ->first();

            $query->decrement('collected');
            $query->increment('need');
        }

        if ($item->delete()) {
            broadcast(new BoxBuildItemChangeEvent());
            Lock::remove();
            return response()->json(['message' => 'Item delete successful']);
        } else {
            Lock::remove();
            return response()->json(['message' => 'Failed to delete item'], 422);
        }
    }

    public function boxBuildLabel($id)
    {
        $data['vid'] = Constants::BOX_BUILD_LABEL_VID_MIX;

        $box = BoxBuildBox::with('items')->where('id', '=', $id)->firstOrFail();
        $data['boxName'] = $box->name;
        $data['boxId'] = Karton::whereName($box->name)->firstOrFail()->id;

        $data['xlsxText'] = '=HYPERLINK("' . config('app.gf_domain') . '/system/goodsflow/index.php?r=testing/artikel&karton=' . $data['boxId'] . '";"' . $data['boxName'] . '")';

        $data['itemCount'] = $box->items->count();

        $onlyOneVid = (count($vid = array_unique(array_column($box->items->toArray(), 'vid'))) === 1);

        if ($onlyOneVid && $vid[0] !== null) {
            $data['vid'] = $vid[0];
        }

        return view('boxBuild.boxBuildLabel', $data);

    }

    public function closeBoxBuildBox(Request $request)
    {
        $box = BoxBuildBox::whereId($request->id)->with('items')->first();
        GoodsFlow::loginToGoodsFlow();
        $goodsFlowBox = GoodsFlow::createBox($box->warehouseCenter->pallet_id, $box->name);

        KartonArtikel::whereIn('gUID', array_column($box->items->toArray(), 'gf'))->update(['karton_id' => $goodsFlowBox->id, 'palette_id' => $goodsFlowBox->palette_id]);
        $box->update(['active' => false]);
        broadcast(new BoxBuildItemChangeEvent());

        return response()->json(['message' => 'Box closed']);
    }

    public function deleteBoxBuildBox(Request $request)
    {
        Lock::db(['box_build_required_items', 'box_build_box_items']);
        $box = BoxBuildBox::with('items')->where('id', '=', $request->id)->first();

        if (!$box->active) {
            return response()->json(['message' => 'You can not delete closed box'], 422);
        }

        $boxExists = $box->box_build_id !== null;


        foreach ($box->items as $item) {

            if ($item->condition_not_important) {
                $productCondition = $item->product;
            } else {
                $productCondition = $item->product . "_" . $item->condition;
            }

            if ($boxExists) {
                $query = BoxBuildRequiredItems::whereWarehouseCenterId($box->warehouse_center_id)
                    ->whereBoxBuildId($box->box_build_id)
                    ->whereProductCondition($productCondition)
                    ->first();

                $query->decrement('collected');
                $query->increment('need');
            }

            $item->delete();
        }

        $box->delete();
        broadcast(new BoxBuildItemChangeEvent);
        Lock::remove();
        return response()->json(['message' => 'Box delete successful']);

    }

    public function deleteClosedBoxBuildBox(Request $request)
    {

        Lock::db(['box_build_required_items', 'box_build_box_items']);
        $box = BoxBuildBox::with('items')->where('id', '=', $request->id)->first();

        if ($box->active) {
            return response()->json(['message' => 'This box is active You can delete it using scan menu'], 422);
        }

        $boxExists = $box->box_build_id !== null;


        foreach ($box->items as $item) {

            if ($item->condition_not_important) {
                $productCondition = $item->product;
            } else {
                $productCondition = $item->product . "_" . $item->condition;
            }

            if ($boxExists) {
                $query = BoxBuildRequiredItems::whereWarehouseCenterId($box->warehouse_center_id)
                    ->whereBoxBuildId($box->box_build_id)
                    ->whereProductCondition($productCondition)
                    ->first();

                $query->decrement('collected');
                $query->increment('need');
            }

            $item->delete();
        }

        $box->delete();
        broadcast(new BoxBuildItemChangeEvent);
        Lock::remove();
        return response()->json(['message' => 'Box delete successful']);

    }

    public function boxBuildSubmitGoodsFlowId(BoxBuildSubmitGoodsFlowIdRequest $request)
    {
        $sorter = new BoxBuildItemSorter($request->goodsFlow);
        broadcast(new BoxBuildItemChangeEvent());

        $addedTo = $sorter->getResultCenterId();
        return response()->json(['to' => $addedTo]);
    }

    public function prepareBoxBuildAudioFiles()
    {
        $centers = BoxBuildRequiredItems::whereHas('boxBuild', function (Builder $query) {
            $query->where('active', '=', true);
        })->select(['warehouse_center_id'])->distinct()->get()->toArray();

        if (Setting::whereName(Constants::BOX_BUILD_DIRECT_SCAN_STATUS)->first()->value && $directScanCenter = Setting::whereName(Constants::BOX_BUILD_DIRECT_SCAN_CENTER)->first()->value) {
            $centers[] = ['warehouse_center_id' => $directScanCenter];
        }

        foreach ($centers as $center) {
            (new AudioMaker(
                WarehouseCenter::whereId($center['warehouse_center_id'])->first()->audio_text,
                Auth::user()->userVoice->voice_name,
                Auth::user()->userVoice->pitch,
                Auth::user()->id, Constants::STRING_FOR_BOX_BUILD_AUDIO_NAME . $center['warehouse_center_id']))->makeIfNotExists();
        }
        return response()->json(['message' => 'Audio files are ready', 'centers' => array_column($centers, 'warehouse_center_id')]);
    }

    public function getFulfilmentCenters()
    {
        return response()->json(WarehouseCenter::all()->toArray());
    }

    public function getDirectScanFulfilmentCenter()
    {
        if ($center = WarehouseCenter::whereId(Setting::whereName(Constants::BOX_BUILD_DIRECT_SCAN_CENTER)->first()->value)->first()) {
            return response()->json($center->toArray());
        } else {
            return null;
        }

    }

    public function getDirectScanStatus()
    {
        return response()->json((boolean)Setting::whereName(Constants::BOX_BUILD_DIRECT_SCAN_STATUS)->first()->value);
    }

    public function toggleDirectScanStatus()
    {
        if (!$center = Setting::whereName(Constants::BOX_BUILD_DIRECT_SCAN_CENTER)->first()->value) {
            return response()->json(['message' => 'You need to select fulfilment center'], 422);
        }

        if (!WarehouseCenter::whereId($center)->exists()) {
            return response()->json(['message' => 'Fulfilment center do not exist'], 422);
        }

        $setting = Setting::whereName(Constants::BOX_BUILD_DIRECT_SCAN_STATUS)->first();
        $setting->value = !$setting->value;
        if ($setting->save()) {
            broadcast(new BoxBuildDirectScanStatusChangeEvent());
            return response()->json(['message' => 'Direct scan status changed']);
        } else {
            return response()->json(['message' => 'Something went wrong'], 422);
        }

    }

    public function setDirectScanFulfilmentCenter(SetDirectScanFulfilmentCenterRequest $request)
    {
        Setting::whereName(Constants::BOX_BUILD_DIRECT_SCAN_CENTER)->update(['value' => $request->fulfilmentCenterId]);
        broadcast(new BoxBuildDirectScanCenterChangeEvent());
        return response()->json(['message' => 'Fulfilment center selected']);
    }
}

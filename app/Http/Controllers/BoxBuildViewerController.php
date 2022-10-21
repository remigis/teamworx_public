<?php

namespace App\Http\Controllers;

use App\Models\BoxBuild;
use App\Models\BoxBuildBox;
use App\Models\BoxBuildBoxItem;
use App\Models\BoxBuildRequiredItems;
use App\Models\Flow\Palette;
use App\Models\WarehouseCenter;
use App\Utilities\GoodsFlow;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Http\Request;

class BoxBuildViewerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_VIEW_BOX_BUILDS]);
    }

    public function viewBoxItems(Request $request)
    {
        return response()->json(BoxBuildBoxItem::whereBoxBuildBoxId($request->id)->get(['id', 'gf', 'product', 'condition'])->toArray());
    }

    public function getBoxBuildViewerBoxes(Request $request)
    {
        $query = BoxBuildBox::with('items')->where('box_build_id', '=', $request->input('boxBuildId'));

        if ($request->input('string') !== null) {
            $query = $query->where('name', 'LIKE', '%' . $request->input('string') . '%');
        }

        return response()->json($query->orderBy('created_at', 'DESC')->get()->toArray());
    }

    public function directScanBoxes()
    {
        return view('boxBuild.directScanBoxes');
    }

    public function getAllDirectScanBoxes(Request $request)
    {
        $query = BoxBuildBox::with('items')->where('box_build_id', '=', null);

        if ($request->input('string') !== null) {
            $query = $query->where('name', 'LIKE', '%' . $request->input('string') . '%');
        }

        if ($request->input('date') !== null && count($request->input('date')) === 2) {
            $query = $query->whereBetween('created_at', [date_format(date_create($request->input('date')[0]), "Y-m-d H:i:s"), date_format(date_create($request->input('date')[1]), "Y-m-d H:i:s")]);
        }

        return response()->json($query->orderBy('created_at', 'DESC')->paginate($request->rows, ['*'], 'page', $request->page));
    }

    public function boxBuildViewer($id)
    {
        $boxBuild = BoxBuild::whereId($id)->firstOrFail()->toArray();
        return view('boxBuild.boxBuildViewer', $boxBuild);
    }

    public function searchForPalletIds(Request $request)
    {
        $boxes = Palette::where('name', 'LIKE', '%' . $request->input('string') . '%')->orWhere('id', 'LIKE', '%' . $request->input('string') . '%')->orderBy('id', 'DESC')->get(['id', 'name'])->toArray();
        foreach ($boxes as $key => $box) {
            $boxes[$key]['label'] = $box['id'] . ', ' . $box['name'];
        }
        return response()->json($boxes);
    }

    public function getNeedToFindAmountsByManufacturer(Request $request)
    {
        $manufacturers = array_column(BoxBuildRequiredItems::whereBoxBuildId($request->input('boxBuildId'))->select(['manufacturer'])->distinct()->get()->toArray(), 'manufacturer');
        $warehouseCenterIds = array_column(BoxBuildRequiredItems::whereBoxBuildId($request->input('boxBuildId'))->select(['warehouse_center_id'])->distinct()->get()->toArray(), 'warehouse_center_id');
        $warehouseCenters = WarehouseCenter::whereIn('id', $warehouseCenterIds)->get(['id', 'name'])->keyBy('id')->toArray();

        $data = [];
        foreach ($warehouseCenters as $id => $center) {
            $data[$id]['name'] = $center['name'];
            $data[$id]['total'] = BoxBuildRequiredItems::whereWarehouseCenterId($id)->whereBoxBuildId($request->input('boxBuildId'))->sum('need');
            foreach ($manufacturers as $k => $manufacturer) {
                $data[$id]['manufacturers'][$k]['need'] = BoxBuildRequiredItems::whereManufacturer($manufacturer)->whereWarehouseCenterId($id)->whereBoxBuildId($request->input('boxBuildId'))->sum('need');
                $data[$id]['manufacturers'][$k]['manufacturer'] = $manufacturer;
            }
        }
        return response()->json($data);
    }

    public function boxBuildFindItems(Request $request)
    {
        $includedCenters = WarehouseCenter::whereIn('id', array_column(BoxBuildRequiredItems::whereBoxBuildId($request->input('boxBuildId'))->select('warehouse_center_id')->distinct()->get()->toArray(), 'warehouse_center_id'))->get(['id', 'name'])->toArray();
        $pallet = Palette::whereId($request->input('palletId'))->with(['kartons.kartonArtikels.artikel'])->first()->toArray();

        $data = [];

        foreach ($includedCenters as $center) {
            $data[$center['name']] = [];
            $requiredItems = BoxBuildRequiredItems::whereWarehouseCenterId($center['id'])->whereBoxBuildId($request->input('boxBuildId'))->where('need', '>', 0)->get(['product_condition', 'need'])->toArray();
            foreach ($requiredItems as $requiredItem) {

                foreach ($pallet['kartons'] as $karton) {
                    $data[$center['name']][$karton['name']]['items'][$requiredItem['product_condition']] = [];
                    $data[$center['name']][$karton['name']]['items'][$requiredItem['product_condition']]['found'] = 0;
                    $data[$center['name']][$karton['name']]['items'][$requiredItem['product_condition']]['need'] = $requiredItem['need'];
                    $data[$center['name']][$karton['name']]['items'][$requiredItem['product_condition']]['name'] = 'Not found in GoodsFlow database';

                    foreach ($karton['karton_artikels'] as $karton_artikel) {
                        if ($karton_artikel['artikel']['artikelnummer'] . '_' . $karton_artikel['zustand'] === $requiredItem['product_condition']) {
                            ++$data[$center['name']][$karton['name']]['items'][$requiredItem['product_condition']]['found'];
                            $data[$center['name']][$karton['name']]['items'][$requiredItem['product_condition']]['name'] = $karton_artikel['artikel']['name'];
                        } elseif ($karton_artikel['artikel']['artikelnummer'] === $requiredItem['product_condition']) {
                            ++$data[$center['name']][$karton['name']]['items'][$requiredItem['product_condition']]['found'];
                            $data[$center['name']][$karton['name']]['items'][$requiredItem['product_condition']]['name'] = $karton_artikel['artikel']['name'];
                        }
                    }

                }
            }
        }

        /**
         * Delete rows if found is 0
         */
        foreach ($data as $centerName => $box) {
            foreach ($box as $boxName => $item) {
                foreach ($item['items'] as $itemName => $result) {
                    if ($result['found'] === 0) {
                        unset($data[$centerName][$boxName]['items'][$itemName]);
                    }
                }
            }
        }

        /**
         * Delete boxes if 0 items found in it
         */
        foreach ($data as $centerName => $box) {
            foreach ($box as $boxName => $item) {
                if (count($item['items']) === 0) {
                    unset($data[$centerName][$boxName]);
                }
            }
        }

        foreach ($data as $centerName => $boxes) {
            foreach ($boxes as $boxName => $items) {
                $data[$centerName][$boxName]['total'] = 0;
                $data[$centerName][$boxName]['boxName'] = $boxName;
                foreach ($items['items'] as $itemName => $result) {
                    $data[$centerName][$boxName]['items'][$itemName]['product'] = $itemName;
                    $data[$centerName][$boxName]['total'] += min($result['found'], $result['need']);

                }
            }
        }

        return response()->json($data);
    }

    public function boxBuildList()
    {
        return view('boxBuild.boxBuildList');
    }

    public function getBoxBuildRequiredItems($id)
    {
        $list = [];
        $fulfilmentCenters = WarehouseCenter::all(['id', 'name'])->keyBy('id')->toArray();
        $artickels = GoodsFlow::getAllArtikelsFromGoodsFlow();

        $boxBuildItems = BoxBuild::whereId($id)->with(['boxBuildRequiredItems', 'boxBuildRequiredItems.warehouseCenter' => function ($query) {
            $query->select(['id', 'name']);
        }])->firstOrFail()->toArray();

        $includedWarehouseCenters = BoxBuildRequiredItems::whereBoxBuildId($id)->select(['warehouse_center_id'])->distinct()->get()->toArray();


        foreach ($includedWarehouseCenters as $key => $center) {
            $includedWarehouseCenters[$key]['name'] = $fulfilmentCenters[$center['warehouse_center_id']]['name'];
        }

        /*Setting default values when item not needed in one or another fulfilment center*/
        foreach ($boxBuildItems['box_build_required_items'] as $boxBuildItem) {
            foreach ($includedWarehouseCenters as $warehouseCenter) {
                $list[$boxBuildItem['product_condition']][$fulfilmentCenters[$warehouseCenter['warehouse_center_id']]['name']] = 0;
            }
        }

        foreach ($boxBuildItems['box_build_required_items'] as $boxBuildItem) {
            $list[$boxBuildItem['product_condition']]['product_condition'] = $boxBuildItem['product_condition'];
            $list[$boxBuildItem['product_condition']]['manufacturer'] = $boxBuildItem['manufacturer'];
            $list[$boxBuildItem['product_condition']]['vid'] = $boxBuildItem['vid'];

            if (array_key_exists($boxBuildItem['product_condition'], $artickels)) {
                $list[$boxBuildItem['product_condition']]['product_name'] = $artickels[$boxBuildItem['product_condition']]['name'];
            } elseif (array_key_exists($key = substr($boxBuildItem['product_condition'], 0, -2), $artickels)) {
                $list[$boxBuildItem['product_condition']]['product_name'] = $artickels[$key]['name'];
            } else {
                $list[$boxBuildItem['product_condition']]['product_name'] = 'Not found in GoodsFlow database';
            }

            $list[$boxBuildItem['product_condition']][$fulfilmentCenters[$boxBuildItem['warehouse_center_id']]['name']] = $boxBuildItem['need'];
        }

        return response()->json(['list' => $list, 'fulfilmentCenters' => $includedWarehouseCenters]);
    }

}

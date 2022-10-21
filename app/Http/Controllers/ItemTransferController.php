<?php

namespace App\Http\Controllers;

use App\Models\Flow\Karton;
use App\Models\Flow\KartonArtikel;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemTransferController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_USE_ITEM_TRANSFER]);
    }

    public function itemTransfer()
    {
        return view('itemTransfer.itemTransfer');
    }

    public function itemTransferSearchForBox(Request $request)
    {
        $boxes = Karton::where('name', 'LIKE', '%' . $request->input('string') . '%')->orWhere('id', 'LIKE', '%' . $request->input('string') . '%')->orderBy('id', 'DESC')->get(['id', 'name'])->toArray();
        foreach ($boxes as $key => $box) {
            $boxes[$key]['label'] = $box['id'] . ', ' . $box['name'];
        }
        return response()->json($boxes);
    }

    public function transferItems(Request $request)
    {
        $goodsFlows = preg_split('/\r\n|\r|\n/', $request->input('items'));
        $boxId = $request->input('box');
        $data = ['goodsFlows' => $goodsFlows, 'boxId' => $boxId];

        $rules = [
            'goodsFlows' => 'required|array',
            'goodsFlows.*' => 'required|string',
            'boxId' => 'required|exists:mysql2.flow_karton,id',
        ];

        Validator::make($data, $rules)->validate();

        $itemsToTransferFoundInDb = array_column(KartonArtikel::whereIn('gUID', $goodsFlows)->get(['gUID'])->toArray(), 'gUID');

        if (count($itemsToTransferFoundInDb) !== count($goodsFlows)) {
            $notFoundString = '';
            foreach (array_diff($goodsFlows, $itemsToTransferFoundInDb) as $notFound) {
                $notFoundString = $notFoundString . ' ' . $notFound;
            }
            abort(422, 'GoodsFlows not found:' . $notFoundString);
        }

        $box = Karton::whereId($boxId)->firstOrFail();

        KartonArtikel::whereIn('gUID', $goodsFlows)->update(['karton_id' => $boxId, 'palette_id' => $box->palette_id]);

        return response()->json(['message' => 'Items transferred']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteItemFromBoxBuildListRequest;
use App\Models\BoxBuildRequiredItems;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Http\Request;

class BoxBuildEditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_EDIT_BOX_BUILDS]);
    }

    public function deleteItemFromBoxBuildList(DeleteItemFromBoxBuildListRequest $request)
    {
        if (BoxBuildRequiredItems::whereProductCondition($request->item)->whereBoxBuildId($request->boxBuildId)->delete()) {
            return response()->json(['message' => 'Item removed from Box-Build']);
        } else {
            return response()->json(['message' => 'Failed to remove item from Box-Build'], 422);
        }

    }

    public function editBoxBuildItem(Request $request)
    {
        $requiredItems = BoxBuildRequiredItems::whereProductCondition($request->input('product_condition'))->whereBoxBuildId($request->input('boxBuildId'))->with(['warehouseCenter'])->get();
        foreach ($requiredItems as $item) {
            $item->need = $request->input($item->warehouseCenter->name);
            $item->save();
        }
        return response()->json(['message' => "Quantity's updated"]);
    }
}

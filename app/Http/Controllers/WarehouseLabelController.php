<?php

namespace App\Http\Controllers;

use App\Utilities\PrivilegeUtilities;
use App\Utilities\WarehouseLabel;

class WarehouseLabelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_USE_WAREHOUSE]);
    }

    public function warehouseLabel($warehouseLabelKey)
    {
        return view('warehouse.label', WarehouseLabel::print($warehouseLabelKey));
    }
}

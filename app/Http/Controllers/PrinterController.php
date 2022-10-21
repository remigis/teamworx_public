<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateWarehouseKeyAndDoPrintRequest;
use App\Utilities\PrivilegeUtilities;
use App\Utilities\WarehouseLabel;

class PrinterController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_USE_PRINTER]);
    }

    public function printer()
    {
        return view('printer.printer');
    }

    public function createWarehouseKeyAndDoPrint(CreateWarehouseKeyAndDoPrintRequest $request)
    {
        $label = new WarehouseLabel();
        $generatedLabel = $label->generate($request->text);
        return response()->json($generatedLabel->warehouse_key);
    }


}

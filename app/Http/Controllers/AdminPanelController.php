<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetGatePhoneNumberRequest;
use App\Models\Setting;
use App\Utilities\Constants;
use App\Utilities\PrivilegeUtilities;

class AdminPanelController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_ACCESS_ADMIN_PANEL]);
    }

    public function getVirtualPhoneNumber()
    {
        return response()->json(['virtual_phone_number' => config('app.virtual_phone_number')]);
    }

    public function adminPanel()
    {
        return view('admin.adminPanel');
    }

    public function setGatePhoneNumber(SetGatePhoneNumberRequest $request)
    {
        Setting::whereName(Constants::GATE_PHONE_NUMBER)->first()->update(['value' => $request->phone]);
        return response()->json(['message' => 'Phone number updated']);
    }

    public function deleteGatePhoneNumber()
    {
        Setting::whereName(Constants::GATE_PHONE_NUMBER)->first()->update(['value' => null]);
        return response()->json(['message' => 'Phone number deleted']);
    }

    public function getGatePhoneNumber()
    {
        $phoneNumber = Setting::whereName(Constants::GATE_PHONE_NUMBER)->first();

        if ($phoneNumber) {
            return response()->json(['phone' => $phoneNumber->value]);
        } else {
            return response()->json(['phone' => null]);
        }

    }
}

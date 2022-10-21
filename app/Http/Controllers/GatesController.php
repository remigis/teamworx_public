<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Utilities\Constants;
use App\Utilities\PrivilegeUtilities;
use Twilio\Rest\Client;

class GatesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_OPEN_GATES]);
    }

    public function gateOpener()
    {
        return view('gates.gatesOpener');
    }

    public function makeCallToGates()
    {

        $gatePhoneNumber = Setting::whereName(Constants::GATE_PHONE_NUMBER)->first()->value;

        if (!$gatePhoneNumber) {
            return response()->json(['message' => 'Gate phone number not provided'], 422);
        }

        $twilio = new Client(config('app.twilio_account_sid'), config('app.twilio_auth_token'));

        $call = $twilio->calls
            ->create('+370' . $gatePhoneNumber, // to
                config('app.virtual_phone_number'), // from
                [
                    "method" => "GET",
                    "statusCallback" => route('gatesCallback'),
                    "statusCallbackEvent" => ["initiated", "ringing", "answered", "completed"],
                    "statusCallbackMethod" => "POST",
                    "url" => "https://demo.twilio.com/docs/voice.xml"
                ]
            );

        return response()->json(['message' => 'Request sent']);
    }
}

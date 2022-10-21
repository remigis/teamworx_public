<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendRegisterInvitationRequest;
use App\Jobs\SendRegistrationInvitation;
use App\Models\Invitation;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SendRegistrationInvitationController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_REGISTER_NEW_USER, 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_ACCESS_ADMIN_PANEL]);
    }

    public function sendRegisterInvitation(SendRegisterInvitationRequest $request): JsonResponse
    {
        $invitation = new Invitation($request->all());
        $invitation->generateToken();
        $invitation->invited_by = Auth::user()->id;
        $invitation->save();
        dispatch(new SendRegistrationInvitation($invitation))->onQueue('default');

        return response()->json(['message' => 'Invitation sent']);
    }
}

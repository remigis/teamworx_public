<?php

namespace App\Http\Controllers;

use App\Models\Privilege;
use App\Models\User;
use App\Models\UserPrivilege;
use App\Utilities\Lock;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivilegeEditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_ACCESS_ADMIN_PANEL]);
    }

    public function searchUserForPrivilegeEdit(Request $request)
    {
        return User::where('name', 'LIKE', '%' . $request->input('string') . '%')
            ->where('id', '!=', [Auth::user()->id, 1])
            ->get(['id', 'name', 'email'])
            ->toArray();
    }

    public function getPrivilegeSetsForUser(Request $request)
    {
        $ownedPrivileges = UserPrivilege::whereUserId($request->input('userId'))->with('privilege')->get()->toArray();

        $notOwnedPrivileges = Privilege::whereDoesntHave('userPrivileges', fn(Builder $query) => $query->where('user_id', '=', $request->input('userId'))
        )->get()->toArray();

        $allPrivileges = Privilege::all()->toArray();

        return ['ownedPrivileges' => $ownedPrivileges, 'notOwnedPrivileges' => $notOwnedPrivileges, 'allPrivileges' => $allPrivileges];
    }

    public function removePrivilege(Request $request)
    {
        Lock::db(['privileges']);
        if (UserPrivilege::wherePrivilegeId($request->input('privilegeId'))->whereUserId($request->input('userId'))->exists()) {
            UserPrivilege::wherePrivilegeId($request->input('privilegeId'))->whereUserId($request->input('userId'))->delete();
        } else {
            Lock::remove();
            return response()->json(['message' => "This user don't have this privilege"], 422);
        }
        Lock::remove();
        return response()->json(['message' => 'Privilege removed']);
    }

    public function addPrivilege(Request $request)
    {
        Lock::db(['privileges']);
        if (!UserPrivilege::wherePrivilegeId($request->input('privilegeId'))->whereUserId($request->input('userId'))->exists()) {
            UserPrivilege::create(['user_id' => $request->input('userId'), 'privilege_id' => $request->input('privilegeId')]);
        } else {
            Lock::remove();
            return response()->json(['message' => 'This user Already have this privilege'], 422);
        }
        Lock::remove();
        return response()->json(['message' => 'Privilege granted']);

    }
}

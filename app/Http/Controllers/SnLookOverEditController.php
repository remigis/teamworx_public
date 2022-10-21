<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateNewSnCheckGroupRequest;
use App\Http\Requests\CreateNewSnCheckRuleRequest;
use App\Models\SnLookOverGroup;
use App\Models\SnLookOverRule;
use App\Utilities\PrivilegeUtilities;
use App\Utilities\Rule;
use Illuminate\Http\Request;

class SnLookOverEditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'privilege:' . PrivilegeUtilities::PRIVILEGE_TO_EDIT_SN_LOOK_OVER]);
    }

    public function getGroups()
    {
        return response()->json(SnLookOverGroup::orderBy('created_at')->get(['id', 'name', 'active', 'active_for_auto_scan'])->toArray());
    }

    public function createNewSnCheckGroup(CreateNewSnCheckGroupRequest $request)
    {
        SnLookOverGroup::create(['name' => $request->name]);
    }

    public function deleteRule(Request $request)
    {
        SnLookOverRule::whereId($request->rule_id)->first()->delete();
    }

    public function deleteSnCheckGroup($id)
    {
        $group = SnLookOverGroup::whereId($id)->first();
        $group->rules()->delete();
        $group->delete();
    }

    public function disableSnCheckGroup($id)
    {
        SnLookOverGroup::whereId($id)->first()->update(['active' => false]);
    }

    public function activateSnCheckGroup($id)
    {
        SnLookOverGroup::whereId($id)->first()->update(['active' => true]);
    }

    public function getRulesForGroup($id)
    {
        return response()->json(SnLookOverRule::whereSnLookOverGroupId($id)->orderBy('created_at', 'DESC')->get()->toArray());
    }

    public function createNewSnCheckRule(CreateNewSnCheckRuleRequest $request)
    {
        $regex = new Rule($request->rule);

        SnLookOverRule::create(['rule' => $request->rule, 'sn_look_over_group_id' => $request->group_id, 'regex' => $regex->regex]);
    }

}

<?php

namespace App\Utilities;

use App\Models\Privilege;
use App\Models\User;
use App\Models\UserPrivilege;

class PrivilegeUtilities
{
    const PRIVILEGE_TO_REGISTER_NEW_USER = 'registerNewUsers';
    const PRIVILEGE_TO_EDIT_ASSISTANT_VOICE = 'editAssistantVoice';
    const PRIVILEGE_TO_EDIT_BATTERY_SCRAP_EAN_LIST = 'batteryScrapEanListEdit';
    const PRIVILEGE_TO_USE_CALCULATOR = 'useCalculator';
    const PRIVILEGE_TO_USE_BOX_SCAN = 'useBoxScan';
    const PRIVILEGE_TO_CREATE_NEW_ITEM_SCAN = 'createNewItemScan';
    const PRIVILEGE_TO_SCAN_NEW_ITEMS = 'scanNewItems';
    const PRIVILEGE_TO_MANAGE_REQUIRED_LISTS = 'manageRequiredLists';
    const PRIVILEGE_TO_ACCESS_ADMIN_PANEL = 'accessAdminPanel';
    const PRIVILEGE_TO_CREATE_NEW_BOX_BUILDS = 'createNewBoxBuild';
    const PRIVILEGE_TO_VIEW_BOX_BUILDS = 'viewBoxBuilds';
    const PRIVILEGE_TO_EDIT_BOX_BUILDS = 'editBoxBuilds';
    const PRIVILEGE_TO_SCAN_BOX_BUILDS = 'scanBoxBuilds';
    const PRIVILEGE_TO_USE_ITEM_TRANSFER = 'itemTransfer';
    const PRIVILEGE_TO_OPEN_GATES = 'openGates';
    const PRIVILEGE_TO_USE_WAREHOUSE = 'useWarehouse';
    const PRIVILEGE_TO_USE_PRINTER = 'usePrinter';
    const PRIVILEGE_TO_EDIT_SN_LOOK_OVER = 'editSnLookOver';

    static function getPrivilegeConstants()
    {
        $class = new \ReflectionClass(__CLASS__);
        return $class->getConstants();
    }

    public static function giveAllPrivilegesToUser(User $user): void
    {
        Lock::db('privileges');
        $insertArray = [];
        $privileges = Privilege::all();

        foreach ($privileges as $privilege) {
            $insertArray[] = [
                'user_id' => $user->id,
                'privilege_id' => $privilege->id,
            ];
        }

        UserPrivilege::whereUserId($user->id)->delete();
        UserPrivilege::insert($insertArray);
        Lock::remove();
    }

    /**
     * Returns an array of strings ['privilege1', 'privilege2', ...]
     *
     * @param User $user
     * @return array
     */
    public static function getUserPrivileges(User $user): array
    {
        $privilegeArray = [];
        $privileges = UserPrivilege::whereUserId($user->id)->with(['privilege'])->get();
        foreach ($privileges as $privilege) {
            $privilegeArray[] = $privilege->privilege['name'];
        }
        return $privilegeArray;
    }
}

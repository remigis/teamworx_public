<?php

namespace Database\Seeders;

use App\Models\Privilege;
use App\Utilities\PrivilegeUtilities;
use Illuminate\Database\Seeder;

class PrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_REGISTER_NEW_USER, 'text' => 'Send registration invitations.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_EDIT_ASSISTANT_VOICE, 'text' => 'Change assistants voice.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_EDIT_BATTERY_SCRAP_EAN_LIST, 'text' => 'Edit Battery/Scrap list.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_USE_CALCULATOR, 'text' => 'Use calculator.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_USE_BOX_SCAN, 'text' => 'Use box scan.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_CREATE_NEW_ITEM_SCAN, 'text' => 'Create new item scans.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_SCAN_NEW_ITEMS, 'text' => 'Scan new items.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_MANAGE_REQUIRED_LISTS, 'text' => 'Manage required lists.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_ACCESS_ADMIN_PANEL, 'text' => 'Access Admin panel.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_CREATE_NEW_BOX_BUILDS, 'text' => 'Create new box builds.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_VIEW_BOX_BUILDS, 'text' => 'View box builds.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_EDIT_BOX_BUILDS, 'text' => 'Edit box builds.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_SCAN_BOX_BUILDS, 'text' => 'Scan box builds.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_USE_ITEM_TRANSFER, 'text' => 'Use item transfer.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_OPEN_GATES, 'text' => 'Open gates.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_USE_WAREHOUSE, 'text' => 'Use warehouse.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_USE_PRINTER, 'text' => 'Use printer.']);
        Privilege::create(['name' => PrivilegeUtilities::PRIVILEGE_TO_EDIT_SN_LOOK_OVER, 'text' => 'Edit SN Look Over.']);
    }
}

<?php

namespace Database\Seeders;

use App\Utilities\Constants;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'name' => Constants::SETTINGS_CHECK_REQUIRED_LIST,
            'value' => false,
        ]);
        DB::table('settings')->insert([
            'name' => Constants::SETTINGS_RAZER_API_STATUS,
            'value' => false,
        ]);
        DB::table('settings')->insert([
            'name' => Constants::AUDIO_SCRAP_SETTINGS,
            'value' => 'Scrap',
        ]);
        DB::table('settings')->insert([
            'name' => Constants::AUDIO_BATTERY_SCRAP_SETTINGS,
            'value' => 'Scrap-Battery',
        ]);
        DB::table('settings')->insert([
            'name' => Constants::AUDIO_ERROR_SETTINGS,
            'value' => 'Error',
        ]);
        DB::table('settings')->insert([
            'name' => Constants::NEW_ITEM_SCAN_LOCK_STATUS,
            'value' => false,
        ]);
        DB::table('settings')->insert([
            'name' => Constants::BOX_BUILD_DIRECT_SCAN_CENTER,
            'value' => null,
        ]);
        DB::table('settings')->insert([
            'name' => Constants::BOX_BUILD_DIRECT_SCAN_STATUS,
            'value' => false,
        ]);
        DB::table('settings')->insert([
            'name' => Constants::GATE_PHONE_NUMBER,
            'value' => null,
        ]);
        DB::table('settings')->insert([
            'name' => Constants::AUTO_LOOK_OVER_STATUS,
            'value' => false,
        ]);
    }
}

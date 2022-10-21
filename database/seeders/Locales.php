<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Locales extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('languages')->insert([
            'language' => "English",
            'alt' => "EN",
            'tag' => "en",
        ]);
        DB::table('languages')->insert([
            'language' => "Lietuvių",
            'alt' => "LT",
            'tag' => "lt",
        ]);
    }
}

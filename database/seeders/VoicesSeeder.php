<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('voices')->insert(['name' => 'Nicole', 'label' => 'Nicole | Australian English', 'location' => 'Australian English']);
        DB::table('voices')->insert(['name' => 'Russell', 'label' => 'Russell | Australian English', 'location' => 'Australian English']);
        DB::table('voices')->insert(['name' => 'Brian', 'label' => 'Brian | British English', 'location' => 'British English']);
        DB::table('voices')->insert(['name' => 'Emma', 'label' => 'Emma | British English', 'location' => 'British English']);
        DB::table('voices')->insert(['name' => 'Amy', 'label' => 'Amy | British English', 'location' => 'British English']);
        DB::table('voices')->insert(['name' => 'Aditi', 'label' => 'Aditi | Indian English', 'location' => 'Indian English']);
        DB::table('voices')->insert(['name' => 'Raveena', 'label' => 'Raveena | Indian English', 'location' => 'Indian English']);
        DB::table('voices')->insert(['name' => 'Ivy', 'label' => 'Ivy | US English', 'location' => 'US English']);
        DB::table('voices')->insert(['name' => 'Kimberly', 'label' => 'Kimberly | US English', 'location' => 'US English']);
        DB::table('voices')->insert(['name' => 'Joey', 'label' => 'Joey | US English', 'location' => 'US English']);
        DB::table('voices')->insert(['name' => 'Justin', 'label' => 'Justin | US English', 'location' => 'US English']);
        DB::table('voices')->insert(['name' => 'Joanna', 'label' => 'Joanna | US English', 'location' => 'US English']);
        DB::table('voices')->insert(['name' => 'Salli', 'label' => 'Salli | US English', 'location' => 'US English']);
        DB::table('voices')->insert(['name' => 'Matthew', 'label' => 'Matthew | US English', 'location' => 'US English']);
        DB::table('voices')->insert(['name' => 'Kendra', 'label' => 'Kendra | US English', 'location' => 'US English']);
        DB::table('voices')->insert(['name' => 'Geraint', 'label' => 'Geraint | Welsh English', 'location' => 'Welsh English']);

    }
}

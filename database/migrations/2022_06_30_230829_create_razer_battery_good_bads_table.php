<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRazerBatteryGoodBadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('razer_battery_good_bads', function (Blueprint $table) {
            $table->id();
            $table->boolean('scrap')->nullable();
            $table->boolean('battery')->nullable();
            $table->string('rz')->unique();
            $table->string('ean')->unique()->nullable();
            $table->longText('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('razer_battery_good_bads');
    }
}

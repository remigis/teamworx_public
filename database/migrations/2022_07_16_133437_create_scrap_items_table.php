<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrapItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scrap_items', function (Blueprint $table) {
            $table->id();
            $table->string('sn');
            $table->string('rz');
            $table->bigInteger('user_id');
            $table->bigInteger('new_item_scan_id');
            $table->bigInteger('scrap_pallet_id');
            $table->bigInteger('scanned_sn_id');
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
        Schema::dropIfExists('scrap_items');
    }
}

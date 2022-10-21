<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewItemScanPalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_item_scan_pallets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('new_item_scan_id');
            $table->integer('pallet_number');
            $table->string('text');
            $table->integer('box_number')->nullable()->default(null);
            $table->boolean('closed')->default(false);
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
        Schema::dropIfExists('new_item_scan_pallets');
    }
}

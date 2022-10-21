<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemToScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_to_scans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('new_item_scan_id');
            $table->string('product');
            $table->string('product_description');
            $table->string('serial_number');
            $table->string('defect');
            $table->string('date_from_distributor')->nullable();
            $table->string('date_from_customer')->nullable();
            $table->string('remarks')->nullable();
            $table->string('product_checked');
            $table->string('description');
            $table->string('remarks_product_code');
            $table->string('remarks_defect_description');
            $table->string('remarks_sn');
            $table->string('status');
            $table->string('qty');
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
        Schema::dropIfExists('item_to_scans');
    }
}

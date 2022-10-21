<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_labels', function (Blueprint $table) {
            $table->id();
            $table->string('warehouse_key');
            $table->string('text');
            $table->string('number')->nullable();
            $table->string('warehouse_pallet_type_id')->nullable();
            $table->string('warehouse_position_id')->nullable();
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
        Schema::dropIfExists('warehouse_labels');
    }
}

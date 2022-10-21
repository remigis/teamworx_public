<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxBuildRequiredItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('box_build_required_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('box_build_id');
            $table->string('product_condition');
            $table->string('warehouse_center_id');
            $table->string('vid');
            $table->string('manufacturer');
            $table->integer('required')->default(0);
            $table->integer('collected')->default(0);
            $table->integer('need')->default(0);
            $table->integer('priority');
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
        Schema::dropIfExists('box_build_required_items');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxBuildBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('box_build_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('active');
            $table->foreignId('warehouse_center_id')->constrained('warehouse_centers');
            $table->foreignId('box_build_id')->nullable();
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
        Schema::dropIfExists('box_build_boxes');
    }
}

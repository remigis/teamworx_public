<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxBuildBoxItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('box_build_box_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('box_build_box_id')->constrained('box_build_boxes');
            $table->string('product');
            $table->string('condition');
            $table->string('vid')->nullable();
            $table->string('gf')->unique();
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
        Schema::dropIfExists('box_build_box_items');
    }
}

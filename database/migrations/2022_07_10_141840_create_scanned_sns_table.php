<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScannedSnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scanned_sns', function (Blueprint $table) {
            $table->id();
            $table->string('sn');
            $table->string('rz');
            $table->string('result')->nullable();
            $table->bigInteger('user_id');
            $table->bigInteger('new_item_scan_id');
            $table->boolean('direct_scarp');
            $table->boolean('direct_scarp_result');
            $table->boolean('check_required');
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
        Schema::dropIfExists('scanned_sns');
    }
}

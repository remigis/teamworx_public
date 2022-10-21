<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNeedToScanGoodsFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('need_to_scan_goods_flows', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('box_id');
            $table->bigInteger('scan_id');
            $table->string('tester');
            $table->string('zustand');
            $table->string('seriennummer');
            $table->string('kommentar');
            $table->string('time');
            $table->string('goodsflow');
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
        Schema::dropIfExists('need_to_scan_goods_flows');
    }
}

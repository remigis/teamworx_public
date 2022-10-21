<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrapPalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scrap_pallets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('text');
            $table->string('type');
            $table->boolean('battery')->nullable();
            $table->boolean('exported')->default(false);
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
        Schema::dropIfExists('scrap_pallets');
    }
}

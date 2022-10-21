<?php

use App\Utilities\Constants;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewItemScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_item_scans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rma');
            $table->string('sender');
            $table->string('uploaded_file_path')->nullable();
            $table->string('export_file_path')->nullable();
            $table->string('ext')->nullable();
            $table->enum('status', [Constants::SCAN_STATUS_NEW, Constants::SCAN_STATUS_DONE, Constants::SCAN_STATUS_CONFIRMED])->default(Constants::SCAN_STATUS_NEW);
            $table->boolean('show')->default(false);
            $table->string('uploaded_file_name')->nullable();
            $table->string('export_file_name')->nullable();
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
        Schema::dropIfExists('new_item_scans');
    }
}

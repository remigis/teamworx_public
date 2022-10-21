<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConditionNotImportantToBoxBuildBoxItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('box_build_box_items', function (Blueprint $table) {
            $table->boolean('condition_not_important')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('box_build_box_items', function (Blueprint $table) {
            $table->dropColumn('condition_not_important');
        });
    }
}

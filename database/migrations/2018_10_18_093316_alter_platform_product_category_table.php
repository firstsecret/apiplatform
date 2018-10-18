<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlatformProductCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_product_categories', function (Blueprint $table) {
            //
            $table->renameColumn('name', 'title');
            $table->unsignedSmallInteger('order')->default(0)->comment('排序');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platform_product_categories', function (Blueprint $table) {
            //
            $table->renameColumn('title', 'name');
            $table->dropColumn('order');
        });
    }
}

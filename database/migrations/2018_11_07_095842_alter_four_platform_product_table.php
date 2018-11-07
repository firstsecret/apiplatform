<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFourPlatformProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platform_products', function (Blueprint $table) {
            //
            $table->unsignedTinyInteger('is_online')->default(1)->comment('是否上线');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platform_products', function (Blueprint $table) {
            //
            $table->dropColumn('is_online');
        });
    }
}

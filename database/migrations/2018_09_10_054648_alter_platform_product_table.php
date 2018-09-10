<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlatformProductTable extends Migration
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
            $table->string('api_path', 64)->default('')->comment('该服务的api路径');
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
            $table->dropColumn('api_path');
        });
    }
}

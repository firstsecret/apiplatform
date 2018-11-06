<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAgainPlatformProductsTable extends Migration
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
            $table->unique('api_path', 'unique_api_path_platform_product');
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
            $table->dropUnique('unique_api_path_platform_product');
        });
    }
}

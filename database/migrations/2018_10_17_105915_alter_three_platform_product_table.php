<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterThreePlatformProductTable extends Migration
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
            $table->string('internal_request_method',16)->default('GET');
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
            $table->dropColumn('internal_request_method');
        });
    }
}

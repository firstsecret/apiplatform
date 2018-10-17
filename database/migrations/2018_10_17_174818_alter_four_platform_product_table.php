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
            $table->string('api_path')->default('/api/default')->change();
            $table->string('internal_api_path')->default('/api/default')->change();
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
            $table->string('api_path')->default('')->change();
            $table->string('internal_api_path')->default('')->change();
        });
    }
}

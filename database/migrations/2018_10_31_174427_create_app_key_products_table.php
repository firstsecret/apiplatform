<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppKeyProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_key_products', function (Blueprint $table) {
            //
//            $table->increments('id');
            $table->unsignedInteger('app_key_id')->index()->comment('app_key_id');
            $table->unsignedInteger('product_id')->index()->comment('product_id');
//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_key_products');
    }
}

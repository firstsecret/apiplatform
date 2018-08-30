<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique()->comment('產品名稱');
            $table->string('detail')->default('')->commnet('描述');
            $table->unsignedSmallInteger('type')->comment('產品類型id');
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
        Schema::dropIfExists('platform_products');
    }
}

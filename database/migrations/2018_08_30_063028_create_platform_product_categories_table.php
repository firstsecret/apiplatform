<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_product_categories', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('parent_id');
            $table->string('name',32)->unique()->comment('分类名称');
            $table->string('detail',128)->default('')->comment('分类简述');
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
        Schema::dropIfExists('platform_product_categories');
    }
}

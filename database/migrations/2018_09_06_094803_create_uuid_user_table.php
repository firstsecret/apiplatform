<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUuidUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uuid_users', function (Blueprint $table) {
//            $table->increments('id');
            $table->unsignedInteger('user_id')->index()->comment('用户Id');
            $table->unsignedMediumInteger('model_id')->index()->comment('内部应用id');
            $table->string('model_uuid', 32)->index()->comment('内部应用的uuid');
            $table->string('openid', 64)->unique()->comment('用户与 应用不同的openid');
            $table->string('model', 32)->comment('应用对应的模型');
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
        Schema::dropIfExists('uuid_users');
    }
}

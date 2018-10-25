<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpRequestFlow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip_request_flows', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip', 32)->comment('remote addr');
            $table->string('request_uri', 255)->comment('request url');
            $table->unsignedInteger('today_total_number')->default(1)->comment('当日的请求总量');
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
        Schema::dropIfExists('ip_request_flows');
    }
}

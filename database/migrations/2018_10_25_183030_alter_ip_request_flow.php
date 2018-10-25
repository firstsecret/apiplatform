<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIpRequestFlow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ip_request_flows', function (Blueprint $table) {
            //
            $table->index('ip','ip_request_flows_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ip_request_flows', function (Blueprint $table) {
            //
            $table->dropIndex('ip_request_flows_index');
        });
    }
}

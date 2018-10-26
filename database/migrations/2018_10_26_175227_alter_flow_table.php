<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flows', function (Blueprint $table) {
            //
            $table->dropColumn('ip_status');
            $table->string('request_uri', 254)->comment('请求的uri');
            $table->unsignedMediumInteger('request_number')->default(1)->comment('请求的数量');
            $table->unique('request_uri', 'request_uri_flows');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flows', function (Blueprint $table) {
            //
            $table->dropUnique('request_uri_flows');
            $table->mediumText('ip_status')->comment('每日的api请求情况');
            $table->dropColumn('request_uri');
            $table->dropColumn('request_number');
        });
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAppUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_users', function (Blueprint $table) {
            //
            $table->unsignedTinyInteger('type')->default(0)->comment('是否永久授权型的appkey,0非永久型,1永久型');
            $table->index('model', 'app_users_model_index');
            $table->index('user_id', 'app_users_user_id_index');
            $table->index('type', 'app_users_type_index');
            $table->index('app_secret', 'app_users_app_secret_index');
            $table->unique('app_key', 'app_users_app_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_users', function (Blueprint $table) {
            //
            $table->dropIndex('app_users_user_id_index');
            $table->dropIndex('app_users_model_index');
            $table->dropIndex('app_users_type_index');
            $table->dropIndex('app_users_app_secret_index');
            $table->dropUnique('app_users_app_key_unique');
            $table->dropColumn('type');
        });
    }
}

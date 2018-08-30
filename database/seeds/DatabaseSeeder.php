<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
//        DB::table('app_users')
//            ->insert([
//                'app_key' => md5(rand(1000,9999)),
//                'app_secret' => md5(rand(1000,9999)),
//                'user_id' => 1,
//                'app_id' => rand(1,100)
//            ]);
        $this->call([
           PlatformProductSeeder::class
        ]);
    }
}

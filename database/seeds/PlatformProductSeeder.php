<?php

use Illuminate\Database\Seeder;

class PlatformProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(\App\Models\PlatformProduct::class, 30)->create();
    }
}

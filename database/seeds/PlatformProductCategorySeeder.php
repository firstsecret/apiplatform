<?php

use Illuminate\Database\Seeder;

class PlatformProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(\App\Models\PlatformProductCategory::class, 10)->create();
    }
}

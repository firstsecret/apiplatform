<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\PlatformProductCategory::class, function (Faker $faker) {
    return [
        //
        'name' => $faker->name,
        'detail' => $faker->name,
        'parent_id' => 0
    ];
});

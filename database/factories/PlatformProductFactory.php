<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\PlatformProduct::class,function (Faker $faker) {
    return [
        //
        'name' => $faker->name,
        'detail' => $faker->name,
        'type' => '0'
    ];
});

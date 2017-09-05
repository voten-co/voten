<?php

use Faker\Generator as Faker;

$factory->define(\App\Category::class, function (Faker $faker) {
    return [
        'name'        => str_slug($faker->name, ''),
        'description' => $faker->paragraph(),
    ];
});

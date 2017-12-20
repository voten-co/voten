<?php

use Faker\Generator as Faker;

$factory->define(\App\Channel::class, function (Faker $faker) {
    return [
        'name'        => str_slug($faker->name, ''),
        'description' => $faker->paragraph(),
        'avatar'      => '/imgs/channel-avatar.png',
    ];
});

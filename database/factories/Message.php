<?php

use Faker\Generator as Faker;

$factory->define(\App\Message::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory('App\User')->create()->id;
        },
        
        'data' => [
            'text' => $faker->paragraph(),
        ],
    ];
});

<?php

use Faker\Generator as Faker;

$factory->define(\App\User::class, function (Faker $faker) {
    return [
        'username'       => $faker->username,
        'name'           => $faker->name,
        'location'       => $faker->country,
        'email'          => $faker->safeEmail,
        'password'       => bcrypt('xbox360'),
        'remember_token' => str_random(10),
    ];
});

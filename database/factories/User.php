<?php

use Faker\Generator as Faker;

$factory->define(\App\User::class, function (Faker $faker) {
    return [
        'username'       => $faker->username,
        'name'           => $faker->name,
        'location'       => $faker->country,
        'email'          => $faker->safeEmail,
        'password'       => bcrypt('password'),
        'remember_token' => str_random(10),
        'confirmed'      => 0,
        
        'settings'       => [
            'notify_submissions_replied'    => true,
            'notify_comments_replied'       => true,
            'notify_mentions'               => true,
        ],
        
        'info' => [
            'website' => null,
            'twitter' => null,
        ],
    ];
});

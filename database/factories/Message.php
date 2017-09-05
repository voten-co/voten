<?php

use Faker\Generator as Faker;

$factory->define(\App\Message::class, function (Faker $faker) {
    return [
        'chat_id'     => '2_3',
        'sender_id'   => '3',
        'receiver_id' => '2',
        'message'     => $faker->sentence($nbWords = 6, $variableNbWords = true),
    ];
});

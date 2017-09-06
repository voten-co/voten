<?php

use Faker\Generator as Faker;

$factory->define(\App\Message::class, function (Faker $faker) {
    return [
        'chat_id'     => '1_2',
        'sender_id'   => '2',
        'receiver_id' => '1',
        'message'     => $faker->sentence($nbWords = 6, $variableNbWords = true),
    ];
});

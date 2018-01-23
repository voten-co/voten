<?php

use Faker\Generator as Faker;

$factory->define(\App\Submission::class, function (Faker $faker) {
    $title = $faker->sentence($nbWords = 6, $variableNbWords = true);
    $channel = factory('App\User')->create();

    return [
        'user_id'       => function () {
            return factory('App\User')->create()->id;
        },
        'title'         => $title,
        'data'          => ['text' => $faker->paragraph()],
        'type'          => 'text',
        'channel_id'    => $channel->id,
        'channel_name'  => $channel->name,
        'slug'          => str_slug($title),
    ];
});

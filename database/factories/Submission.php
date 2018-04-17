<?php

use Faker\Generator as Faker;

$factory->define(\App\Submission::class, function (Faker $faker) {
    $title = $faker->sentence($nbWords = 6, $variableNbWords = true);
    $channel = factory('App\Channel')->create();
    $slug = str_slug($title); 

    return [
        'title' => $title,
        'slug' => $slug,
        'url' => config('app.url') . '/c/' . $channel->name . '/' . $slug,
        'domain' => null, 
        'type' => 'text',
        'data' => ['text' => $faker->paragraph()],
        'rate' => firstRate(), 
        
        'user_id' => function () {
            return factory('App\User')->create()->id;
        },
        
        'nsfw' => 0, 
        'channel_id' => $channel->id,
        'channel_name' => $channel->name,
    ];
});

$factory->state(\App\Submission::class, 'link', function (Faker $faker) {
    return [
        'type' => 'link',
        'title' => 'an awesome platform', 

        'data' => [
            'url'           => 'https://voten.co',
            'title'         => 'an awesome platform',
            'description'   => null,
            'type'          => 'link',
            'embed'         => null,
            'img'           => null,
            'thumbnail'     => null,
            'providerName'  => null,
            'publishedTime' => null,
            'domain'        => domain('https://voten.co'),
        ]
    ];
});

$factory->state(\App\Submission::class, 'nsfw', function (Faker $faker) {
    return [
        'nsfw' => 1, 
    ];
});

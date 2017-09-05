<?php

use Faker\Generator as Faker;

$factory->define(\App\Submission::class, function (Faker $faker) {
    $title = $faker->sentence($nbWords = 6, $variableNbWords = true);

    return [
        'user_id'       => 1,
        'title'         => $title,
        'data'          => ['text' => $faker->paragraph()],
        'type'          => 'text',
        'category_id'   => 22,
        'category_name' => 'gaming',
        'slug'          => str_slug($title),
    ];
});

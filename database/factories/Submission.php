<?php

use Faker\Generator as Faker;

$factory->define(\App\Submission::class, function (Faker $faker) {
    $title = $faker->sentence($nbWords = 6, $variableNbWords = true);
    $category = factory('App\User')->create();

    return [
        'user_id'       => function () {
            return factory('App\User')->create()->id;
        },
        'title'         => $title,
        'data'          => ['text' => $faker->paragraph()],
        'type'          => 'text',
        'category_id'   => $category->id,
        'category_name' => $category->name,
        'slug'          => str_slug($title),
    ];
});

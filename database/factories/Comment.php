<?php

use Faker\Generator as Faker;

$factory->define(\App\Comment::class, function (Faker $faker) {
    return [
        'user_id'       => 2,
        'body'          => $faker->paragraph(),
        'parent_id'     => 0,
        'submission_id' => 4249,
        'category_id'   => 215,
        'level'         => 2,
    ];
});

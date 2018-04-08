<?php

use Faker\Generator as Faker;

$factory->define(\App\Comment::class, function (Faker $faker) {
    $submission = factory('App\Submission')->create();

    return [
        'body'          => $faker->paragraph(),
        'user_id'       => $submission->user_id,
        'channel_id'    => $submission->channel_id,
        'parent_id'     => 0,
        'level'         => 0,
        'submission_id' => $submission->id,
        'likes'       => 1,
        'edited_at'     => null,
        'rate' => firstRate(),
    ];
});

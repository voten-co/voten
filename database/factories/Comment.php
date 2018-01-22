<?php

use Faker\Generator as Faker;

$factory->define(\App\Comment::class, function (Faker $faker) {
    $submission = factory('App\Submission')->create();

    return [
        'user_id'       => $submission->user_id,
        'body'          => $faker->paragraph(),
        'parent_id'     => 0,
        'submission_id' => $submission->id,
        'channel_id'    => $submission->channel_id,
        'level'         => 0,
        'upvotes'       => 1,
        'downvotes'     => 0,
        'edited_at'     => null,
    ];
});

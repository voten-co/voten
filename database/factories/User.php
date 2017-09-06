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
            'font'                          => 'Lato',
            'sidebar_color'                 => 'Gray',
            'nsfw'                          => false,
            'nsfw_media'                    => false,
            'notify_submissions_replied'    => true,
            'notify_comments_replied'       => true,
            'notify_mentions'               => true,
            'exclude_upvoted_submissions'   => false,
            'exclude_downvoted_submissions' => true,
            'submission_small_thumbnail'    => true,
        ],
        'info' => [
            'website' => null,
            'twitter' => null,
        ],
    ];
});

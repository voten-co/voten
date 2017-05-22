<?php

// User
$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
      'username'       => $faker->username,
      'name'           => $faker->name,
      'location'       => $faker->country,
      'email'          => $faker->safeEmail,
      'password'       => bcrypt('xbox360'),
      'remember_token' => str_random(10),
    ];
});

// Submission
$factory->define(App\Submission::class, function (Faker\Generator $faker) {
    $title = $faker->sentence($nbWords = 6, $variableNbWords = true);

    return [
        'user_id' => factory('App\User')->create()->id,
        // 'user_id' => 1,
        'title'         => $title,
        'data'          => ['text' => $faker->paragraph()],
        'type'          => 'text',
        'category_id'   => 22,
        'category_name' => 'gaming',
        'slug'          => str_slug($title),
    ];
});

// article
$factory->define(App\Article::class, function (Faker\Generator $faker) {
    $title = $faker->sentence($nbWords = 6, $variableNbWords = true);

    return [
        'title'     => $title,
        'sub_title' => $faker->sentence($nbWords = 8, $variableNbWords = true),
        'slug'      => str_slug($title),
        'body'      => '<p>'.$faker->paragraph($nbSentences = 5, $variableNbSentences = true).
        '</p>'.'<h2>'.$faker->sentence($nbWords = 6, $variableNbWords = true).'</h2>'.'<p>'.$faker->paragraph($nbSentences = 3, $variableNbSentences = true).
        '</p>'.'<p>'.$faker->paragraph($nbSentences = 6, $variableNbSentences = true).
        '</p>'.'<h2>'.$faker->sentence($nbWords = 8, $variableNbWords = true).'</h2>'.'<p>'.$faker->paragraph($nbSentences = 8, $variableNbSentences = true).
        '</p>',
    ];
});

// Category
$factory->define(App\Category::class, function (Faker\Generator $faker) {
    $name = $faker->name;

    return [
        // 'user_id' => factory('App\User')->create()->id,
        'name'        => str_slug($name, ''),
        'description' => $faker->paragraph(),
    ];
});

// invite
$factory->define(App\Invite::class, function (Faker\Generator $faker) {
    return [
        'invitation' => str_random(50),
        'email'      => $faker->safeEmail,
    ];
});

// message
$factory->define(App\Message::class, function (Faker\Generator $faker) {
    return [
        'chat_id'     => '2_3',
        'sender_id'   => '3',
        'receiver_id' => '2',
        'message'     => $faker->sentence($nbWords = 6, $variableNbWords = true),
    ];
});

// feedback
$factory->define(App\Feedback::class, function (Faker\Generator $faker) {
    return [
        'subject'     => $faker->sentence($nbWords = 6, $variableNbWords = true),
        'user_id'     => factory('App\User')->create()->id,
        'description' => $faker->paragraph(),
    ];
});

// Comment
$factory->define(App\Comment::class, function (Faker\Generator $faker) {
    return [
        'user_id'       => 2,
        'body'          => $faker->paragraph(),
        'parent_id'     => 0,
        'submission_id' => 127,
        'category_id'   => 331,
        'level'         => 0,
    ];
});

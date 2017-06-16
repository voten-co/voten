<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Universal Setting Variables
    |--------------------------------------------------------------------------
    |
    | Please set these variables to personalize your Voten site
    |
    */
    'title'             => 'Social Bookmarking For The 21st Century',
    'description'       => 'A Modern, real-time, open-source, beautiful, deadly simple and warm community.',
    'tos_description'   => 'While Voten enables users to apply leeway to what content is acceptable, here are some guidelines for content that is not:',
    'about_description' => 'A Modern, real-time, open-source, beautiful, deadly simple and warm community.',

    'google' => [
        'url' => 'https://google.com/your_google',

        'client_id' => '',
        'secret'    => '',
        'callback'  => 'https://voten.co/login/facebook/callback',

        'analytics' => 'UA-A38242'
    ],

    'facebook' => [
        'url' => 'https://facebook.com/your_facebook',

        'client_id' => '',
        'secret'    => '',
        'callback'  => 'https://voten.co/login/facebook/callback',
    ],

    'twitter' => [
        'url'  => 'https://twitter.com/your_twitter',
        'name' => '@username',

        'client_id' => '',
        'secret'    => '',
        'callback'  => 'https://voten.co/login/facebook/callback'
    ],

    'email' => [
        'info'  => 'info@your-site.com',
        'press' => 'press@your-site.com',
    ],
];

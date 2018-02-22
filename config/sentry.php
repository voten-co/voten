<?php

return [
    'dsn' => env('SENTRY_DSN'),
    'dsn-js' => env('SENTRY_DSN_JS'),

    // capture release as git sha
    // 'release' => trim(exec('git log --pretty="%h" -n1 HEAD')),

    // Capture bindings on SQL queries
    'breadcrumbs.sql_bindings' => true,
];

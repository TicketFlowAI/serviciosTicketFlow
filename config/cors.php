<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'user',
        'forgot-password',
        'reset-password',
        'register',
        'email/verify',
        'email/verification-notification',
        '/user/two-factor-qr-code', 
        '/user/confirmed-two-factor-authentication',
        '/user/confirm-password',
        '/user/two-factor-recovery-codes',
        '/user/two-factor-challenge',
        '/user/two-factor-authentication',
        'two-factor-challenge',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [env('SANCTUM_STATEFUL_DOMAINS')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];

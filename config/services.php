<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'jubipay' => [
    'base_url'       => env('JUBIPAY_BASE_URL'),
    'email_endpoint' => env('JUBIPAY_EMAIL_ENDPOINT', '/email/send/LIFE_BUSINESS'),
    'username'       => env('JUBIPAY_USERNAME'),
    'password'       => env('JUBIPAY_PASSWORD'),
    'from_email'     => env('JUBIPAY_FROM_EMAIL'),   // ← new
    'from_name'      => env('JUBIPAY_FROM_NAME'),    // ← new
],

];

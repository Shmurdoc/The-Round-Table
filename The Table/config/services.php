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

    'nowpayments' => [
        'api_key' => env('NOWPAYMENTS_API_KEY'),
        'api_url' => env('NOWPAYMENTS_API_URL', 'https://api.nowpayments.io/v1'),
        'ipn_secret' => env('NOWPAYMENTS_IPN_SECRET'),
        'sandbox' => env('NOWPAYMENTS_SANDBOX', false),
    ],

    'payfast' => [
        'merchant_id' => env('PAYFAST_MERCHANT_ID', '10000100'),
        'merchant_key' => env('PAYFAST_MERCHANT_KEY', '46f0cd694581a'),
        'passphrase' => env('PAYFAST_PASSPHRASE', null),
        'test_mode' => env('PAYFAST_TEST_MODE', true),
    ],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];

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

    'whatsapp' => [
        'api_url' => env('WHATSAPP_API_URL', 'https://api.fonnte.com/send'),
        'api_token' => env('WHATSAPP_API_TOKEN'),
    ],

    'firebase' => [
        'api_key' => env('FIREBASE_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_APP_ID'),
        'vapid_key' => env('FIREBASE_VAPID_KEY'),
        'service_account_json' => env('FIREBASE_SERVICE_ACCOUNT_JSON'),
    ],

    'equran_prayer' => [
        'base_url' => env('EQURAN_PRAYER_URL', 'https://equran.id/api/v2/shalat'),
        'province' => env('PRAYER_DEFAULT_PROVINCE', 'Jawa Barat'),
        'city' => env('PRAYER_DEFAULT_CITY', 'Kota Bandung'),
        'cache_hours' => (int) env('PRAYER_SCHEDULE_CACHE_HOURS', 24),
        'on_time_minutes' => (int) env('PRAYER_ON_TIME_MINUTES', 60),
        'late_minutes' => (int) env('PRAYER_LATE_MINUTES', 30),
    ],

];

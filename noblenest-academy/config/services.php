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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Services
    |--------------------------------------------------------------------------
    */

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Provider Services
    |--------------------------------------------------------------------------
    */

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
    ],

    'ai' => [
        'daily_image_limit' => (int) env('AI_DAILY_IMAGE_LIMIT', 200),
        'daily_audio_limit' => (int) env('AI_DAILY_AUDIO_LIMIT', 50),
        'daily_video_limit' => (int) env('AI_DAILY_VIDEO_LIMIT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Curriculum AI Python Sidecar
    |--------------------------------------------------------------------------
    | Configuration for the Python-based curriculum generation service.
    | This service generates activities with full Phase 2 metadata.
    */

    'curriculum_ai' => [
        'base_url' => env('CURRICULUM_AI_BASE_URL', 'http://localhost:8001'),
        'api_key' => env('CURRICULUM_AI_API_KEY', ''),
        'timeout' => (int) env('CURRICULUM_AI_TIMEOUT', 60),
    ],

];

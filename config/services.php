<?php

return [
    /*
     * |--------------------------------------------------------------------------
     * | Third Party Services
     * |--------------------------------------------------------------------------
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

    /*
     * |--------------------------------------------------------------------------
     * | WhatsApp API (WhacCenter)
     * |--------------------------------------------------------------------------
     */
    'whatsapp' => [
        'url' => env('WA_URL', 'https://app.whacenter.com/api/send'),
        'token' => env('WA_TOKEN'),
        'test_phone' => env('WA_TEST_PHONE'),
    ],

    /*
     * |--------------------------------------------------------------------------
     * | Google Services (Gemini AI)
     * |--------------------------------------------------------------------------
     */
    'google' => [
        'gemini_api_key' => env('GEMINI_API_KEY'),
    ],
    'gemini' => [
        'enabled' => true,  // Hardcoded temporarily
        'api_key' => 'AIzaSyDZhqltbKXBJHrNcdKObz7bp22pzvT3tWw',
    ],

    /*
     * |--------------------------------------------------------------------------
     * | OpenRouter AI Service
     * |--------------------------------------------------------------------------
     */
    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'api_url' => env('OPENROUTER_API_URL', 'https://openrouter.ai/api/v1/chat/completions'),
    ],
];

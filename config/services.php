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

    'chatbot' => [
        'provider' => env('CHATBOT_AI_PROVIDER', 'gemini'),
        'api_key' => env('CHATBOT_AI_API_KEY'),
        'openai_api_key' => env('CHATBOT_OPENAI_API_KEY'),
        'endpoint' => env('CHATBOT_AI_ENDPOINT'),
        'model' => env('CHATBOT_AI_MODEL', 'gemini-3.8-flash'),
        'fallback_models' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('CHATBOT_AI_FALLBACK_MODELS', 'gemini-3.5-flash-lite,gemini-2.5-flash-lite')),
        ))),
        'timeout' => (int) env('CHATBOT_AI_TIMEOUT', 20),
        'max_output_tokens' => (int) env('CHATBOT_AI_MAX_OUTPUT_TOKENS', 500),
        'max_models_per_request' => (int) env('CHATBOT_AI_MAX_MODELS_PER_REQUEST', 3),
        'max_requests_per_minute' => (int) env('CHATBOT_AI_MAX_REQUESTS_PER_MINUTE', 10),
        'max_total_seconds' => (int) env('CHATBOT_AI_MAX_TOTAL_SECONDS', 35),
        'log_conversations' => (bool) env('CHATBOT_LOG_CONVERSATIONS', false),
        'log_retention_days' => (int) env('CHATBOT_LOG_RETENTION_DAYS', 30),
    ],

];

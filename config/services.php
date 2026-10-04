<?php

$chatbotProvider = env('CHATBOT_AI_PROVIDER', 'router');
$chatbotGroqKey = (string) env('CHATBOT_GROQ_API_KEY', '');
$chatbotGeminiKey = (string) env('CHATBOT_AI_API_KEY', '');
$chatbotOpenAIKey = (string) (env('CHATBOT_OPENAI_API_KEY') ?: $chatbotGeminiKey);

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
        'provider' => $chatbotProvider,
        'api_key' => env('CHATBOT_AI_API_KEY'),
        'openai_api_key' => env('CHATBOT_OPENAI_API_KEY'),
        'endpoint' => env('CHATBOT_AI_ENDPOINT'),
        'model' => env('CHATBOT_AI_MODEL', 'gemini-3.8-flash'),
        'primary_provider' => env('CHATBOT_AI_PRIMARY_PROVIDER', 'groq'),
        'fallback_providers' => array_values(array_filter(array_map(
            static fn (string $provider): string => strtolower(trim($provider)),
            explode(',', (string) env('CHATBOT_AI_FALLBACK_PROVIDERS', 'gemini')),
        ))),
        'max_providers_per_request' => (int) env('CHATBOT_AI_MAX_PROVIDERS_PER_REQUEST', 2),
        'max_attempts_per_request' => (int) env('CHATBOT_AI_MAX_ATTEMPTS_PER_REQUEST', 4),
        'ai_enabled' => match ($chatbotProvider) {
            'mock' => true,
            'router' => $chatbotGroqKey !== '' || $chatbotGeminiKey !== '',
            'groq' => $chatbotGroqKey !== '',
            'openai', 'openai-compatible' => $chatbotOpenAIKey !== '',
            default => $chatbotGeminiKey !== '',
        },
        'groq' => [
            'api_key' => $chatbotGroqKey,
            'endpoint' => env('CHATBOT_GROQ_ENDPOINT', 'https://api.groq.com/openai/v1/chat/completions'),
            'model' => env('CHATBOT_GROQ_MODEL', 'openai/gpt-oss-20b'),
            'fallback_models' => array_values(array_filter(array_map(
                'trim',
                explode(',', (string) env(
                    'CHATBOT_GROQ_FALLBACK_MODELS',
                    'openai/gpt-oss-120b,qwen/qwen3.8-27b',
                )),
            ))),
            'timeout' => (int) env('CHATBOT_GROQ_TIMEOUT', 20),
        ],
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

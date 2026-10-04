<?php

namespace App\Services\Chatbot;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class GroqProvider implements AIProvider
{
    private const DEFAULT_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct(private readonly ApplicationAIRequestBudget $requestBudget) {}

    public function generate(string $message, array $history, array $context, array $options = []): AIResponse
    {
        $apiKey = trim((string) config('services.chatbot.groq.api_key'));
        $model = trim((string) ($options['model'] ?? config('services.chatbot.groq.model')));
        if ($apiKey === '') {
            throw new AIProviderException('provider_missing_api_key');
        }
        if ($model === '' || preg_match('/\A[A-Za-z0-9][A-Za-z0-9._\/-]{0,100}\z/D', $model) !== 1) {
            throw new AIProviderException('provider_invalid_model_configuration');
        }
        if (! $this->requestBudget->reserve()) {
            throw new AIProviderException('provider_application_rate_limited');
        }

        $endpoint = trim((string) config('services.chatbot.groq.endpoint')) ?: self::DEFAULT_ENDPOINT;
        $messages = [[
            'role' => 'system',
            'content' => ChatbotPrompt::systemInstruction($context),
        ]];
        foreach (array_slice($history, -8) as $turn) {
            $role = ($turn['role'] ?? '') === 'bot' ? 'assistant' : ($turn['role'] ?? '');
            if (! in_array($role, ['user', 'assistant'], true)) {
                continue;
            }
            $content = ChatbotPrompt::sanitizeForProvider((string) ($turn['content'] ?? ''));
            if ($content !== '') {
                $messages[] = [
                    'role' => $role,
                    'content' => mb_substr($content, 0, 1000),
                ];
            }
        }
        $messages[] = [
            'role' => 'user',
            'content' => ChatbotPrompt::sanitizeForProvider($message),
        ];

        $timeout = max(1, min((int) ($options['timeout_seconds'] ?? config('services.chatbot.groq.timeout', 20)), 45));
        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withToken($apiKey)
                ->connectTimeout(min(5, $timeout))
                ->timeout($timeout)
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'max_completion_tokens' => max(
                        64,
                        min((int) ($options['max_output_tokens'] ?? config('services.chatbot.max_output_tokens', 500)), 1000),
                    ),
                    'reasoning_effort' => 'low',
                    'stream' => false,
                ]);
        } catch (ConnectionException) {
            throw new AIProviderException('provider_connection_error');
        }

        if (! $response->successful()) {
            $errorCode = $this->normalizeProviderError($response);
            throw new AIProviderException(
                $errorCode,
                $response->status(),
                $this->retryAfterSeconds($response, $errorCode === 'provider_quota_exhausted'),
            );
        }

        $data = $response->json();
        if (! is_array($data)) {
            throw new AIProviderException('provider_malformed_response', $response->status());
        }

        $text = $data['choices'][0]['message']['content'] ?? null;
        if (! is_string($text) || trim($text) === '') {
            throw new AIProviderException('provider_empty_response', $response->status());
        }
        $text = mb_substr(trim(strip_tags($text)), 0, 2500);
        if ($text === '') {
            throw new AIProviderException('provider_empty_response', $response->status());
        }

        return new AIResponse(
            $text,
            'groq',
            $model,
            is_array($data['usage'] ?? null) ? $data['usage'] : null,
        );
    }

    private function normalizeProviderError(Response $response): string
    {
        $status = $response->status();
        $type = strtolower((string) $response->json('error.type', ''));
        $code = strtolower((string) $response->json('error.code', ''));
        $message = strtolower((string) $response->json('error.message', ''));
        $error = $type.' '.$code.' '.$message;

        if ($status === 429) {
            return preg_match('/requests per day|tokens per day|daily limit|daily quota|per.?day/i', $error)
                ? 'provider_quota_exhausted'
                : 'provider_rate_limited';
        }

        if (str_contains($error, 'model_decommissioned') || str_contains($error, 'model_not_found')) {
            return 'provider_model_not_found';
        }

        return match (true) {
            $status === 400, $status === 413, $status === 422 => 'provider_invalid_request',
            $status === 401 => 'provider_invalid_credentials',
            $status === 403, $status === 424 => 'provider_forbidden',
            $status === 404 => 'provider_model_not_found',
            $status === 409 => 'provider_conflict',
            $status === 408, $status === 504 => 'provider_timeout',
            $status === 499 => 'provider_request_cancelled',
            $status === 498 => 'provider_capacity_exceeded',
            $status >= 500 => 'provider_unavailable',
            default => 'provider_request_failed',
        };
    }

    private function retryAfterSeconds(Response $response, bool $dailyQuota): ?int
    {
        $headers = $dailyQuota
            ? ['x-ratelimit-reset-requests', 'retry-after', 'x-ratelimit-reset-tokens']
            : ['retry-after', 'x-ratelimit-reset-tokens', 'x-ratelimit-reset-requests'];
        foreach ($headers as $header) {
            $value = trim((string) $response->header($header));
            if ($value === '') {
                continue;
            }
            if (ctype_digit($value)) {
                return max(1, min((int) $value, 86400));
            }

            if (preg_match('/^(?:(\d+)h)?(?:(\d+)m)?(?:(\d+(?:\.\d+)?)s)?$/i', $value, $matches) === 1) {
                $seconds = ((int) ($matches[1] ?? 0) * 3600)
                    + ((int) ($matches[2] ?? 0) * 60)
                    + (int) ceil((float) ($matches[3] ?? 0));
                if ($seconds > 0) {
                    return min($seconds, 86400);
                }
            }
        }

        return null;
    }
}

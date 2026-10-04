<?php

namespace App\Services\Chatbot;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final class OpenAICompatibleProvider implements AIProvider
{
    public function generate(string $message, array $history, array $context, array $options = []): AIResponse
    {
        $endpoint = trim((string) config('services.chatbot.endpoint'));
        $model = trim((string) config('services.chatbot.model'));
        $apiKey = trim((string) config('services.chatbot.openai_api_key'))
            ?: trim((string) config('services.chatbot.api_key'));
        $endpoint = $endpoint !== '' ? $endpoint : 'https://api.openai.com/v1/chat/completions';

        if ($endpoint === '' || $model === '' || $apiKey === '') {
            throw new AIProviderException('provider_not_configured');
        }

        $messages = [[
            'role' => 'system',
            'content' => ChatbotPrompt::systemInstruction($context),
        ]];

        foreach ($history as $turn) {
            $role = $turn['role'] === 'bot' ? 'assistant' : $turn['role'];
            if (! in_array($role, ['user', 'assistant'], true)) {
                continue;
            }
            $content = ChatbotPrompt::sanitizeForProvider((string) ($turn['content'] ?? ''));
            if ($content !== '') {
                $messages[] = ['role' => $role, 'content' => mb_substr($content, 0, 1000)];
            }
        }

        $messages[] = ['role' => 'user', 'content' => ChatbotPrompt::sanitizeForProvider($message)];

        try {
            $response = Http::acceptJson()
                ->withToken($apiKey)
                ->connectTimeout(5)
                ->timeout(max(5, min((int) config('services.chatbot.timeout', 20), 45)))
                ->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'max_completion_tokens' => max(64, min((int) ($options['max_output_tokens'] ?? 500), 1000)),
                ]);
        } catch (ConnectionException) {
            throw new AIProviderException('provider_connection_error');
        }

        if (! $response->successful()) {
            throw new AIProviderException(
                $this->normalizeProviderError(
                    $response->status(),
                    (string) $response->json('error.code', ''),
                    (string) $response->json('error.type', ''),
                ),
                $response->status(),
            );
        }

        $text = $response->json('choices.0.message.content');
        if (! is_string($text) || trim($text) === '') {
            throw new AIProviderException('provider_empty_response', $response->status());
        }

        return new AIResponse(
            mb_substr(trim(strip_tags($text)), 0, 2500),
            (string) config('services.chatbot.provider', 'openai-compatible'),
            $model,
            is_array($response->json('usage')) ? $response->json('usage') : null,
        );
    }

    private function normalizeProviderError(int $status, string $code, string $type): string
    {
        $code = strtolower($code.' '.$type);
        if ($status === 429 && (str_contains($code, 'insufficient_quota') || str_contains($code, 'credit_balance_exhausted'))) {
            return 'provider_quota_exhausted';
        }

        return match (true) {
            $status === 401 => 'provider_invalid_credentials',
            $status === 403 => 'provider_forbidden',
            $status === 404 => 'provider_model_or_endpoint_not_found',
            $status === 408, $status === 504 => 'provider_timeout',
            $status === 429 => 'provider_rate_limited',
            $status === 400, $status === 422 => 'provider_invalid_request',
            $status >= 500 => 'provider_unavailable',
            default => 'provider_request_failed',
        };
    }
}

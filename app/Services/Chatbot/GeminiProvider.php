<?php

namespace App\Services\Chatbot;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class GeminiProvider implements AIProvider
{
    private const DEFAULT_ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/interactions';

    public function generate(string $message, array $history, array $context, array $options = []): AIResponse
    {
        return $this->generateForModel(
            trim((string) config('services.chatbot.model')),
            $message,
            $history,
            $context,
            $options,
        );
    }

    public function generateForModel(string $model, string $message, array $history, array $context, array $options = []): AIResponse
    {
        $apiKey = trim((string) config('services.chatbot.api_key'));
        $model = trim($model);
        if ($apiKey === '') {
            throw new AIProviderException('provider_missing_api_key');
        }
        if ($model === '') {
            throw new AIProviderException('provider_missing_model');
        }

        $endpoint = trim((string) config('services.chatbot.endpoint')) ?: self::DEFAULT_ENDPOINT;
        $payload = [
            'model' => $model,
            'input' => ChatbotPrompt::historyInput($message, $history),
            'system_instruction' => ChatbotPrompt::systemInstruction($context),
            'generation_config' => [
                'max_output_tokens' => max(64, min((int) ($options['max_output_tokens'] ?? 500), 1000)),
                'thinking_level' => 'low',
            ],
            'store' => false,
        ];
        $timeout = max(1, min((int) ($options['timeout_seconds'] ?? config('services.chatbot.timeout', 20)), 45));

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withHeaders(['x-goog-api-key' => $apiKey])
                ->connectTimeout(min(5, $timeout))
                ->timeout($timeout)
                ->post($endpoint, $payload);
        } catch (ConnectionException) {
            throw new AIProviderException('provider_connection_error');
        }

        if (! $response->successful()) {
            throw new AIProviderException($this->normalizeProviderError($response), $response->status());
        }

        $data = $response->json();
        if (! is_array($data)) {
            throw new AIProviderException('provider_malformed_response', $response->status());
        }

        $text = $this->responseText($data);
        if ($text === '') {
            $errorCode = $this->responseErrorCode($data);
            throw new AIProviderException($errorCode, $response->status());
        }

        $text = mb_substr(trim(strip_tags($text)), 0, 2500);
        if ($text === '') {
            throw new AIProviderException('provider_empty_response', $response->status());
        }

        return new AIResponse(
            $text,
            'gemini',
            $model,
            is_array($data['usage'] ?? null) ? $data['usage'] : null,
        );
    }

    private function responseText(array $data): string
    {
        $text = is_string($data['output_text'] ?? null) ? trim($data['output_text']) : '';
        if ($text !== '') {
            return $text;
        }

        foreach (['outputs', 'output', 'steps'] as $key) {
            if (is_string($data[$key] ?? null)) {
                $text .= ($text === '' ? '' : "\n").$data[$key];

                continue;
            }
            if (! is_array($data[$key] ?? null)) {
                continue;
            }

            foreach ($data[$key] as $output) {
                if (! is_array($output)) {
                    continue;
                }

                $type = $output['type'] ?? '';
                if ($type !== '' && ! in_array($type, ['model_output', 'text', 'message'], true)) {
                    continue;
                }

                if (is_string($output['text'] ?? null)) {
                    $text .= ($text === '' ? '' : "\n").$output['text'];

                    continue;
                }

                $content = $output['content'] ?? null;
                if (is_string($content)) {
                    $text .= ($text === '' ? '' : "\n").$content;

                    continue;
                }
                if (! is_array($content)) {
                    continue;
                }

                foreach ($content as $part) {
                    if (is_array($part) && ($part['type'] ?? 'text') === 'text' && is_string($part['text'] ?? null)) {
                        $text .= ($text === '' ? '' : "\n").$part['text'];
                    }
                }
            }
        }

        return trim($text);
    }

    private function responseErrorCode(array $data): string
    {
        $status = strtolower((string) ($data['status'] ?? ''));
        $reason = strtolower((string) ($data['error']['status'] ?? $data['error']['code'] ?? ''));
        if (str_contains($status.' '.$reason, 'block') || str_contains($status.' '.$reason, 'safety')) {
            return 'provider_blocked_response';
        }

        return 'provider_empty_response';
    }

    private function normalizeProviderError(Response $response): string
    {
        $status = $response->status();
        $providerCode = strtolower((string) $response->json('error.code', ''));
        $providerStatus = strtolower((string) $response->json('error.status', ''));
        $message = strtolower((string) $response->json('error.message', ''));
        $details = json_encode($response->json('error.details', []), JSON_INVALID_UTF8_SUBSTITUTE) ?: '';
        $error = $providerCode.' '.$providerStatus.' '.$message.' '.strtolower($details);

        if (str_contains($error, 'quota_exceeded') || ($status === 429 && preg_match('/daily|per.?day|requestsperday/i', $error))) {
            return 'provider_quota_exhausted';
        }
        if (str_contains($error, 'rate_limit_exceeded') || str_contains($error, 'too_many_requests')
            || ($status === 429 && preg_match('/per.?minute|per.?second|requestsperminute|tokensperminute/i', $error))) {
            return 'provider_rate_limited';
        }
        if ($status === 429 && preg_match('/quota.{0,30}(?:exhaust|exceed)|exceed.{0,30}quota/i', $error)) {
            return 'provider_quota_exhausted';
        }
        if (str_contains($error, 'failed_precondition')) {
            return 'provider_failed_precondition';
        }
        if (str_contains($error, 'invalid_request') || str_contains($error, 'parameter_unknown') || str_contains($error, 'out_of_range')) {
            return 'provider_invalid_request';
        }
        if (str_contains($error, 'model_not_found')) {
            return 'provider_model_not_found';
        }
        if (str_contains($error, 'not_found')) {
            return 'provider_endpoint_not_found';
        }
        if (str_contains($error, 'authentication')) {
            return 'provider_invalid_credentials';
        }
        if (str_contains($error, 'permission_denied')) {
            return 'provider_forbidden';
        }
        if (str_contains($error, 'payment_required')) {
            return 'provider_payment_required';
        }
        if (str_contains($error, 'service_unavailable') || str_contains($error, 'api_error')) {
            return 'provider_unavailable';
        }
        if (str_contains($error, 'deadline_exceeded')) {
            return 'provider_timeout';
        }
        if (str_contains($error, 'cancelled')) {
            return 'provider_request_cancelled';
        }
        if (str_contains($error, 'unimplemented')) {
            return 'provider_unsupported';
        }
        if (str_contains($error, 'quota_exhausted') || $status === 402) {
            return 'provider_payment_required';
        }

        return match (true) {
            $status === 400, $status === 416 => 'provider_invalid_request',
            $status === 401 => 'provider_invalid_credentials',
            $status === 402 => 'provider_payment_required',
            $status === 403 => 'provider_forbidden',
            $status === 404 => 'provider_model_not_found',
            $status === 408, $status === 504 => 'provider_timeout',
            $status === 429 => 'provider_rate_limited',
            $status === 499 => 'provider_request_cancelled',
            $status === 501 => 'provider_unsupported',
            $status >= 500 => 'provider_unavailable',
            default => 'provider_request_failed',
        };
    }
}

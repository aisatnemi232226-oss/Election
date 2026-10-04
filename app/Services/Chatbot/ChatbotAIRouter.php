<?php

namespace App\Services\Chatbot;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class ChatbotAIRouter implements AIProvider
{
    private const RETRYABLE_PROVIDER_ERRORS = [
        'provider_quota_exhausted',
        'provider_rate_limited',
        'provider_capacity_exceeded',
        'provider_conflict',
        'provider_unavailable',
        'provider_timeout',
        'provider_connection_error',
        'provider_model_not_found',
        'provider_empty_response',
        'provider_malformed_response',
        'provider_invalid_credentials',
        'provider_forbidden',
        'provider_missing_api_key',
        'provider_missing_model',
        'provider_invalid_model_configuration',
        'provider_all_models_busy',
        'provider_all_models_cooling_down',
    ];

    public function __construct(
        private readonly GroqProvider $groq,
        private readonly GeminiModelRouter $gemini,
    ) {}

    public function generate(string $message, array $history, array $context, array $options = []): AIResponse
    {
        $providers = $this->configuredProviders();
        if ($providers === []) {
            throw new AIProviderException('provider_invalid_provider_configuration');
        }

        $limit = max(1, min((int) config('services.chatbot.max_providers_per_request', 2), 3));
        $providers = array_slice($providers, 0, $limit);
        $maxAttempts = max(1, min((int) config('services.chatbot.max_attempts_per_request', 3), 5));
        $attempts = 0;
        $startedAt = microtime(true);
        $maxDuration = max(1, min((int) config('services.chatbot.max_total_seconds', 35), 120));
        $lastError = null;
        $coolingDown = 0;
        $unconfigured = 0;

        foreach ($providers as $index => $provider) {
            if (! $this->isConfigured($provider)) {
                $unconfigured++;

                continue;
            }

            if ($provider === 'gemini' && $this->isCoolingDown($provider)) {
                $coolingDown++;

                continue;
            }

            $remaining = $maxDuration - (microtime(true) - $startedAt);
            if ($remaining < 1 || $attempts >= $maxAttempts) {
                break;
            }
            $providerTimeout = $provider === 'groq'
                ? config('services.chatbot.groq.timeout', 20)
                : config('services.chatbot.timeout', 20);
            $attemptOptions = array_merge($options, [
                'timeout_seconds' => max(1, min(
                    (int) $providerTimeout,
                    (int) ceil($remaining),
                )),
                'max_total_seconds' => max(1, (int) ceil($remaining)),
                'max_models_per_request' => min(
                    max(1, (int) config('services.chatbot.max_models_per_request', 3)),
                    $maxAttempts - $attempts,
                ),
            ]);

            try {
                if ($provider === 'groq') {
                    $response = $this->generateWithGroqModels(
                        $message,
                        $history,
                        $context,
                        $attemptOptions,
                        $attempts,
                        $maxAttempts,
                        $maxDuration,
                        $startedAt,
                        $index,
                    );
                } else {
                    $attempts++;
                    $response = $this->gemini->generate($message, $history, $context, $attemptOptions);
                    Cache::forget($this->cooldownKey($provider));
                }

                return $response;
            } catch (AIProviderException $exception) {
                $lastError = $exception;
                if ($provider === 'gemini') {
                    $this->recordFailure($provider, $exception);
                    Log::warning('A configured PCU Assistant provider failed.', [
                        'provider' => $provider,
                        'model' => (string) config('services.chatbot.model'),
                        'error_code' => $exception->errorCode,
                        'provider_status' => $exception->providerStatus,
                        'retry_after_seconds' => $exception->retryAfterSeconds,
                        'attempt' => $index + 1,
                        'fallback_used' => $index > 0,
                    ]);
                }

                if (in_array($exception->errorCode, [
                    'provider_application_rate_limited',
                    'provider_invalid_request',
                    'provider_invalid_provider_configuration',
                ], true)) {
                    throw $exception;
                }
                if (! in_array($exception->errorCode, self::RETRYABLE_PROVIDER_ERRORS, true)) {
                    throw $exception;
                }
            }
        }

        if ($lastError !== null) {
            throw $lastError;
        }
        if ($coolingDown === count($providers)) {
            throw new AIProviderException('provider_all_providers_cooling_down');
        }
        if ($unconfigured === count($providers)) {
            throw new AIProviderException('provider_missing_api_key');
        }

        throw new AIProviderException('provider_all_providers_unavailable');
    }

    /**
     * @return list<'groq'|'gemini'>
     */
    private function configuredProviders(): array
    {
        $primary = strtolower(trim((string) config('services.chatbot.primary_provider', 'groq')));
        $fallbacks = config('services.chatbot.fallback_providers', ['gemini']);
        if (! in_array($primary, ['groq', 'gemini'], true) || ! is_array($fallbacks)) {
            throw new AIProviderException('provider_invalid_provider_configuration');
        }

        $providers = [$primary];
        foreach ($fallbacks as $fallback) {
            if (! is_string($fallback) || ! in_array(strtolower(trim($fallback)), ['groq', 'gemini'], true)) {
                throw new AIProviderException('provider_invalid_provider_configuration');
            }
            $fallback = strtolower(trim($fallback));
            if (! in_array($fallback, $providers, true)) {
                $providers[] = $fallback;
            }
        }

        return $providers;
    }

    private function generateWithGroqModels(
        string $message,
        array $history,
        array $context,
        array $options,
        int &$attempts,
        int $maxAttempts,
        int $maxDuration,
        float $startedAt,
        int $providerIndex,
    ): AIResponse {
        $models = $this->configuredGroqModels();
        $lastError = null;
        $coolingDown = 0;
        if ($this->isCoolingDown('groq')) {
            throw new AIProviderException('provider_all_models_cooling_down');
        }

        foreach ($models as $model) {
            if ($attempts >= $maxAttempts || $maxDuration - (microtime(true) - $startedAt) < 1) {
                break;
            }
            if ($this->isCoolingDown('groq', $model)) {
                $coolingDown++;

                continue;
            }

            $modelOptions = array_merge($options, [
                'model' => $model,
                'timeout_seconds' => max(1, min(
                    (int) ($options['timeout_seconds'] ?? config('services.chatbot.groq.timeout', 20)),
                    (int) ceil($maxDuration - (microtime(true) - $startedAt)),
                )),
            ]);

            try {
                $attempts++;
                $response = $this->groq->generate($message, $history, $context, $modelOptions);
                Cache::forget($this->cooldownKey('groq', $model));

                return $response;
            } catch (AIProviderException $exception) {
                $lastError = $exception;
                $this->recordFailure('groq', $exception, $model);
                Log::warning('A configured PCU Assistant provider model failed.', [
                    'provider' => 'groq',
                    'model' => $model,
                    'error_code' => $exception->errorCode,
                    'provider_status' => $exception->providerStatus,
                    'retry_after_seconds' => $exception->retryAfterSeconds,
                    'attempt' => $attempts,
                    'fallback_used' => $providerIndex > 0 || $attempts > 1,
                ]);

                if (in_array($exception->errorCode, [
                    'provider_application_rate_limited',
                    'provider_invalid_request',
                    'provider_invalid_provider_configuration',
                    'provider_invalid_credentials',
                    'provider_forbidden',
                ], true)) {
                    if (in_array($exception->errorCode, [
                        'provider_invalid_credentials',
                        'provider_forbidden',
                    ], true)) {
                        $this->recordFailure('groq', $exception);
                    }
                    break;
                }
                if (! in_array($exception->errorCode, self::RETRYABLE_PROVIDER_ERRORS, true)) {
                    break;
                }
            }
        }

        if ($lastError !== null) {
            throw $lastError;
        }
        if ($coolingDown === count($models)) {
            throw new AIProviderException('provider_all_models_cooling_down');
        }

        throw new AIProviderException('provider_all_models_unavailable');
    }

    /**
     * @return list<string>
     */
    private function configuredGroqModels(): array
    {
        $primary = trim((string) config('services.chatbot.groq.model', ''));
        $fallbacks = config('services.chatbot.groq.fallback_models', []);
        if (! is_array($fallbacks)) {
            throw new AIProviderException('provider_invalid_provider_configuration');
        }

        $models = $primary === '' ? [] : [$primary];
        foreach ($fallbacks as $fallback) {
            if (! is_string($fallback) || trim($fallback) === '') {
                throw new AIProviderException('provider_invalid_provider_configuration');
            }
            $fallback = trim($fallback);
            if (! in_array($fallback, $models, true)) {
                $models[] = $fallback;
            }
        }

        return $models;
    }

    private function isConfigured(string $provider): bool
    {
        return match ($provider) {
            'groq' => trim((string) config('services.chatbot.groq.api_key')) !== ''
                && trim((string) config('services.chatbot.groq.model')) !== '',
            'gemini' => trim((string) config('services.chatbot.api_key')) !== ''
                && trim((string) config('services.chatbot.model')) !== '',
        };
    }

    private function isCoolingDown(string $provider, ?string $model = null): bool
    {
        return (int) Cache::get($this->cooldownKey($provider, $model), 0) > time();
    }

    private function recordFailure(string $provider, AIProviderException $exception, ?string $model = null): void
    {
        $seconds = match ($exception->errorCode) {
            'provider_quota_exhausted' => $exception->retryAfterSeconds ?? $this->secondsUntilQuotaReset(),
            'provider_rate_limited' => $exception->retryAfterSeconds ?? 30,
            'provider_model_not_found' => 1800,
            'provider_invalid_credentials', 'provider_forbidden', 'provider_missing_api_key', 'provider_missing_model' => 600,
            'provider_capacity_exceeded', 'provider_conflict', 'provider_unavailable', 'provider_timeout', 'provider_connection_error' => 15,
            'provider_empty_response', 'provider_malformed_response' => 15,
            default => 0,
        };
        if ($seconds > 0) {
            $key = $this->cooldownKey($provider, $model);
            Cache::put($key, time() + min($seconds, 86400), min($seconds, 86400));
        }
    }

    private function cooldownKey(string $provider, ?string $model = null): string
    {
        if ($model !== null) {
            return 'chatbot:ai:provider-cooldown:'.$provider.':'.hash('sha256', $model);
        }

        return 'chatbot:ai:provider-cooldown:'.$provider;
    }

    private function secondsUntilQuotaReset(): int
    {
        $now = CarbonImmutable::now('America/Los_Angeles');

        return max(60, (int) $now->diffInSeconds($now->startOfDay()->addDay()));
    }
}

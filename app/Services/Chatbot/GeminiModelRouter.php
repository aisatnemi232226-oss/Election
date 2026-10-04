<?php

namespace App\Services\Chatbot;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class GeminiModelRouter implements AIProvider
{
    private const DAILY_QUOTA = 'provider_quota_exhausted';

    private const RETRYABLE_MODEL_ERRORS = [
        'provider_quota_exhausted',
        'provider_rate_limited',
        'provider_unavailable',
        'provider_timeout',
        'provider_connection_error',
        'provider_model_not_found',
        'provider_empty_response',
        'provider_malformed_response',
    ];

    public function __construct(
        private readonly GeminiProvider $provider,
        private readonly ApplicationAIRequestBudget $requestBudget,
    ) {}

    public function generate(string $message, array $history, array $context, array $options = []): AIResponse
    {
        if (trim((string) config('services.chatbot.api_key')) === '') {
            throw new AIProviderException('provider_missing_api_key');
        }

        $models = $this->configuredModels();
        if ($models === []) {
            throw new AIProviderException('provider_missing_model');
        }
        if (! $this->validModels($models)) {
            throw new AIProviderException('provider_invalid_model_configuration');
        }

        $maxModels = max(1, min(
            (int) ($options['max_models_per_request'] ?? config('services.chatbot.max_models_per_request', 3)),
            5,
        ));
        $models = array_slice($models, 0, $maxModels);
        $maxDuration = max(1, min(
            (int) ($options['max_total_seconds'] ?? config('services.chatbot.max_total_seconds', 35)),
            120,
        ));
        $providerTimeout = max(1, min(
            (int) ($options['timeout_seconds'] ?? config('services.chatbot.timeout', 20)),
            45,
        ));
        $startedAt = microtime(true);
        $lastError = null;
        $busyModels = 0;

        foreach ($models as $index => $model) {
            if ($this->isCoolingDown($model)) {
                continue;
            }

            $remaining = $maxDuration - (microtime(true) - $startedAt);
            if ($remaining < 1) {
                break;
            }

            $timeout = max(1, min($providerTimeout, (int) ceil($remaining)));
            $lock = Cache::lock($this->modelLockKey($model), $timeout + 5);
            if (! $lock->get()) {
                $busyModels++;

                continue;
            }

            try {
                if (! $this->requestBudget->reserve()) {
                    throw new AIProviderException('provider_application_rate_limited');
                }

                try {
                    $response = $this->provider->generateForModel(
                        $model,
                        $message,
                        $history,
                        $context,
                        array_merge($options, ['timeout_seconds' => $timeout]),
                    );
                    $this->markHealthy($model);

                    return $response;
                } catch (AIProviderException $exception) {
                    $lastError = $exception;
                    if (in_array($exception->errorCode, self::RETRYABLE_MODEL_ERRORS, true)) {
                        $this->markUnhealthy($model, $exception->errorCode);
                    }

                    Log::warning('A configured Gemini model request failed.', [
                        'provider' => 'gemini',
                        'model' => $model,
                        'error_code' => $exception->errorCode,
                        'provider_status' => $exception->providerStatus,
                        'attempt' => $index + 1,
                        'fallback_used' => $index > 0,
                    ]);

                    if (! in_array($exception->errorCode, self::RETRYABLE_MODEL_ERRORS, true)) {
                        throw $exception;
                    }
                }
            } finally {
                $lock->release();
            }
        }

        if ($lastError !== null) {
            throw $lastError;
        }
        if ($busyModels > 0) {
            throw new AIProviderException('provider_all_models_busy');
        }

        throw new AIProviderException('provider_all_models_cooling_down');
    }

    /**
     * @return list<string>
     */
    private function configuredModels(): array
    {
        $primary = trim((string) config('services.chatbot.model'));
        $fallbacks = config('services.chatbot.fallback_models', []);
        if (! is_array($fallbacks)) {
            throw new AIProviderException('provider_invalid_model_configuration');
        }

        $models = $primary === '' ? [] : [$primary];
        foreach ($fallbacks as $fallback) {
            if (! is_string($fallback)) {
                throw new AIProviderException('provider_invalid_model_configuration');
            }
            $fallback = trim($fallback);
            if ($fallback !== '' && ! in_array($fallback, $models, true)) {
                $models[] = $fallback;
            }
        }

        return $models;
    }

    /**
     * @param  list<string>  $models
     */
    private function validModels(array $models): bool
    {
        foreach ($models as $model) {
            if (preg_match('/\Agemini-[A-Za-z0-9][A-Za-z0-9._-]{0,80}\z/D', $model) !== 1) {
                return false;
            }
        }

        return true;
    }

    private function isCoolingDown(string $model): bool
    {
        $health = Cache::get($this->healthKey($model));

        return is_array($health) && (int) ($health['cooldown_until'] ?? 0) > time();
    }

    private function markHealthy(string $model): void
    {
        $previous = Cache::get($this->healthKey($model), []);
        Cache::put($this->healthKey($model), [
            'state' => 'available',
            'last_success_at' => now()->toIso8601String(),
            'last_failure_at' => is_array($previous) ? ($previous['last_failure_at'] ?? null) : null,
            'failure_count' => 0,
            'last_error_category' => null,
            'cooldown_until' => 0,
        ], 21600);
    }

    private function markUnhealthy(string $model, string $errorCode): void
    {
        $previous = Cache::get($this->healthKey($model), []);
        $cooldown = match ($errorCode) {
            self::DAILY_QUOTA => $this->secondsUntilQuotaReset(),
            'provider_rate_limited' => 30,
            'provider_model_not_found' => 1800,
            default => 15,
        };
        $now = time();
        Cache::put($this->healthKey($model), [
            'state' => match ($errorCode) {
                self::DAILY_QUOTA => 'quota_exhausted',
                'provider_rate_limited' => 'rate_limited',
                default => 'temporarily_unavailable',
            },
            'last_success_at' => is_array($previous) ? ($previous['last_success_at'] ?? null) : null,
            'last_failure_at' => now()->toIso8601String(),
            'failure_count' => (is_array($previous) ? (int) ($previous['failure_count'] ?? 0) : 0) + 1,
            'last_error_category' => $errorCode,
            'cooldown_until' => $now + $cooldown,
        ], $cooldown);
    }

    private function secondsUntilQuotaReset(): int
    {
        $now = CarbonImmutable::now('America/Los_Angeles');
        $reset = $now->startOfDay()->addDay();

        return max(60, (int) $now->diffInSeconds($reset));
    }

    private function healthKey(string $model): string
    {
        return 'chatbot:gemini:model-health:'.hash('sha256', $model);
    }

    private function modelLockKey(string $model): string
    {
        return 'chatbot:gemini:model-lock:'.hash('sha256', $model);
    }
}

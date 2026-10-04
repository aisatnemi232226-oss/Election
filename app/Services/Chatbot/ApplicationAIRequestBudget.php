<?php

namespace App\Services\Chatbot;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

final class ApplicationAIRequestBudget
{
    public function reserve(): bool
    {
        $limit = max(1, min((int) config('services.chatbot.max_requests_per_minute', 10), 1000));
        $limiterKey = 'chatbot:ai:outbound-budget';
        $lock = Cache::lock('chatbot:ai:outbound-budget-lock', 5);
        if (! $lock->get()) {
            return false;
        }

        try {
            if (RateLimiter::tooManyAttempts($limiterKey, $limit)) {
                return false;
            }
            RateLimiter::hit($limiterKey, 60);

            return true;
        } finally {
            $lock->release();
        }
    }
}

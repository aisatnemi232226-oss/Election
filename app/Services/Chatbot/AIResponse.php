<?php

namespace App\Services\Chatbot;

final readonly class AIResponse
{
    public function __construct(
        public string $text,
        public string $provider,
        public string $model,
        public ?array $usage = null,
    ) {}
}

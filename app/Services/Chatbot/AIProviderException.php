<?php

namespace App\Services\Chatbot;

use RuntimeException;

final class AIProviderException extends RuntimeException
{
    public function __construct(
        public readonly string $errorCode,
        public readonly ?int $providerStatus = null,
        public readonly ?int $retryAfterSeconds = null,
    ) {
        parent::__construct($errorCode);
    }
}

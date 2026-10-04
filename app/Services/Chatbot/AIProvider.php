<?php

namespace App\Services\Chatbot;

interface AIProvider
{
    public function generate(string $message, array $history, array $context, array $options = []): AIResponse;
}

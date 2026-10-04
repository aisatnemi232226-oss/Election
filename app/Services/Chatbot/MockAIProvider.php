<?php

namespace App\Services\Chatbot;

final class MockAIProvider implements AIProvider
{
    public function generate(string $message, array $history, array $context, array $options = []): AIResponse
    {
        return new AIResponse(
            'The chatbot mock provider is active. Configure a real AI provider to receive generated answers.',
            'mock',
            'mock',
        );
    }
}

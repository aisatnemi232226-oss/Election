<?php

namespace App\Http\Controllers;

use App\Models\ChatbotIntent;
use App\Models\ChatbotLog;
use App\Models\ChatbotSetting;
use App\Services\Chatbot\AIProvider;
use App\Services\Chatbot\AIProviderException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    private const DEFAULT_GREETING = "Hello! I'm the PCU Assistant. I can help with signing in, registration, voting, and election results.";

    private const DEFAULT_FALLBACK = "I don't have an answer for that yet. Try asking about signing in, voter or candidate registration, verification, voting, or results.";

    private const DEFAULT_QUICK_REPLIES = [
        'How do I log in?',
        'How do I vote?',
        'How do I register as a candidate?',
        'Where can I see results?',
    ];

    public function __construct(private readonly AIProvider $aiProvider) {}

    public function respond(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['sometimes', 'array', 'max:8'],
            'history.*.role' => ['required', 'string', 'in:user,assistant,bot'],
            'history.*.content' => ['required', 'string', 'max:2500'],
        ]);

        $message = trim($validated['message']);
        $normalizedMessage = $this->normalize($message);
        $history = array_map(static function (array $turn): array {
            return [
                'role' => $turn['role'] === 'bot' ? 'assistant' : $turn['role'],
                'content' => trim($turn['content']),
            ];
        }, $validated['history'] ?? []);

        $settings = ChatbotSetting::query()
            ->whereIn('key', ['greeting', 'fallback', 'default_quick_replies'])
            ->pluck('value', 'key');

        $quickReplies = $this->splitLinesCsv((string) ($settings['default_quick_replies'] ?? ''));
        if ($quickReplies === []) {
            $quickReplies = self::DEFAULT_QUICK_REPLIES;
        }

        $greeting = trim((string) ($settings['greeting'] ?? ''));
        if ($greeting === '' || $this->hasConflictingGreetingParts($greeting)) {
            $greeting = self::DEFAULT_GREETING;
        }

        $fallback = trim((string) ($settings['fallback'] ?? ''));
        if ($fallback === '') {
            $fallback = self::DEFAULT_FALLBACK;
        }

        $customIntents = ChatbotIntent::query()
            ->where('is_active', true)
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        $matchedIntentId = null;
        $reply = '';
        $mode = 'knowledge';
        $notice = null;

        if (in_array($normalizedMessage, ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening'], true)) {
            $reply = $greeting;
        } else {
            foreach ($customIntents as $intent) {
                if ($this->matchesIntent($normalizedMessage, (string) $intent->name, $intent->triggers)) {
                    $reply = trim((string) $intent->reply);
                    $matchedIntentId = $intent->id;
                    $intentReplies = is_array($intent->quick_replies) ? $intent->quick_replies : [];
                    if ($intentReplies !== []) {
                        $quickReplies = $intentReplies;
                    }
                    break;
                }
            }

            if ($reply === '') {
                foreach ($this->defaultIntents() as $intent) {
                    if ($this->matchesIntent($normalizedMessage, $intent['name'], $intent['triggers'])) {
                        $reply = $intent['reply'];
                        break;
                    }
                }
            }

            $provider = config('services.chatbot.provider');
            $aiIsConfigured = (bool) config('services.chatbot.ai_enabled');
            if ($reply === '' && $aiIsConfigured) {
                $startedAt = microtime(true);
                try {
                    $facts = collect($this->defaultIntents())
                        ->map(static fn (array $intent) => $intent['name'].': '.$intent['reply'])
                        ->concat($customIntents->map(static fn (ChatbotIntent $intent) => (string) $intent->name.': '.(string) $intent->reply))
                        ->take(40)
                        ->values()
                        ->all();

                    $response = $this->aiProvider->generate($message, $history, [
                        'approved_facts' => $facts,
                    ], ['max_output_tokens' => config('services.chatbot.max_output_tokens', 500)]);
                    $reply = $response->text;
                    $mode = 'ai';
                    Log::info('The PCU Assistant AI provider request succeeded.', [
                        'provider' => $response->provider,
                        'model' => $response->model,
                        'elapsed_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                        'fallback_used' => config('services.chatbot.provider') === 'router'
                            ? $response->provider !== config('services.chatbot.primary_provider')
                            : $response->model !== trim((string) config('services.chatbot.model')),
                    ]);
                } catch (AIProviderException $exception) {
                    Log::warning('The PCU Assistant AI provider request failed.', [
                        'provider' => (string) config('services.chatbot.provider'),
                        'model' => mb_substr((string) config('services.chatbot.model'), 0, 80),
                        'error_code' => $exception->errorCode,
                        'provider_status' => $exception->providerStatus,
                        'elapsed_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                        'fallback_used' => true,
                    ]);
                    $notice = $this->providerNotice($exception->errorCode);
                } catch (\Throwable $exception) {
                    Log::warning('The PCU Assistant encountered an unexpected provider error.', [
                        'provider' => (string) config('services.chatbot.provider'),
                        'model' => mb_substr((string) config('services.chatbot.model'), 0, 80),
                        'exception_class' => $exception::class,
                        'elapsed_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                        'fallback_used' => true,
                    ]);
                    $notice = 'AI replies are temporarily unavailable. You can still use the common election help topics.';
                }
            }
        }

        if ($reply === '') {
            $reply = $fallback;
        }

        $this->logChat($request, $message, $reply, $matchedIntentId);

        return response()->json([
            'reply' => $reply,
            'quick_replies' => $quickReplies,
            'mode' => $mode,
            'notice' => $notice,
        ]);
    }

    private function providerNotice(string $errorCode): string
    {
        return match ($errorCode) {
            'provider_quota_exhausted' => 'AI quota is temporarily unavailable. Common election help is still available; please try one of the suggested questions.',
            'provider_payment_required' => 'AI credits are currently unavailable. Common election help is still available; please try one of the suggested questions.',
            'provider_rate_limited' => 'The AI assistant is receiving too many requests right now. Please wait a moment and try again.',
            'provider_invalid_credentials', 'provider_forbidden', 'provider_not_configured', 'provider_missing_api_key', 'provider_missing_model' => 'AI replies are not available right now. Common election help is still available.',
            'provider_timeout', 'provider_connection_error' => 'The AI assistant could not connect in time. Please try again in a moment.',
            'provider_blocked_response' => 'I could not provide an AI reply to that message. Please ask a PCU election help question instead.',
            'provider_application_rate_limited', 'provider_all_models_busy', 'provider_all_models_cooling_down', 'provider_all_providers_cooling_down', 'provider_all_providers_unavailable' => 'AI assistance is temporarily unavailable. You can still use the election help options below.',
            default => 'AI replies are temporarily unavailable. Common election help is still available.',
        };
    }

    private function defaultIntents(): array
    {
        return [
            [
                'name' => 'Change a submitted vote',
                'triggers' => ['change my vote', 'edit my vote', 'change my ballot', 'edit my ballot'],
                'reply' => 'Please review your choices before submitting your ballot. The system accepts one vote per student; if you submitted the wrong ballot, contact the election office for help.',
            ],
            [
                'name' => 'Sign in and account recovery',
                'triggers' => ['log in', 'login', 'sign in', 'forgot password', 'reset password', 'access my account'],
                'reply' => 'Use the Log in page with your student account. If you forgot your password, choose Forgot password on the login page to begin account recovery.',
            ],
            [
                'name' => 'Voter verification',
                'triggers' => ['verification', 'verify my account', 'verify my voter account', 'verified voter', 'student verification'],
                'reply' => 'Open your account menu and choose the voter verification option. Submit your student details, then return to your account to check the application status.',
            ],
            [
                'name' => 'Who can vote',
                'triggers' => ['who can vote', 'eligible to vote', 'eligible voter', 'voting eligibility'],
                'reply' => 'Eligible, verified students can access the ballot using their student account.',
            ],
            [
                'name' => 'Candidate registration',
                'triggers' => ['register as a candidate', 'register as candidate', 'candidate registration', 'run for office', 'become a candidate'],
                'reply' => 'Choose Register from the site navigation, select candidate registration, and follow the application steps. Contact the election office if you need help with your application.',
            ],
            [
                'name' => 'Voter registration',
                'triggers' => ['register as a voter', 'voter registration', 'register to vote', 'create an account', 'sign up'],
                'reply' => 'Choose Register from the site navigation and select voter registration. Sign in afterward and complete voter verification to access the ballot.',
            ],
            [
                'name' => 'Cast a vote',
                'triggers' => ['how do i vote', 'how can i vote', 'cast my ballot', 'submit my ballot', 'voting', 'vote'],
                'reply' => 'Sign in with your student account and make sure your voter account is verified. Open Vote, select your candidates, review your choices, and submit your ballot. Each student may vote only once.',
            ],
            [
                'name' => 'Election results',
                'triggers' => ['where can i see results', 'election results', 'vote count', 'who won', 'results', 'result'],
                'reply' => 'Open Results from the site navigation. Results are available there when they have been published.',
            ],
            [
                'name' => 'Election schedule',
                'triggers' => ['when is the election', 'when does voting start', 'election date', 'voting dates', 'election schedule'],
                'reply' => 'Check the Election timeline and News and events sections on the home page for the currently published election dates and announcements.',
            ],
            [
                'name' => 'Contact the election office',
                'triggers' => ['contact the election office', 'contact support', 'need help', 'contact us'],
                'reply' => 'Visit the Contact page to reach the election office. Include a short description of the issue so they can direct your request.',
            ],
        ];
    }

    private function matchesIntent(string $message, string $name, mixed $triggers): bool
    {
        if ($message === '') {
            return false;
        }

        $normalizedName = $this->normalize($name);
        if ($normalizedName !== '' && $normalizedName === $message) {
            return true;
        }

        $phrases = is_array($triggers) ? $triggers : [];
        if ($phrases === [] && trim($name) !== '') {
            $phrases[] = $name;
        }

        $paddedMessage = ' '.$message.' ';
        foreach ($phrases as $phrase) {
            $needle = $this->normalize((string) $phrase);
            if ($needle !== '' && str_contains($paddedMessage, ' '.$needle.' ')) {
                return true;
            }
        }

        return false;
    }

    private function splitLinesCsv(string $raw): array
    {
        $parts = preg_split('/[\n,]+/', trim($raw)) ?: [];

        return array_values(array_unique(array_filter(array_map(
            static fn ($part) => trim((string) $part),
            $parts
        ), static fn ($part) => $part !== '')));
    }

    private function hasConflictingGreetingParts(string $greeting): bool
    {
        preg_match_all('/\b(?:morning|afternoon|evening)\b/i', $greeting, $matches);

        return count(array_unique(array_map('strtolower', $matches[0] ?? []))) > 1;
    }

    private function logChat(Request $request, string $message, string $reply, ?int $matchedIntentId): void
    {
        if (! config('services.chatbot.log_conversations', false)) {
            return;
        }

        try {
            $retentionDays = max(1, (int) config('services.chatbot.log_retention_days', 30));
            ChatbotLog::query()
                ->where('created_at', '<', now()->subDays($retentionDays))
                ->delete();

            ChatbotLog::query()->create([
                'user_id' => $request->user()?->id,
                'session_id' => (string) ($request->session()->getId() ?? ''),
                'message' => $this->redactSensitiveText($message),
                'reply' => $this->redactSensitiveText($reply),
                'matched_intent_id' => $matchedIntentId,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Unable to save a chatbot conversation log.', [
                'exception_class' => $exception::class,
            ]);
        }
    }

    private function redactSensitiveText(string $text): string
    {
        $patterns = [
            '/\b(password|passcode|otp|one[- ]time code|verification code|token)\b\s*[:=]?\s*\S+/iu',
            '/\b\d{6,}\b/u',
        ];

        return mb_substr(trim(preg_replace($patterns, '[redacted]', strip_tags($text)) ?? '[redacted]'), 0, 2000);
    }

    private function normalize(string $text): string
    {
        $normalized = mb_strtolower(trim($text));
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $normalized) ?? $normalized;

        return trim(preg_replace('/\s+/u', ' ', $normalized) ?? $normalized);
    }
}

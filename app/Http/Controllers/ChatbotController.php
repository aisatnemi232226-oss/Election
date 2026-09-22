<?php

namespace App\Http\Controllers;

use App\Models\ChatbotIntent;
use App\Models\ChatbotLog;
use App\Models\ChatbotSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function respond(Request $request): JsonResponse
    {
        $message = trim((string) $request->input('message', ''));
        $text = strtolower($message);

        $normalizedText = $this->normalize($text);

        $settings = ChatbotSetting::query()
            ->whereIn('key', ['greeting', 'fallback', 'default_quick_replies'])
            ->pluck('value', 'key');

        $quickRepliesRaw = (string) ($settings['default_quick_replies'] ?? '');
        $quickReplies = $this->splitLinesCsv($quickRepliesRaw);
        if (empty($quickReplies)) {
            $quickReplies = $this->intentNameSuggestions();
        }

        $greeting = (string) ($settings['greeting'] ?? "Hello! You can ask me how to log in, how to vote, or where to see the election results.");
        $fallback = (string) ($settings['fallback'] ?? "I couldn't find an exact match for that. Try asking about login, voting, candidate registration, or results.");

        $isGibberish = function (string $input): bool {
            $t = trim($input);
            if ($t === '' || mb_strlen($t) < 2) {
                return true;
            }

            $stripped = preg_replace('/\s+/', '', $t) ?? '';
            $lettersOnly = preg_match('/^[a-zA-Z]+$/', $stripped) === 1;

            if ($lettersOnly && mb_strlen($stripped) >= 8) {
                $lower = strtolower($stripped);
                $uniqueChars = count(array_unique(str_split($lower)));
                if ($uniqueChars <= 3) {
                    return true;
                }
                if (preg_match('/^(.{1,3})\1+$/', $lower) === 1) {
                    return true;
                }
            }

            $meaningfulChars = preg_match_all('/[a-zA-Z0-9]/', $t);
            if (mb_strlen($t) >= 6 && $meaningfulChars <= 2) {
                return true;
            }

            return false;
        };

        if ($isGibberish($message)) {
            $reply = "I couldn't understand that. Please try a clear question or tap one of the suggestions below.";
            $this->logChat($request, $message, $reply, null);
            return response()->json(['reply' => $reply, 'quick_replies' => $quickReplies]);
        }

        $matchedIntentId = null;
        $reply = '';

        if ($text === '' || $text === 'hi' || $text === 'hello' || $text === 'hey') {
            $reply = $greeting;
        } else {
            $intents = ChatbotIntent::query()
                ->where('is_active', true)
                ->orderBy('priority')
                ->orderBy('id')
                ->get();

            foreach ($intents as $intent) {
                $intentNameNorm = $this->normalize(strtolower((string) $intent->name));
                if ($intentNameNorm !== '' && $intentNameNorm === $normalizedText) {
                    $reply = $intent->reply;
                    $matchedIntentId = $intent->id;
                    $qr = is_array($intent->quick_replies) ? $intent->quick_replies : [];
                    if (!empty($qr)) {
                        $quickReplies = $qr;
                    }
                    break;
                }

                $triggers = is_array($intent->triggers) ? $intent->triggers : [];
                if (empty($triggers) && (string) $intent->name !== '') {
                    $triggers = [(string) $intent->name];
                }
                foreach ($triggers as $tr) {
                    $needle = strtolower(trim((string) $tr));
                    if ($needle === '') continue;
                    if (str_contains($text, $needle)) {
                        $reply = $intent->reply;
                        $matchedIntentId = $intent->id;
                        $qr = is_array($intent->quick_replies) ? $intent->quick_replies : [];
                        if (!empty($qr)) {
                            $quickReplies = $qr;
                        }
                        break 2;
                    }
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
        ]);
    }

    private function splitLinesCsv(string $raw): array
    {
        $t = trim($raw);
        if ($t === '') return [];

        $parts = preg_split('/[\n,]+/', $t) ?: [];
        $clean = [];
        foreach ($parts as $p) {
            $v = trim((string) $p);
            if ($v === '') continue;
            $clean[] = $v;
        }

        return array_values(array_unique($clean));
    }

    private function intentNameSuggestions(int $limit = 6): array
    {
        $names = ChatbotIntent::query()
            ->where('is_active', true)
            ->orderBy('priority')
            ->orderBy('id')
            ->limit($limit)
            ->pluck('name')
            ->map(fn ($v) => trim((string) $v))
            ->filter(fn ($v) => $v !== '')
            ->values()
            ->all();

        if (!empty($names)) {
            return $names;
        }

        return [
            'How do I log in?',
            'How do I vote?',
            'How do I register as candidate?',
            'Where can I see results?',
        ];
    }

    private function logChat(Request $request, string $message, string $reply, ?int $matchedIntentId): void
    {
        try {
            ChatbotLog::query()->create([
                'user_id' => $request->user()?->id,
                'session_id' => (string) ($request->session()->getId() ?? ''),
                'message' => $message,
                'reply' => $reply,
                'matched_intent_id' => $matchedIntentId,
            ]);
        } catch (\Throwable $e) {
            // swallow
        }
    }

    private function normalize(string $s): string
    {
        $t = trim($s);
        if ($t === '') return '';
        $t = preg_replace('/\s+/', ' ', $t) ?? $t;
        $t = preg_replace('/[^a-z0-9 ]/i', '', $t) ?? $t;
        return trim(strtolower($t));
    }
}

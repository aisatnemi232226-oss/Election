<?php

namespace App\Http\Controllers;

use App\Models\ChatbotIntent;
use App\Models\ChatbotSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminChatbotController extends Controller
{
    public function index(): View
    {
        $intents = ChatbotIntent::query()
            ->orderBy('priority')
            ->orderBy('name')
            ->get();

        $settings = ChatbotSetting::query()
            ->whereIn('key', ['greeting', 'fallback'])
            ->pluck('value', 'key');

        return view('admin.chatbot', [
            'intents' => $intents,
            'settings' => $settings,
        ]);
    }

    public function storeIntent(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'priority' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'is_active' => ['nullable'],
            'triggers' => ['nullable', 'string'],
            'reply' => ['required', 'string'],
        ]);

        $intent = new ChatbotIntent();
        $intent->name = $data['name'];
        $intent->priority = (int) ($data['priority'] ?? 100);
        $intent->is_active = (bool) ($request->boolean('is_active'));
        $intent->reply = $data['reply'];

        $intent->triggers = $this->splitLinesCsv($data['triggers'] ?? '');

        $intent->save();

        return back()->with('success', 'Chatbot intent created.');
    }

    public function updateIntent(Request $request, int $id): RedirectResponse
    {
        $intent = ChatbotIntent::query()->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'priority' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'is_active' => ['nullable'],
            'triggers' => ['nullable', 'string'],
            'reply' => ['required', 'string'],
        ]);

        $intent->name = $data['name'];
        $intent->priority = (int) ($data['priority'] ?? 100);
        $intent->is_active = (bool) ($request->boolean('is_active'));
        $intent->reply = $data['reply'];
        $intent->triggers = $this->splitLinesCsv($data['triggers'] ?? '');

        $intent->save();

        return back()->with('success', 'Chatbot intent updated.');
    }

    public function destroyIntent(int $id): RedirectResponse
    {
        $intent = ChatbotIntent::query()->findOrFail($id);
        $intent->delete();

        return back()->with('success', 'Chatbot intent deleted.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'greeting' => ['nullable', 'string'],
            'fallback' => ['nullable', 'string'],
        ]);

        foreach (['greeting', 'fallback'] as $key) {
            if (!array_key_exists($key, $data)) continue;
            ChatbotSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $data[$key] ?? null]
            );
        }

        return back()->with('success', 'Chatbot settings updated.');
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
}

@extends('layouts.admin')

@section('title', 'Chatbot CMS')
@section('dashboard-title', 'Chatbot CMS')

@section('content')
    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Chatbot CMS</h2>
        <p class="text-gray-600 text-lg">Manage chatbot intents, triggers, and responses.</p>
    </section>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-800">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Settings</h3>

                <form method="POST" action="{{ route('admin.chatbot.settings.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Greeting</label>
                        <textarea name="greeting" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">{{ old('greeting', $settings['greeting'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fallback</label>
                        <textarea name="fallback" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">{{ old('fallback', $settings['fallback'] ?? '') }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save Settings</button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">New Intent</h3>

                <form method="POST" action="{{ route('admin.chatbot.intents.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2" required />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Priority (lower = first)</label>
                            <input name="priority" type="number" value="{{ old('priority', 100) }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2" />
                        </div>
                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" {{ old('is_active', true) ? 'checked' : '' }} />
                                Active
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Triggers (comma or new line)</label>
                        <textarea name="triggers" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">{{ old('triggers') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reply</label>
                        <textarea name="reply" rows="4" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2" required>{{ old('reply') }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Create Intent</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Intents</h3>

                <div class="space-y-4">
                    @forelse($intents as $intent)
                        <details class="border border-gray-200 rounded-lg p-4">
                            <summary class="cursor-pointer select-none flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-gray-900">{{ $intent->name }}</div>
                                    <div class="text-xs text-gray-500">Priority: {{ $intent->priority }} · {{ $intent->is_active ? 'Active' : 'Inactive' }}</div>
                                </div>
                                <div class="text-xs text-gray-500">Click to edit</div>
                            </summary>

                            <div class="mt-4">
                                <form method="POST" action="{{ route('admin.chatbot.intents.update', $intent->id) }}" class="space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Name</label>
                                        <input name="name" value="{{ old('name', $intent->name) }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2" required />
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Priority</label>
                                            <input name="priority" type="number" value="{{ old('priority', $intent->priority) }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2" />
                                        </div>
                                        <div class="flex items-end">
                                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300" {{ old('is_active', $intent->is_active) ? 'checked' : '' }} />
                                                Active
                                            </label>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Triggers</label>
                                        <textarea name="triggers" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">{{ old('triggers', is_array($intent->triggers) ? implode("\n", $intent->triggers) : '') }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Reply</label>
                                        <textarea name="reply" rows="4" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2" required>{{ old('reply', $intent->reply) }}</textarea>
                                    </div>

                                    <div class="flex justify-end gap-2">
                                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('admin.chatbot.intents.destroy', $intent->id) }}" class="mt-2" onsubmit="return confirm('Delete this intent?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</button>
                                </form>
                            </div>
                        </details>
                    @empty
                        <div class="text-sm text-gray-500">No intents yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

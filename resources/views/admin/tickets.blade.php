@extends('layouts.admin')

@section('title', 'Tickets')
@section('dashboard-title', 'Tickets')

@section('content')
    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Support Tickets</h2>
        <p class="text-gray-600 text-lg">User vote concerns (reset vote request / wrong vote).</p>
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

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Date</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">User</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Type</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Subject</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Status</th>
                        <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($tickets ?? collect()) as $t)
                        <tr class="align-top">
                            <td class="py-2 px-4 border-b border-gray-300">{{ optional($t->created_at)->format('Y-m-d H:i') }}</td>
                            <td class="py-2 px-4 border-b border-gray-300">
                                <div class="font-semibold">{{ optional($t->user)->name ?? ('User #' . $t->user_id) }}</div>
                                <div class="text-xs text-gray-500">{{ optional($t->user)->idnumber ?? '' }}</div>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-300">{{ $t->type }}</td>
                            <td class="py-2 px-4 border-b border-gray-300">
                                <div class="font-semibold">{{ $t->subject }}</div>
                                <div class="text-sm text-gray-600 whitespace-pre-line mt-1">{{ $t->message }}</div>
                                @if ($t->admin_reply)
                                    <div class="mt-2 text-sm">
                                        <div class="font-bold text-gray-800">Admin Reply:</div>
                                        <div class="text-gray-700 whitespace-pre-line">{{ $t->admin_reply }}</div>
                                    </div>
                                @endif
                            </td>
                            <td class="py-2 px-4 border-b border-gray-300">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold {{ $t->status === 'Resolved' ? 'bg-green-100 text-green-800' : ($t->status === 'In Progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $t->status }}
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b border-gray-300">
                                <details class="border border-gray-200 rounded-lg p-3">
                                    <summary class="cursor-pointer select-none text-sm font-semibold text-blue-700">Reply / Update</summary>
                                    <form method="POST" action="{{ route('admin.tickets.update', $t->id) }}" class="mt-3 space-y-3">
                                        @csrf
                                        @method('PUT')

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Status</label>
                                            <select name="status" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2" required>
                                                <option value="Open" {{ $t->status === 'Open' ? 'selected' : '' }}>Open</option>
                                                <option value="In Progress" {{ $t->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="Resolved" {{ $t->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Admin Reply</label>
                                            <textarea name="admin_reply" rows="4" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2">{{ old('admin_reply', $t->admin_reply) }}</textarea>
                                        </div>

                                        <div class="flex justify-end">
                                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                                        </div>
                                    </form>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-4 text-gray-500" colspan="6">No tickets yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

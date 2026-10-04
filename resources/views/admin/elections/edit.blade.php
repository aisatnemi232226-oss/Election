@extends('layouts.admin')

@section('title', 'Edit Election')
@section('dashboard-title', 'Edit Election')

@section('content')
    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Edit Election</h2>
        <p class="text-gray-600 text-lg">Update election details below.</p>
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

    <section class="my-8">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
            <form action="{{ route('admin.elections.update', $election->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Election Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $election->name) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', optional($election->start_date)->format('Y-m-d') ?? $election->start_date) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date', optional($election->end_date)->format('Y-m-d') ?? $election->end_date) }}" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="reg_start" class="block text-sm font-medium text-gray-700">Registration Start</label>
                        <input id="reg_start" name="reg_start" type="date" value="{{ old('reg_start', optional($election->reg_start)->format('Y-m-d') ?? $election->reg_start) }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div>
                        <label for="reg_end" class="block text-sm font-medium text-gray-700">Registration End</label>
                        <input id="reg_end" name="reg_end" type="date" value="{{ old('reg_end', optional($election->reg_end)->format('Y-m-d') ?? $election->reg_end) }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                </div>

                <div>
                    <label for="result_visibility" class="block text-sm font-medium text-gray-700">Candidate result visibility</label>
                    <select id="result_visibility" name="result_visibility" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="hidden" @selected(old('result_visibility', $election->result_visibility ?? 'hidden') === 'hidden')>Hidden</option>
                        <option value="turnout_only" @selected(old('result_visibility', $election->result_visibility ?? 'hidden') === 'turnout_only')>Turnout only</option>
                        <option value="live_candidate_results" @selected(old('result_visibility', $election->result_visibility ?? 'hidden') === 'live_candidate_results')>Publish candidate results immediately</option>
                        <option value="after_close" @selected(old('result_visibility', $election->result_visibility ?? 'hidden') === 'after_close')>Publish after voting closes</option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Candidate vote totals are hidden unless this election explicitly permits publication.</p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <a href="{{ route('admin.elections.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">Back</a>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save Changes</button>
                </div>
            </form>
        </div>
    </section>
@endsection

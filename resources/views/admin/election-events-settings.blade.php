@extends('layouts.admin')

@section('title', 'Manage Election Events')
@section('dashboard-title', 'Manage Election Events')

@section('content')
    <div class="mx-auto w-full max-w-5xl">
        <section class="mb-6 sm:mb-8">
            <h2 class="text-2xl sm:text-4xl font-bold text-gray-800">Manage Election Events</h2>
            <p class="mt-2 text-gray-600 text-sm sm:text-lg">Configure the Election Events label and the 3 event tiles shown on the voter homepage.</p>
        </section>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if (!empty($warning))
        <div class="mb-4 rounded-lg bg-yellow-50 px-4 py-3 text-yellow-800">
            {{ $warning }}
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

    @php
        $defaultLabel = 'Election 2026 for CSG';

        $defaults = [
            1 => [
                'title' => 'Election Kickoff',
                'date' => 'March 1, 2023',
                'description' => 'Join us for the official start of the election season with speeches from candidates and more.',
                'image' => 'https://storage.googleapis.com/a1aa/image/dvCf7rv3J9wNaqenfCddb8170ZkT2UD6ivYRPVt79lD3ezaQB.jpg',
            ],
            2 => [
                'title' => 'Candidate Debate',
                'date' => 'March 15, 2023',
                'description' => 'Watch the candidates debate on key issues and present their platforms.',
                'image' => 'https://storage.googleapis.com/a1aa/image/4ankqrk3osKqB9DQfnuXoqcnqPtRvVpXvf2A5JDQWeZ9ezaQB.jpg',
            ],
            3 => [
                'title' => 'Voting Day',
                'date' => 'April 1, 2023',
                'description' => 'Cast your vote online and make your voice heard in the student government elections.',
                'image' => 'https://storage.googleapis.com/a1aa/image/2ZcIUq4TX0axBpyzxe6zBIm7EI5K3w8fnLKi5Py79RXgfZNoA.jpg',
            ],
        ];

        $labelValue = old('label', $settings->label ?? $defaultLabel);

        $labelImageRelative = $settings?->label_image;
        $labelImageExists = $labelImageRelative ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $labelImageRelative) : false;
        $labelImageUrl = $labelImageExists
            ? route('storage.image', ['folder' => 'upload', 'filename' => $labelImageRelative])
            : null;

        $imageUrls = [];
        for ($i = 1; $i <= 3; $i++) {
            $relative = $settings?->{"event_{$i}_image"};
            $exists = $relative ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $relative) : false;
            $imageUrls[$i] = $exists
                ? route('storage.image', ['folder' => 'upload', 'filename' => $relative])
                : $defaults[$i]['image'];
        }
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <form action="{{ route('admin.election-events-settings.update') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-8">
            @csrf
            @method('PUT')

            <!-- Election Status Table -->
            <div class="border border-gray-200 rounded-xl p-4 sm:p-5">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Election Status</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($settings?->start_date)
                                        {{ \Carbon\Carbon::parse($settings->start_date)->format('M j, Y g:i A') }}
                                    @else
                                        <span class="text-gray-400">Not set</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($settings?->end_date)
                                        {{ \Carbon\Carbon::parse($settings->end_date)->format('M j, Y g:i A') }}
                                    @else
                                        <span class="text-gray-400">Not set</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($settings?->status === 'upcoming')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Upcoming</span>
                                    @elseif($settings?->status === 'ongoing')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Ongoing</span>
                                    @elseif($settings?->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Completed</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Not set</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <label for="label" class="block text-sm font-medium text-gray-700">Election Events Label (shown on voter homepage)</label>
                <input id="label" name="label" type="text" value="{{ $labelValue }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input id="start_date" name="start_date" type="datetime-local" value="{{ old('start_date', $settings?->start_date ? \Carbon\Carbon::parse($settings->start_date)->format('Y-m-d\TH:i') : '') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                <input id="end_date" name="end_date" type="datetime-local" value="{{ old('end_date', $settings?->end_date ? \Carbon\Carbon::parse($settings->end_date)->format('Y-m-d\TH:i') : '') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="upcoming" {{ ($settings?->status ?? 'upcoming') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ ($settings?->status ?? '') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ ($settings?->status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div class="border border-gray-200 rounded-xl p-4 sm:p-5">
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between mb-4">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-800">Label Image</h3>
                    <div class="text-xs text-gray-500">Optional</div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    <div class="lg:col-span-3 space-y-2">
                        <label for="label_image" class="block text-sm font-medium text-gray-700">Upload Image</label>
                        <input id="label_image" name="label_image" type="file" accept="image/*" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 bg-white" />
                        <p class="mt-1 text-xs text-gray-500">Max 5MB. If you don't upload, current image stays.</p>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="text-sm font-medium text-gray-700 mb-2">Current Image</div>
                        @if($labelImageUrl)
                            <img src="{{ $labelImageUrl }}" alt="Label image" class="w-full h-40 sm:h-48 object-cover rounded-lg border" />
                        @else
                            <div class="w-full h-40 sm:h-48 rounded-lg border bg-gray-50 flex items-center justify-center text-sm text-gray-500">No image</div>
                        @endif
                    </div>
                </div>
            </div>

            @for ($i = 1; $i <= 3; $i++)
                @php
                    $titleKey = "event_{$i}_title";
                    $dateKey = "event_{$i}_date";
                    $descKey = "event_{$i}_description";
                    $imgKey = "event_{$i}_image";

                    $titleVal = old($titleKey, $settings?->{$titleKey} ?? $defaults[$i]['title']);
                    $dateVal = old($dateKey, $settings?->{$dateKey} ?? $defaults[$i]['date']);
                    $descVal = old($descKey, $settings?->{$descKey} ?? $defaults[$i]['description']);
                @endphp

                <div class="border border-gray-200 rounded-xl p-4 sm:p-5">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800">Event Tile {{ $i }}</h3>
                        <div class="text-xs text-gray-500">Preview image shown below</div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                        <div class="lg:col-span-3 space-y-4">
                            <div>
                                <label for="{{ $titleKey }}" class="block text-sm font-medium text-gray-700">Title</label>
                                <input id="{{ $titleKey }}" name="{{ $titleKey }}" type="text" value="{{ $titleVal }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>

                            <div>
                                <label for="{{ $dateKey }}" class="block text-sm font-medium text-gray-700">Date (text)</label>
                                <input id="{{ $dateKey }}" name="{{ $dateKey }}" type="text" value="{{ $dateVal }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>

                            <div>
                                <label for="{{ $descKey }}" class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea id="{{ $descKey }}" name="{{ $descKey }}" rows="4" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" data-quill="1">{{ $descVal }}</textarea>
                            </div>

                            <div>
                                <label for="{{ $imgKey }}" class="block text-sm font-medium text-gray-700">Image (upload)</label>
                                <input id="{{ $imgKey }}" name="{{ $imgKey }}" type="file" accept="image/*" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 bg-white" />
                                <p class="mt-1 text-xs text-gray-500">Max 5MB. If you don't upload, current image stays.</p>
                            </div>
                        </div>

                        <div class="lg:col-span-2">
                            <div class="text-sm font-medium text-gray-700 mb-2">Current/Default Image</div>
                            <img src="{{ $imageUrls[$i] }}" alt="Event {{ $i }} image" class="w-full h-48 sm:h-56 object-cover rounded-lg border" />
                        </div>
                    </div>
                </div>
            @endfor

            <div class="flex flex-col sm:flex-row sm:justify-end gap-3">
                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Save Settings</button>
            </div>
        </form>
    </div>
    </div>
@endsection

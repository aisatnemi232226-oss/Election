@extends('layouts.admin')

@section('title', 'Candidate')
@section('dashboard-title', 'Candidates')

@section('content')
    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Manage Candidates</h2>
        <p class="text-gray-600 text-lg">Here you can manage all the candidates for the elections.</p>
    </section>

    <section class="my-8">
        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <!-- Header with title, search, pagination, and new button -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-800">Candidates List</h1>
                    
                    <div class="relative">
                        <x-search-box 
                            id="candidates-search"
                            placeholder="Search candidates, courses, status..."
                            class="w-80"
                        />
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a
                        href="{{ $isArchived ? route('admin.candidates.index') : route('admin.candidates.index', ['archived' => 1]) }}"
                        class="px-5 py-2 text-sm font-medium text-white {{ $isArchived ? 'bg-gray-700 hover:bg-gray-800' : 'bg-gray-600 hover:bg-gray-700' }} rounded-md"
                        id="archive-toggle-btn">
                        {{ $isArchived ? 'Back to List' : 'Archive' }}
                    </a>

                    <!-- Pagination at the top - properly aligned -->
                    <div id="pagination-container-top" class="flex items-center">
                        {{ $candidates->appends(['archived' => $isArchived ? 1 : 0, 'status' => $status, 'course' => $courseFilter, 'party' => $partyFilter, 'position' => $positionFilter])->links('components.pagination') }}
                    </div>
                    
                    @if (!$isArchived)
                        <button 
                            type="button"
                            class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                            id="new-candidate-btn">
                            New Candidate
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-2 mb-4" id="candidate-status-filters">
                <button type="button" class="px-3 py-2 text-sm font-medium rounded-md border border-gray-200 bg-white hover:bg-gray-50" data-status-filter="" aria-pressed="false">All</button>
                <button type="button" class="px-3 py-2 text-sm font-medium rounded-md border border-gray-200 bg-white hover:bg-gray-50" data-status-filter="Pending" aria-pressed="false">Pending</button>
                <button type="button" class="px-3 py-2 text-sm font-medium rounded-md border border-gray-200 bg-white hover:bg-gray-50" data-status-filter="Active" aria-pressed="false">Active</button>
                <button type="button" class="px-3 py-2 text-sm font-medium rounded-md border border-gray-200 bg-white hover:bg-gray-50" data-status-filter="Inactive" aria-pressed="false">Inactive</button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border-collapse">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Photo</th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Name</th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">
                                <label for="candidate-course-filter" class="sr-only">Filter by course</label>
                                <select id="candidate-course-filter" data-candidate-filter="course" class="max-w-48 rounded-md border border-gray-300 bg-white px-2 py-1 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    <option value="">All Courses</option>
                                    <option value="none" @selected($courseFilter === 'none')>No Course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}" @selected((string) $courseFilter === (string) $course->id)>{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">
                                <label for="candidate-party-filter" class="sr-only">Filter by party</label>
                                <select id="candidate-party-filter" data-candidate-filter="party" class="max-w-48 rounded-md border border-gray-300 bg-white px-2 py-1 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    <option value="">All Parties</option>
                                    <option value="none" @selected($partyFilter === 'none')>No Party</option>
                                    @foreach ($parties as $party)
                                        <option value="{{ $party->id }}" @selected((string) $partyFilter === (string) $party->id)>{{ $party->name }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">
                                <label for="candidate-position-filter" class="sr-only">Filter by position</label>
                                <select id="candidate-position-filter" data-candidate-filter="position" class="max-w-48 rounded-md border border-gray-300 bg-white px-2 py-1 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                                    <option value="">All Positions</option>
                                    <option value="none" @selected($positionFilter === 'none')>No Position</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}" @selected((string) $positionFilter === (string) $position->id)>{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Status</th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="candidates-table-body">
                        @foreach ($candidates as $candidate)
                            @php
                                $profileRelative = $candidate->profile_img
                                    ? (\Illuminate\Support\Str::contains($candidate->profile_img, '/') ? $candidate->profile_img : ('profile-picture/' . $candidate->profile_img))
                                    : null;
                                $bannerRelative = $candidate->banner_img
                                    ? (\Illuminate\Support\Str::contains($candidate->banner_img, '/') ? $candidate->banner_img : ('banner/' . $candidate->banner_img))
                                    : null;

                                $profileExists = $profileRelative
                                    ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $profileRelative)
                                    : false;
                                $bannerExists = $bannerRelative
                                    ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $bannerRelative)
                                    : false;

                                $profileUrl = $profileExists
                                    ? route('storage.image', ['folder' => 'upload', 'filename' => $profileRelative])
                                    : asset('images/default-profile.jpg');
                                $bannerUrl = $bannerExists
                                    ? route('storage.image', ['folder' => 'upload', 'filename' => $bannerRelative])
                                    : asset('images/default-banner.png');
                            @endphp
                            <tr>
                                <td class="py-2 px-4 border-b">
                                    <img 
                                        src="{{ $profileUrl }}" 
                                        alt="{{ $candidate->name }}"
                                        class="w-10 h-10 object-cover rounded-full"
                                        onerror="this.onerror=null; this.src='{{ asset('images/default-profile.jpg') }}';"
                                    >
                                </td>
                                <td class="py-2 px-4 border-b">{{ $candidate->name }}</td>
                                <td class="py-2 px-4 border-b">{{ optional($candidate->course)->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b">{{ $candidate->party ? $candidate->party->name : 'No Party' }}</td>
                                <td class="py-2 px-4 border-b">{{ optional($candidate->position)->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex flex-wrap items-center gap-1">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $candidate->status === 'Active' ? 'bg-green-100 text-green-800' : ($candidate->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $candidate->status }}
                                        </span>
                                        @if ($isArchived)
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Archived</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex gap-2">
                                        @if (!$isArchived)
                                            @if (($candidate->status ?? '') === 'Pending')
                                                <form method="POST" action="{{ route('admin.candidates.approve', $candidate->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg hover:bg-emerald-700">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.candidates.reject', $candidate->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700">
                                                        Reject
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                        <button 
                                            type="button"
                                            class="view-button bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                                            data-id="{{ $candidate->id }}"
                                            data-candidate="{{ $candidate->name }}"
                                            data-course="{{ optional($candidate->course)->name ?? 'N/A' }}"
                                            data-position="{{ optional($candidate->position)->name ?? 'N/A' }}"
                                            data-party="{{ $candidate->party ? $candidate->party->name : 'No Party' }}"
                                            data-description="{{ $candidate->description ?? 'No description available.' }}"
                                            data-status="{{ $candidate->status }}"
                                            data-profile-image="{{ $profileUrl }}"
                                            data-banner-image="{{ $bannerUrl }}"
                                            data-platforms="{{ $candidate->platforms ?? 'No Platforms' }}"
                                            data-related-events="{{ $candidate->related_events ?? '' }}"
                                            data-joined-elections="{{ e($candidate->joined_elections ?? '') }}">
                                            View
                                        </button>

                                        @if (!$isArchived)
                                            <button 
                                                type="button"
                                                class="edit-button bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                                                data-id="{{ $candidate->id }}"
                                                data-candidate="{{ $candidate->name }}"
                                                data-course="{{ optional($candidate->course)->name ?? '' }}"
                                                data-course-id="{{ optional($candidate->course)->id ?? '' }}"
                                                data-election-id="{{ $candidate->election_id ?? '' }}"
                                                data-position="{{ optional($candidate->position)->name ?? '' }}"
                                                data-position-id="{{ optional($candidate->position)->id ?? '' }}"
                                                data-party="{{ $candidate->party ? $candidate->party->name : 'No Party' }}"
                                                data-party-id="{{ $candidate->party ? $candidate->party->id : '' }}"
                                                data-description="{{ $candidate->description ?? '' }}"
                                                data-status="{{ $candidate->status }}"
                                                data-profile-image="{{ $profileUrl }}"
                                                data-banner-image="{{ $bannerUrl }}"
                                                data-platforms="{{ $candidate->platforms ?? '' }}"
                                                data-related-events="{{ $candidate->related_events ?? '' }}"
                                                data-joined-elections="{{ e($candidate->joined_elections ?? '') }}">
                                                Edit
                                            </button>
                                        @endif
                                        
                                        @if ($isArchived)
                                            <form method="POST" action="{{ route('admin.candidates.restore', $candidate->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                                                    Restore
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('admin.candidates.forceDestroy', $candidate->id) }}" id="force-delete-form-{{ $candidate->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                    class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800 force-delete-button"
                                                    data-id="{{ $candidate->id }}">
                                                    Permanent Delete
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.candidates.destroy', $candidate->id) }}" id="delete-form-{{ $candidate->id }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 delete-button"
                                                    data-id="{{ $candidate->id }}">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Hidden pagination container for backup -->
            <div id="pagination-container" class="mt-4" style="display: none;">
                {{ $candidates->appends(['archived' => $isArchived ? 1 : 0])->links('components.pagination') }}
            </div>
        </div>
    </section>

    <!-- Candidate Modal (View) -->
    <div class="fixed inset-0 overflow-y-auto hidden" id="candidate-modal" style="z-index: 9999;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>

            <div class="relative bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="bg-white">
                    <div class="relative">
                        <img id="candidate-banner-image" class="w-full h-36 object-cover" alt="Banner Image" onerror="this.onerror=null; this.src='{{ asset('images/default-banner.png') }}';">
                        <div class="absolute -bottom-8 left-6 flex items-end gap-4">
                            <img id="candidate-profile-image" class="w-16 h-16 object-cover rounded-full border-4 border-white shadow" alt="Profile Image" onerror="this.onerror=null; this.src='{{ asset('images/default-profile.jpg') }}';">
                            <div class="pb-1">
                                <h3 class="text-2xl font-bold text-gray-900" id="candidate-name"></h3>
                                <div class="mt-1">
                                    <span id="candidate-status-badge" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 pt-12">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs font-semibold text-gray-500">Status</div>
                                <div class="text-sm font-medium text-gray-900" id="candidate-status"></div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs font-semibold text-gray-500">Course</div>
                                <div class="text-sm font-medium text-gray-900" id="candidate-course"></div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="text-xs font-semibold text-gray-500">Position</div>
                                <div class="text-sm font-medium text-gray-900" id="candidate-position"></div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 sm:col-span-2">
                                <div class="text-xs font-semibold text-gray-500">Party</div>
                                <div class="text-sm font-medium text-gray-900" id="candidate-party"></div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="text-sm font-bold text-gray-900 mb-2">Description</div>
                            <div class="text-sm text-gray-800 bg-white border border-gray-200 rounded-lg p-3" id="candidate-description"></div>
                        </div>

                        <div class="mt-5">
                            <div class="text-sm font-bold text-gray-900 mb-2">Platforms</div>
                            <div class="text-sm text-gray-800 bg-white border border-gray-200 rounded-lg p-3" id="candidate-platforms"></div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm font-bold text-gray-900 mb-2">Related Events</div>
                                <ul class="list-disc pl-5 text-sm text-gray-800 bg-white border border-gray-200 rounded-lg p-3" id="candidate-related-events"></ul>
                            </div>
                            <div>
                                <div class="text-sm font-bold text-gray-900 mb-2">Joined Elections</div>
                                <ul class="list-disc pl-5 text-sm text-gray-800 bg-white border border-gray-200 rounded-lg p-3" id="candidate-joined-elections"></ul>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" id="close-modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal (Delete) -->
    <div class="fixed inset-0 overflow-y-auto hidden" id="confirmation-modal" style="z-index: 9999;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>
            <div class="relative bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-sm sm:w-full">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Confirm Deletion</h3>
                    <p class="text-gray-600 mb-4">Are you sure you want to delete this candidate?</p>
                    <div class="flex justify-end">
                        <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400" id="cancel-delete">Cancel</button>
                        <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 ml-2" id="confirm-delete">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="fixed inset-0 overflow-y-auto hidden" id="error-modal" style="z-index: 9999;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>
            <div class="relative bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-sm sm:w-full">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Error</h3>
                    <p class="text-gray-600 mb-4" id="error-message"></p>
                    <div class="flex justify-end">
                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" id="close-error">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal" class="fixed inset-0 overflow-y-auto hidden" style="z-index: 9999;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>

            <div class="relative bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="bg-white p-6">
                    <h3 class="text-2xl font-bold mb-4">Edit Candidate</h3>
                    <form id="edit-candidate-form" method="POST" action="" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit-candidate-id" name="id">

                        <div class="mb-4">
                            <label for="edit-candidate-name" class="block font-medium">Name</label>
                            <input id="edit-candidate-name" name="name" type="text" class="w-full p-2 border border-gray-300 rounded" placeholder="Enter name" required>
                        </div>
                        
                        <!-- Custom Profile Image Upload -->
                        <div class="mb-4">
                            <label class="block font-medium mb-2">Profile Image</label>
                            <div class="relative">
                                <input id="edit-profile-image" name="profile_img" type="file" class="hidden" accept="image/*">
                                <button type="button" onclick="document.getElementById('edit-profile-image').click()" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-left flex items-center justify-between">
                                    <span id="edit-profile-filename" class="text-gray-500">Choose profile image...</span>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </button>
                            </div>
                            <img id="edit-profile-image-preview" src="" alt="Profile Image" class="w-32 h-32 rounded-full mt-2 hidden object-cover" onerror="this.onerror=null; this.src='{{ asset('images/default-profile.jpg') }}';">
                        </div>

                        <!-- Custom Banner Image Upload -->
                        <div class="mb-4">
                            <label class="block font-medium mb-2">Banner Image</label>
                            <div class="relative">
                                <input id="edit-banner-image" name="banner_img" type="file" class="hidden" accept="image/*">
                                <button type="button" onclick="document.getElementById('edit-banner-image').click()" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-left flex items-center justify-between">
                                    <span id="edit-banner-filename" class="text-gray-500">Choose banner image...</span>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </button>
                            </div>
                            <img id="edit-banner-image-preview" src="" alt="Banner Image" class="w-full h-40 object-cover rounded-lg mt-2 hidden" onerror="this.onerror=null; this.src='{{ asset('images/default-banner.png') }}';">
                        </div>

                        <div class="mb-4">
                            <label for="edit-candidate-description" class="block font-medium">Description</label>
                            <div id="edit-description-editor" class="bg-white border rounded shadow min-h-32 p-2" contenteditable="true"></div>
                            <input type="hidden" id="edit-candidate-description" name="description" data-quill="1">
                        </div>
                        <div class="mb-4">
                            <label for="edit-candidate-course" class="block font-medium">Course</label>
                            <select id="edit-candidate-course" name="course_id" class="border border-gray-300 p-2 w-full rounded" required>
                                <option value="">-- Select a Course --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="edit-candidate-election" class="block font-medium">Election</label>
                            <select id="edit-candidate-election" name="election_id" class="border border-gray-300 p-2 w-full rounded">
                                <option value="">Unassigned / historical</option>
                                @foreach ($elections as $election)
                                    <option value="{{ $election->id }}">{{ $election->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="edit-candidate-position" class="block font-medium">Position</label>
                            <select id="edit-candidate-position" name="position_id" class="border border-gray-300 p-2 w-full rounded">
                                <option value="">-- Select a Position --</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <label class="block mb-2" for="edit-candidate-party">Party</label>
                            <select id="edit-candidate-party" name="party_id" class="border border-gray-300 p-2 w-full rounded">
                                <option value="">No Party</option>
                                @foreach ($parties as $party)
                                    <option value="{{ $party->id }}">{{ $party->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit-candidate-platforms" class="block font-medium">Platforms</label>
                            <div id="edit-platforms-editor" class="bg-white border rounded shadow min-h-32 p-2" contenteditable="true"></div>
                            <input type="hidden" id="edit-candidate-platforms" name="platforms" data-quill="1">
                        </div>

                        <div class="mb-4">
                            <label for="edit-candidate-related-events" class="block font-medium">Related Events</label>
                            <textarea id="edit-candidate-related-events" name="related_events" class="w-full p-2 border border-gray-300 rounded" rows="3" placeholder="One per line or comma-separated"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="edit-candidate-joined-elections" class="block font-medium">Joined Elections</label>
                            <textarea id="edit-candidate-joined-elections" name="joined_elections" class="w-full p-2 border border-gray-300 rounded" rows="3" placeholder='JSON (e.g. [{"year":"2024","position":"Representative","winner":false}]) or plain text'></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="edit-candidate-status" class="block font-medium">Status</label>
                            <select id="edit-candidate-status" name="status" class="w-full p-2 border border-gray-300 rounded" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" id="cancel-edit" class="bg-gray-600 text-white px-4 py-2 rounded mr-2">Cancel</button>
                            <button type="submit" id="save-edit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Create New Candidate Modal -->
    <div class="fixed inset-0 overflow-y-auto hidden" id="new-candidate-modal" style="z-index: 9999;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>
            <div class="relative flex w-full max-w-lg flex-col overflow-hidden rounded-lg bg-white shadow-xl transform transition-all"
                style="max-height: calc(100vh - 2rem); max-height: calc(100dvh - 2rem);">
                <div class="flex min-h-0 flex-col p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Create New Candidate</h3>
                    <form id="new-candidate-form" method="POST" action="{{ route('admin.candidates.store') }}" enctype="multipart/form-data" class="min-h-0 flex-1 overflow-y-auto overscroll-contain pr-1">
                        @csrf
                        <div>
                            <label class="block mb-2" for="new-candidate-name">Name</label>
                            <input type="text" id="new-candidate-name" name="name" class="border border-gray-300 p-2 w-full rounded" required />
                        </div>
                        
                        <!-- Custom Profile Image Upload -->
                        <div class="mt-4">
                            <label class="block mb-2">Profile Image</label>
                            <div class="relative">
                                <input id="new-candidate-image" name="profile_img" type="file" class="hidden" accept="image/*" required>
                                <button type="button" onclick="document.getElementById('new-candidate-image').click()" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-left flex items-center justify-between">
                                    <span id="new-profile-filename" class="text-gray-500">Choose profile image...</span>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </button>
                            </div>
                            <img id="new-candidate-image-preview" src="" alt="Profile Preview" class="w-32 h-32 object-cover rounded-full mt-2 hidden" onerror="this.onerror=null; this.src='{{ asset('images/default-profile.jpg') }}';">
                        </div>

                        <!-- Custom Banner Image Upload -->
                        <div class="mt-4">
                            <label class="block mb-2">Banner Image</label>
                            <div class="relative">
                                <input id="new-candidate-banner" name="banner_img" type="file" class="hidden" accept="image/*" required>
                                <button type="button" onclick="document.getElementById('new-candidate-banner').click()" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-left flex items-center justify-between">
                                    <span id="new-banner-filename" class="text-gray-500">Choose banner image...</span>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </button>
                            </div>
                            <img id="new-candidate-banner-preview" src="" alt="Banner Preview" class="w-full h-40 object-cover rounded-lg mt-2 hidden" onerror="this.onerror=null; this.src='{{ asset('images/default-banner.png') }}';">
                        </div>

                        <div class="mt-4">
                            <label class="block mb-2" for="new-candidate-description">Description</label>
                            <div id="new-description-editor" class="bg-white border rounded shadow min-h-32 p-2" contenteditable="true"></div>
                            <input type="hidden" id="new-candidate-description" name="description" data-quill="1">
                        </div>
                        <div class="mt-4">
                            <label class="block mb-2" for="new-candidate-course">Course</label>
                            <select id="new-candidate-course" name="course_id" class="border border-gray-300 p-2 w-full rounded" required>
                                <option value="">-- Select a Course --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <label class="block mb-2" for="new-candidate-election">Election</label>
                            <select id="new-candidate-election" name="election_id" class="border border-gray-300 p-2 w-full rounded" required>
                                <option value="">-- Select an Election --</option>
                                @foreach ($elections as $election)
                                    <option value="{{ $election->id }}">{{ $election->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <label class="block mb-2" for="new-candidate-position">Position</label>
                            <select id="new-candidate-position" name="position_id" class="border border-gray-300 p-2 w-full rounded">
                                <option value="">-- Select a Position --</option>
                                @foreach ($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4">
                            <label class="block mb-2" for="new-candidate-party">Party</label>
                            <select id="new-candidate-party" name="party_id" class="border border-gray-300 p-2 w-full rounded">
                                <option value="">No Party</option>
                                @foreach ($parties as $party)
                                    <option value="{{ $party->id }}">{{ $party->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-4">
                            <label class="block mb-2" for="new-candidate-platforms">Platforms</label>
                            <div id="new-platforms-editor" class="bg-white border rounded shadow min-h-32 p-2" contenteditable="true"></div>
                            <input type="hidden" id="new-candidate-platforms" name="platforms" data-quill="1">
                        </div>

                        <div class="mt-4">
                            <label class="block mb-2" for="new-candidate-related-events">Related Events</label>
                            <textarea id="new-candidate-related-events" name="related_events" class="w-full p-2 border border-gray-300 rounded" rows="3" placeholder="One per line or comma-separated"></textarea>
                        </div>

                        <div class="mt-4">
                            <label class="block mb-2" for="new-candidate-joined-elections">Joined Elections</label>
                            <textarea id="new-candidate-joined-elections" name="joined_elections" class="w-full p-2 border border-gray-300 rounded" rows="3" placeholder='JSON (e.g. [{"year":"2024","position":"Representative","winner":false}]) or plain text'></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="new-candidate-status" class="block font-medium">Status</label>
                            <select id="new-candidate-status" name="status" class="w-full p-2 border border-gray-300 rounded" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="sticky bottom-0 flex justify-end mt-4 bg-white py-2">
                            <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400" id="cancel-new-candidate">Cancel</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 ml-2" id="save-new-candidate">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    <!-- Load the SearchBox component script -->
    @vite(['resources/js/components/search-box.js'])
    <!-- Load the Candidates script -->
    @vite(['resources/js/admin/candidates.js'])

    <script>
        // Custom file input functionality
        document.addEventListener('DOMContentLoaded', function() {
            const newCandidateBtn = document.getElementById('new-candidate-btn');
            const newCandidateModal = document.getElementById('new-candidate-modal');
            const cancelNewCandidateBtn = document.getElementById('cancel-new-candidate');

            const viewModal = document.getElementById('candidate-modal');
            const editModal = document.getElementById('edit-modal');
            const confirmationModal = document.getElementById('confirmation-modal');
            const confirmDeleteBtn = document.getElementById('confirm-delete');
            let deleteFormToSubmit = null;

            const closeViewBtn = document.getElementById('close-modal');
            const cancelEditBtn = document.getElementById('cancel-edit');
            const cancelDeleteBtn = document.getElementById('cancel-delete');
            const closeErrorBtn = document.getElementById('close-error');

            const hideModal = (modalEl) => {
                if (!modalEl) return;
                modalEl.classList.add('hidden');
                if (!document.querySelector('.fixed.inset-0.overflow-y-auto:not(.hidden)')) {
                    document.body.style.overflow = '';
                }
            };

            if (closeViewBtn) {
                closeViewBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    hideModal(viewModal);
                });
            }

            if (cancelEditBtn) {
                cancelEditBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    hideModal(editModal);
                });
            }

            if (cancelDeleteBtn) {
                cancelDeleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    hideModal(confirmationModal);
                });
            }

            if (closeErrorBtn) {
                closeErrorBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    hideModal(document.getElementById('error-modal'));
                });
            }

            // Backdrop click closes any open modal
            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList && e.target.classList.contains('modal-backdrop')) {
                    hideModal(viewModal);
                    hideModal(editModal);
                    hideModal(confirmationModal);
                    hideModal(document.getElementById('error-modal'));
                    hideModal(newCandidateModal);
                }
            });

            if (newCandidateBtn && newCandidateModal) {
                newCandidateBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    newCandidateModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }

            if (cancelNewCandidateBtn && newCandidateModal) {
                cancelNewCandidateBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    newCandidateModal.classList.add('hidden');
                    document.body.style.overflow = '';
                });
            }

            // View/Edit/Delete fallback handlers (works even if candidates.js doesn't load)
            document.addEventListener('click', function(e) {
                const viewBtn = e.target.closest('.view-button');
                if (viewBtn && viewModal) {
                    e.preventDefault();
                    const get = (key, fallback = '') => viewBtn.getAttribute(key) ?? fallback;

                    const nameEl = document.getElementById('candidate-name');
                    const statusEl = document.getElementById('candidate-status');
                    const courseEl = document.getElementById('candidate-course');
                    const positionEl = document.getElementById('candidate-position');
                    const partyEl = document.getElementById('candidate-party');
                    const descriptionEl = document.getElementById('candidate-description');
                    const platformsEl = document.getElementById('candidate-platforms');
                    const profileImgEl = document.getElementById('candidate-profile-image');
                    const bannerImgEl = document.getElementById('candidate-banner-image');
                    const statusBadgeEl = document.getElementById('candidate-status-badge');

                    if (nameEl) nameEl.textContent = get('data-candidate', '');
                    const statusVal = get('data-status', '');
                    if (statusEl) statusEl.textContent = statusVal;
                    if (courseEl) courseEl.textContent = get('data-course', '');
                    if (positionEl) positionEl.textContent = get('data-position', '');
                    if (partyEl) partyEl.textContent = get('data-party', '');
                    if (descriptionEl) descriptionEl.innerHTML = get('data-description', '');

                    if (platformsEl) {
                        const plats = get('data-platforms', '');
                        platformsEl.innerHTML = (plats && plats.trim() !== '') ? plats : '<span class="text-gray-500">No platforms provided.</span>';
                    }
                    if (profileImgEl) profileImgEl.src = get('data-profile-image', '');
                    if (bannerImgEl) bannerImgEl.src = get('data-banner-image', '');

                    if (statusBadgeEl) {
                        statusBadgeEl.textContent = statusVal;
                    }

                    viewModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    return;
                }

                const editBtn = e.target.closest('.edit-button');
                if (editBtn && editModal) {
                    e.preventDefault();
                    const candidateId = editBtn.getAttribute('data-id');

                    const editForm = document.getElementById('edit-candidate-form');
                    if (editForm && candidateId) {
                        editForm.action = `/admin/candidates/${candidateId}`;
                    }

                    const editId = document.getElementById('edit-candidate-id');
                    const editName = document.getElementById('edit-candidate-name');
                    const editCourse = document.getElementById('edit-candidate-course');
                    const editElection = document.getElementById('edit-candidate-election');
                    const editPosition = document.getElementById('edit-candidate-position');
                    const editParty = document.getElementById('edit-candidate-party');
                    const editStatus = document.getElementById('edit-candidate-status');
                    const editDescHidden = document.getElementById('edit-candidate-description');
                    const editPlatHidden = document.getElementById('edit-candidate-platforms');
                    const editDescEditor = document.getElementById('edit-description-editor');
                    const editPlatEditor = document.getElementById('edit-platforms-editor');
                    const profilePreview = document.getElementById('edit-profile-image-preview');
                    const bannerPreview = document.getElementById('edit-banner-image-preview');

                    if (editId) editId.value = candidateId || '';
                    if (editName) editName.value = editBtn.getAttribute('data-candidate') || '';
                    if (editCourse) editCourse.value = editBtn.getAttribute('data-course-id') || '';
                    if (editElection) editElection.value = editBtn.getAttribute('data-election-id') || '';
                    if (editPosition) editPosition.value = editBtn.getAttribute('data-position-id') || '';
                    if (editParty) editParty.value = editBtn.getAttribute('data-party-id') || '';
                    if (editStatus) editStatus.value = editBtn.getAttribute('data-status') || 'Active';

                    const desc = editBtn.getAttribute('data-description') || '';
                    const plats = editBtn.getAttribute('data-platforms') || '';
                    if (editDescHidden) editDescHidden.value = desc;
                    if (editPlatHidden) editPlatHidden.value = plats;
                    if (editDescEditor) editDescEditor.innerHTML = desc;
                    if (editPlatEditor) editPlatEditor.innerHTML = plats;

                    const profileImg = editBtn.getAttribute('data-profile-image');
                    const bannerImg = editBtn.getAttribute('data-banner-image');
                    if (profilePreview && profileImg) {
                        profilePreview.src = profileImg;
                        profilePreview.classList.remove('hidden');
                    }
                    if (bannerPreview && bannerImg) {
                        bannerPreview.src = bannerImg;
                        bannerPreview.classList.remove('hidden');
                    }

                    editModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    return;
                }

                const deleteBtn = e.target.closest('.delete-button');
                if (deleteBtn && confirmationModal) {
                    e.preventDefault();
                    const id = deleteBtn.getAttribute('data-id');
                    deleteFormToSubmit = id ? document.getElementById(`delete-form-${id}`) : null;
                    confirmationModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    return;
                }

                const forceDeleteBtn = e.target.closest('.force-delete-button');
                if (forceDeleteBtn && confirmationModal) {
                    e.preventDefault();
                    const id = forceDeleteBtn.getAttribute('data-id');
                    deleteFormToSubmit = id ? document.getElementById(`force-delete-form-${id}`) : null;
                    confirmationModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            });

            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (deleteFormToSubmit) {
                        deleteFormToSubmit.submit();
                    }
                });
            }

            const newCandidateForm = document.getElementById('new-candidate-form');
            if (newCandidateForm) {
                newCandidateForm.addEventListener('submit', function() {
                    const descriptionHidden = document.getElementById('new-candidate-description');
                    const platformsHidden = document.getElementById('new-candidate-platforms');
                    const descriptionEditor = document.getElementById('new-description-editor');
                    const platformsEditor = document.getElementById('new-platforms-editor');

                    if (descriptionHidden && descriptionEditor && (!descriptionHidden.value || descriptionHidden.value.trim() === '')) {
                        descriptionHidden.value = descriptionEditor.innerHTML;
                    }
                    if (platformsHidden && platformsEditor && (!platformsHidden.value || platformsHidden.value.trim() === '')) {
                        platformsHidden.value = platformsEditor.innerHTML;
                    }
                });
            }

            const editCandidateForm = document.getElementById('edit-candidate-form');
            if (editCandidateForm) {
                editCandidateForm.addEventListener('submit', function() {
                    const descriptionHidden = document.getElementById('edit-candidate-description');
                    const platformsHidden = document.getElementById('edit-candidate-platforms');
                    const descriptionEditor = document.getElementById('edit-description-editor');
                    const platformsEditor = document.getElementById('edit-platforms-editor');

                    if (descriptionHidden && descriptionEditor) {
                        descriptionHidden.value = descriptionEditor.innerHTML;
                    }
                    if (platformsHidden && platformsEditor) {
                        platformsHidden.value = platformsEditor.innerHTML;
                    }
                });
            }

            // Profile image handlers
            const newCandidateImage = document.getElementById('new-candidate-image');
            if (newCandidateImage) newCandidateImage.addEventListener('change', function() {
                const filename = this.files[0]?.name || 'Choose profile image...';
                document.getElementById('new-profile-filename').textContent = filename;
                
                if (this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('new-candidate-image-preview');
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            const editProfileImage = document.getElementById('edit-profile-image');
            if (editProfileImage) editProfileImage.addEventListener('change', function() {
                const filename = this.files[0]?.name || 'Choose profile image...';
                document.getElementById('edit-profile-filename').textContent = filename;
                
                if (this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('edit-profile-image-preview');
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Banner image handlers
            const newCandidateBanner = document.getElementById('new-candidate-banner');
            if (newCandidateBanner) newCandidateBanner.addEventListener('change', function() {
                const filename = this.files[0]?.name || 'Choose banner image...';
                document.getElementById('new-banner-filename').textContent = filename;
                
                if (this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('new-candidate-banner-preview');
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

            const editBannerImage = document.getElementById('edit-banner-image');
            if (editBannerImage) editBannerImage.addEventListener('change', function() {
                const filename = this.files[0]?.name || 'Choose banner image...';
                document.getElementById('edit-banner-filename').textContent = filename;
                
                if (this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.getElementById('edit-banner-image-preview');
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endpush
@endsection
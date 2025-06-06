@extends('layouts.admin')

@section('title', 'Candidate')
@section('dashboard-title', 'Candidates')

@section('content')
    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Manage Candidates</h2>
        <p class="text-gray-600 text-lg">Here you can manage all the candidates for the elections.</p>
    </section>

    <section class="my-8">
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
                    <!-- Pagination at the top - properly aligned -->
                    <div id="pagination-container-top" class="flex items-center">
                        {{ $candidates->links('components.pagination') }}
                    </div>
                    
                    <button 
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                        id="new-candidate-btn">
                        New Candidate
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border-collapse">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Name</th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Course</th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Status</th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="candidates-table-body">
                        @foreach ($candidates as $candidate)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $candidate->name }}</td>
                                <td class="py-2 px-4 border-b">{{ optional($candidate->course)->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $candidate->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $candidate->status }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex gap-2">
                                        <button 
                                            type="button"
                                            class="view-button bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                                            data-id="{{ $candidate->id }}"
                                            data-candidate="{{ $candidate->name }}"
                                            data-course="{{ optional($candidate->course)->name ?? 'N/A' }}"
                                            data-party="{{ $candidate->party ? $candidate->party->name : 'No Party' }}"
                                            data-description="{{ $candidate->description ?? 'No description available.' }}"
                                            data-status="{{ $candidate->status }}"
                                            data-profile-image="{{ $candidate->profile_img ? asset('storage/upload/profile-picture/' . $candidate->profile_img) : asset('images/default-profile.jpg') }}"
                                            data-banner-image="{{ $candidate->banner_img ? asset('storage/upload/banner/' . $candidate->banner_img) : asset('images/default-banner.png') }}"
                                            data-platforms="{{ $candidate->platforms ?? 'No Platforms' }}"
                                            data-related-events="Organized Tech Fest 2023, Led Community Service Day 2024"
                                            data-joined-elections='[{"year": "2023", "position": "Vice President", "winner": true}, {"year": "2024", "position": "Representative", "winner": false}]'>
                                            View
                                        </button>

                                        <button 
                                            type="button"
                                            class="edit-button bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700"
                                            data-id="{{ $candidate->id }}"
                                            data-candidate="{{ $candidate->name }}"
                                            data-course="{{ optional($candidate->course)->name ?? '' }}"
                                            data-course-id="{{ optional($candidate->course)->id ?? '' }}"
                                            data-party="{{ $candidate->party ? $candidate->party->name : 'No Party' }}"
                                            data-party-id="{{ $candidate->party ? $candidate->party->id : '' }}"
                                            data-description="{{ $candidate->description ?? '' }}"
                                            data-status="{{ $candidate->status }}"
                                            data-profile-image="{{ $candidate->profile_img ? asset('storage/upload/profile-picture/' . $candidate->profile_img) : asset('images/default-profile.jpg') }}"
                                            data-banner-image="{{ $candidate->banner_img ? asset('storage/upload/banner/' . $candidate->banner_img) : asset('images/default-banner.png') }}"
                                            data-platforms="{{ $candidate->platforms ?? '' }}">
                                            Edit
                                        </button>

                                        <form method="POST" action="{{ route('admin.candidates.destroy', $candidate->id) }}" id="delete-form-{{ $candidate->id }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" 
                                                class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 delete-button"
                                                data-id="{{ $candidate->id }}">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Hidden pagination container for backup -->
            <div id="pagination-container" class="mt-4" style="display: none;">
                {{ $candidates->links('components.pagination') }}
            </div>
        </div>
    </section>

    <!-- Candidate Modal (View) -->
    <div class="fixed inset-0 overflow-y-auto hidden" id="candidate-modal" style="z-index: 9999;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>

            <div class="relative bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="bg-white p-6">
                    <img id="candidate-banner-image" class="w-full h-32 object-cover rounded-lg mb-4" alt="Banner Image">
                    <div class="flex items-center mb-4">
                        <img id="candidate-profile-image" class="w-16 h-16 object-cover rounded-full mr-4" alt="Profile Image">
                        <h3 class="text-2xl font-bold" id="candidate-name"></h3>
                    </div>
                    <p class="text-gray-800 mb-2"><strong>Status:</strong> <span id="candidate-status"></span></p>
                    <p class="text-gray-800 mb-2"><strong>Course:</strong> <span id="candidate-course"></span></p>
                    <p class="text-gray-800 mb-2"><strong>Party:</strong> <span id="candidate-party"></span></p>
                    <p class="text-gray-800 mb-2" id="candidate-description"></p>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Platforms</h4>
                    <ul class="list-disc list-inside text-gray-800 mb-2" id="candidate-platforms"></ul>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Related Events</h4>
                    <ul class="list-disc list-inside text-gray-800 mb-2" id="candidate-related-events"></ul>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Joined Elections</h4>
                    <ul class="list-disc list-inside text-gray-800 mb-2" id="candidate-joined-elections"></ul>
                    <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mt-4" id="close-modal">Close</button>
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
                    <form id="edit-candidate-form">
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
                            <img id="edit-profile-image-preview" src="" alt="Profile Image" class="w-32 h-32 rounded-full mt-2 hidden object-cover">
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
                            <img id="edit-banner-image-preview" src="" alt="Banner Image" class="w-full h-40 object-cover rounded-lg mt-2 hidden">
                        </div>

                        <div class="mb-4">
                            <label for="edit-candidate-description" class="block font-medium">Description</label>
                            <div id="edit-description-editor" class="bg-white border rounded shadow min-h-32"></div>
                            <input type="hidden" id="edit-candidate-description" name="description">
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
                            <div id="edit-platforms-editor" class="bg-white border rounded shadow min-h-32"></div>
                            <input type="hidden" id="edit-candidate-platforms" name="platforms">
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
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity modal-backdrop"></div>
            <div class="relative bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Create New Candidate</h3>
                    <form id="new-candidate-form">
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
                            <img id="new-candidate-image-preview" src="" alt="Profile Preview" class="w-32 h-32 object-cover rounded-full mt-2 hidden">
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
                            <img id="new-candidate-banner-preview" src="" alt="Banner Preview" class="w-full h-40 object-cover rounded-lg mt-2 hidden">
                        </div>

                        <div class="mt-4">
                            <label class="block mb-2" for="new-candidate-description">Description</label>
                            <div id="new-description-editor" class="bg-white border rounded shadow min-h-32"></div>
                            <input type="hidden" id="new-candidate-description" name="description">
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
                            <div id="new-platforms-editor" class="bg-white border rounded shadow min-h-32"></div>
                            <input type="hidden" id="new-candidate-platforms" name="platforms">
                        </div>
                        <div class="mb-4">
                            <label for="new-candidate-status" class="block font-medium">Status</label>
                            <select id="new-candidate-status" name="status" class="w-full p-2 border border-gray-300 rounded" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="button" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400" id="cancel-new-candidate">Cancel</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 ml-2" id="save-new-candidate">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    <!-- Load the SearchBox component script -->
    @vite(['resources/js/admin/candidates.js'])
    <!-- Load the Candidates script -->
    @vite(['resources/js/components/search-box.js'])

    <script>
        // Custom file input functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Profile image handlers
            document.getElementById('new-candidate-image').addEventListener('change', function() {
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

            document.getElementById('edit-profile-image').addEventListener('change', function() {
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
            document.getElementById('new-candidate-banner').addEventListener('change', function() {
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

            document.getElementById('edit-banner-image').addEventListener('change', function() {
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
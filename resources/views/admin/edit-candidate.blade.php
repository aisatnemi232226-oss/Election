@extends('layouts.admin')

@section('title', 'New Candidate')
@section('dashboard-title', 'Candidates Details')

@section('content')
    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Candidate Details</h2>
        <p class="text-gray-600 text-lg">Here you can manage the candidate details and update their personal information.</p>
    </section>
    
    <section class="my-8">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <form action="{{ route('admin.candidates.update', $candidate->id) }}" method="POST" enctype="multipart/form-data" id="candidate-form">
                @csrf
                @method('PUT')
                <div class="mb-4 flex flex-col items-start">
                    <!-- Banner Image Preview -->
                    <div class="relative w-[1200px] h-[312px] overflow-hidden mt-4">
                        <img id="bannerImage" src="{{ $candidate->banner_img ? asset('storage/upload/banner/' . $candidate->banner_img) : asset('images/default-banner.png') }}" 
                            alt="Banner"
                            class="w-[1200px] h-[312px] rounded object-cover border shadow cursor-pointer">
                    </div>

                    <!-- Hidden File Input -->
                    <!-- Banner Image -->
                    <input type="file" id="bannerInput" name="banner_img" class="hidden" accept="image/*" {{ !$candidate->banner_img ? 'required' : '' }}>
                </div>

                <div class="mb-4 flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-8">
                    <!-- Profile Picture Section -->
                    <div class="flex flex-col items-center">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Profile Picture</label>

                        <!-- Profile Image Preview -->
                        <div class="relative w-40 h-40">
                            <img id="profileImage" src="{{ $candidate->profile_img ? asset('storage/upload/profile-picture/' . $candidate->profile_img) : asset('images/default-profile.jpg') }}" 
                                alt="Profile Picture"
                                class="w-40 h-40 rounded object-cover border shadow cursor-pointer">
                        </div>

                        <!-- Hidden File Input -->
                        <!-- Profile Image -->
                        <input type="file" id="profileInput" name="profile_img" class="hidden" accept="image/*" {{ !$candidate->profile_img ? 'required' : '' }}>
                    </div>

                    <!-- ID Number, Name, Course & Status Section -->
                    <div class="flex flex-col w-full space-y-4">
                        <div class="flex flex-col sm:flex-row sm:space-x-4">
                            <!-- ID Number -->
                            <div class="flex flex-col sm:w-1/6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="idnumber">ID Number</label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                    name="id_number" id="idnumber" type="text" placeholder="ID Number" value="{{ $candidate->id_number ?? 'No ID Number' }}" required>
                            </div>
                            <!-- Name -->
                            <div class="flex flex-col sm:w-2/6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-400 leading-tight focus:outline-none focus:shadow-outline" 
                                    name="name" id="name" type="text" value="{{ $candidate->name ?? 'No Name' }}" readonly required>
                            </div>
                            <!-- Course -->
                            <div class="flex flex-col sm:w-2/6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="course">Course</label>
                                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-400 leading-tight focus:outline-none focus:shadow-outline" 
                                    name="course" id="course" type="text" value="{{ $candidate->course->name ?? 'No Course' }}" readonly required>
                            </div>
                            <!-- Party -->
                            <div class="flex flex-col sm:w-2/6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="party">Party</label>
                                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                        name="party" id="party" class="form-control" required>
                                    <option value="" disabled selected>{{ $candidate->party ? $candidate->party->name : 'No Party' }}</option>
                                    @foreach ($parties as $party)
                                        <option value="{{ $party->id }}" 
                                            @if($candidate->party && $candidate->party->id == $party->id) selected @endif>
                                            {{ $party->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="flex flex-col sm:w-1/6">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">Status</label>
                                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                    name="status" id="status" required>
                                    <option value="No Status" disabled {{ is_null($candidate->status) ? 'selected' : '' }}>No Status</option>
                                    <option value="Active" {{ $candidate->status == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ $candidate->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:space-x-4">
                            <!-- Related Events -->
                            <div class="flex flex-col w-full">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="related-events">Related Events</label>
                                <textarea class="shadow appearance-none border rounded w-full px-3 py-2 text-gray-400 leading-tight focus:outline-none focus:shadow-outline min-h-18 resize-y" id="related-events" readonly>
Organized Tech Fest 2022
Led Community Service Day 2021</textarea>
                            </div>
                            <!-- Joined Elections -->
                            <div class="flex flex-col w-full">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="joined-elections">Joined Elections</label>
                                <textarea class="shadow appearance-none border rounded w-full px-3 py-2 text-gray-400 leading-tight focus:outline-none focus:shadow-outline min-h-18 resize-y" id="joined-elections" readonly>
2022 - Vice President (Winner)
2021 - Representative</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row items-start lg:items-center space-y-4 lg:space-x-8 lg:space-y-0 mb-4 w-full">
                    <!-- Platforms -->
                    <div class="flex flex-col w-full">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Platforms</label>
                        <div id="platforms-editor" class="bg-white border rounded shadow min-h-32"></div>
                        <input type="hidden" name="platforms" id="platforms-input">
                    </div>

                    <!-- Description -->
                    <div class="flex flex-col w-full">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <div id="description-editor" class="bg-white border rounded shadow min-h-32"></div>
                        <input type="hidden" name="description" id="description-input">
                    </div>
                </div>
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex justify-between">
                    <button type="button" onclick="location.href='{{ route('admin.candidates.index') }}'" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Back
                    </button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" type="submit">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Quill Script -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Banner Image Upload
            document.getElementById("bannerImage").addEventListener("click", function () {
                document.getElementById("bannerInput").click();
            });

            document.getElementById("bannerInput").addEventListener("change", function (event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith("image/")) {
                    const reader = new FileReader();
                    reader.onload = (e) => document.getElementById("bannerImage").src = e.target.result;
                    reader.readAsDataURL(file);
                } else {
                    alert("Please upload a valid image file.");
                    this.value = "";
                }
            });

            // Profile Image Upload
            document.getElementById("profileImage").addEventListener("click", function () {
                document.getElementById("profileInput").click();
            });

            document.getElementById("profileInput").addEventListener("change", function (event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith("image/")) {
                    const reader = new FileReader();
                    reader.onload = (e) => document.getElementById("profileImage").src = e.target.result;
                    reader.readAsDataURL(file);
                } else {
                    alert("Please upload a valid image file.");
                    this.value = "";
                }
            });

            // Quill Initialization for Platforms and Description
            const fullToolbar = [
                [{ 'font': [] }, { 'size': [] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                ['link'],
                ['image'],
                ['blockquote']
            ];

            // Initialize Quill for Platforms
            const platformsEditor = new Quill("#platforms-editor", {
                theme: "snow",
                modules: {
                    toolbar: fullToolbar
                }
            });

            // Initialize Quill for Description
            const descriptionEditor = new Quill("#description-editor", {
                theme: "snow",
                modules: {
                    toolbar: fullToolbar
                }
            });

            // Pre-fill Quill editors with existing content
            platformsEditor.root.innerHTML = @json($candidate->platforms);
            descriptionEditor.root.innerHTML = @json($candidate->description);

            // Image upload for Quill
            const toolbar = platformsEditor.getModule('toolbar');
            toolbar.addHandler('image', function () {
                let input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = function () {
                    const file = input.files[0];
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const range = platformsEditor.getSelection();
                        platformsEditor.insertEmbed(range.index, 'image', e.target.result);
                    };
                    reader.readAsDataURL(file);
                };
            });

            // Handle form submission
            document.getElementById("candidate-form").onsubmit = function () {
                // Capture the content from Quill editors and set it in the hidden inputs
                document.getElementById("platforms-input").value = platformsEditor.root.innerHTML;
                document.getElementById("description-input").value = descriptionEditor.root.innerHTML;
            };
        });
    </script>
@endsection

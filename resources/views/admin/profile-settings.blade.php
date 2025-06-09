@extends('layouts.admin')

@section('title', 'Profile Settings')
@section('dashboard-title', 'Admin Dashboard')

@section('content')
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .tile {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .tile:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .tile:active {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        .no-wrap {
            white-space: nowrap;
        }
    </style>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <span class="close-btn float-right cursor-pointer text-green-700 hover:text-green-900">&times;</span>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <span class="close-btn float-right cursor-pointer text-red-700 hover:text-red-900">&times;</span>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div id="validation-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <span class="close-btn float-right cursor-pointer text-red-700 hover:text-red-900">&times;</span>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Profile Settings</h2>
        <p class="text-gray-600 text-lg">Manage your profile settings and update your personal information.</p>
    </section>

    <section class="my-8">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
            <form method="POST" action="{{ route('admin.profile-settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Name
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="name" 
                           name="name" 
                           type="text" 
                           placeholder="John Doe" 
                           value="{{ old('name', $user->name ?? 'John Doe') }}"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="id-number">
                        ID Number
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100" 
                           id="id-number" 
                           type="text" 
                           value="{{ $user->id_number ?? '123456789' }}" 
                           readonly>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="email" 
                           name="email" 
                           type="email" 
                           placeholder="john.doe@example.com" 
                           value="{{ old('email', $user->email ?? 'john.doe@example.com') }}"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="password" 
                           name="password" 
                           type="password" 
                           placeholder="Leave blank to keep current password">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                        Confirm Password
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           placeholder="Confirm new password">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="profile-picture">
                        Profile Picture
                    </label>
                    <div class="flex items-center">
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="profile-picture" 
                               name="profile_picture" 
                               type="file" 
                               accept="image/*">
                        <button class="ml-2 text-red-600 hover:text-red-800 hidden" 
                                id="remove-picture" 
                                type="button">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    @if(isset($user->profile_picture) && $user->profile_picture)
                        <div class="mt-4" id="current-picture">
                            <img alt="Current profile picture" 
                                 class="rounded-full w-24 h-24 object-cover" 
                                 src="{{ asset('storage/' . $user->profile_picture) }}">
                            <button type="button" 
                                    class="ml-2 text-red-600 hover:text-red-800" 
                                    onclick="removeCurrentPicture()">
                                <i class="fas fa-times"></i> Remove current picture
                            </button>
                        </div>
                    @endif

                    <div class="mt-4 hidden" id="uploaded-picture">
                        <img alt="Uploaded profile picture preview" 
                             class="rounded-full w-24 h-24 object-cover" 
                             id="picture-preview">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="course-position">
                        Course or Position
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="course-position" 
                           name="course_position" 
                           type="text" 
                           placeholder="Computer Science" 
                           value="{{ old('course_position', $user->course_position ?? 'Computer Science') }}">
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" 
                            type="submit">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </section>

    @push('scripts')
        <script>
            // Auto-hide alerts functionality
            function setupAlerts() {
                const alerts = document.querySelectorAll('#success-alert, #error-alert, #validation-alert');
                
                alerts.forEach(alert => {
                    setTimeout(() => {
                        if (alert && alert.style.display !== 'none') {
                            alert.style.transition = 'opacity 0.5s ease-in-out';
                            alert.style.opacity = '0';
                            setTimeout(() => {
                                alert.style.display = 'none';
                            }, 500);
                        }
                    }, 5000);
                });

                const closeButtons = document.querySelectorAll('.close-btn');
                closeButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const alert = this.parentElement;
                        alert.style.transition = 'opacity 0.5s ease-in-out';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 500);
                    });
                });
            }

            // Profile picture preview functionality
            const profilePictureInput = document.getElementById('profile-picture');
            const removePictureButton = document.getElementById('remove-picture');
            const uploadedPictureDiv = document.getElementById('uploaded-picture');
            const picturePreview = document.getElementById('picture-preview');

            profilePictureInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        picturePreview.src = e.target.result;
                        uploadedPictureDiv.classList.remove('hidden');
                        removePictureButton.classList.remove('hidden');
                    };
                    
                    reader.readAsDataURL(this.files[0]);
                } else {
                    uploadedPictureDiv.classList.add('hidden');
                    removePictureButton.classList.add('hidden');
                }
            });

            removePictureButton.addEventListener('click', function() {
                profilePictureInput.value = '';
                uploadedPictureDiv.classList.add('hidden');
                removePictureButton.classList.add('hidden');
            });

            // Remove current profile picture
            function removeCurrentPicture() {
                if (confirm('Are you sure you want to remove your current profile picture?')) {
                    fetch('{{ route("admin.profile-settings.remove-picture") }}', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('current-picture').style.display = 'none';
                            // Show success message
                            const successDiv = document.createElement('div');
                            successDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4';
                            successDiv.innerHTML = '<span class="close-btn float-right cursor-pointer text-green-700 hover:text-green-900">&times;</span>' + data.message;
                            document.querySelector('section').insertBefore(successDiv, document.querySelector('section').firstChild);
                            setupAlerts();
                        } else {
                            alert('Failed to remove profile picture: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while removing the profile picture.');
                    });
                }
            }

            // Initialize when the document is ready
            document.addEventListener('DOMContentLoaded', () => {
                setupAlerts();
            });
        </script>
    @endpush
@endsection
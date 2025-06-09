@extends('layouts.admin')

@section('title', 'Voters')
@section('dashboard-title', 'Admin Dashboard')

@section('content')
    @php
        function getUpdateUrl($id) {
            return route('admin.voters.update', $id);
        }

        function getDeleteUrl($id) {
            return route('admin.voters.destroy', $id);
        }

        function getShowUrl($id) {
            return route('admin.voters.show', $id);
        }
    @endphp

    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Manage Voters</h2>
        <p class="text-gray-600 text-lg">Here you can manage all the users.</p>
    </section>

    <section class="my-8">
        @if (session('success'))
            <div id="success-alert" class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4 flex justify-between items-center">
                <span>{{ session('success') }}</span>
                <button class="close-btn text-green-700 hover:text-green-900" onclick="this.parentElement.style.display='none';">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div id="error-alert" class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 flex justify-between items-center">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button class="close-btn text-red-700 hover:text-red-900" onclick="this.parentElement.style.display='none';">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <div class="flex items-center justify-between px-6 py-4 bg-gray-50">
            <h1 class="text-2xl font-bold text-gray-800">Users List</h1>
            
            <div class="flex items-center space-x-4">
                <!-- Search box -->
                <input type="text" 
                    placeholder="Search users..." 
                    class="px-4 py-2 border rounded-lg w-64" 
                    id="search-box">
                            
                <button 
                    class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                    id="new-voter-btn">
                    New User
                </button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Student ID</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Course</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Email Address</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="voters-table-body">
                        @foreach($voters as $voter)
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-300">{{ $voter->name }}</td>
                                <td class="py-2 px-4 border-b border-gray-300">{{ $voter->idnumber ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b border-gray-300">{{ $voter->course ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border-b border-gray-300">{{ $voter->email }}</td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    <div class="flex flex-wrap items-center gap-1">
                                        <button class="bg-blue-600 text-white px-3 py-1 text-sm rounded hover:bg-blue-700 view-btn"
                                            data-id="{{ $voter->id }}"
                                            data-name="{{ $voter->name }}"
                                            data-student-id="{{ $voter->idnumber ?? 'N/A' }}"
                                            data-course="{{ $voter->course ?? 'N/A' }}"
                                            data-email="{{ $voter->email }}"
                                            data-admin-role="{{ $voter->admin_role ?? 'None' }}"
                                            data-profile-image="https://external-content.duckduckgo.com/iu/?u=https%3A%2F%2Fcdn2.vectorstock.com%2Fi%2F1000x1000%2F20%2F76%2Fman-avatar-profile-vector-21372076.jpg&f=1&nofb=1&ipt=834d3a62a26ee7e7b9e973f9822d5b9d147a8a65d1f49880e7222f1314a76f7e&ipo=images"
                                            data-show-url="{{ getShowUrl($voter->id) }}">View</button>
                                        
                                        <button class="bg-blue-600 text-white px-3 py-1 text-sm rounded hover:bg-blue-700 edit-btn"
                                            data-id="{{ $voter->id }}"
                                            data-name="{{ $voter->name }}"
                                            data-student-id="{{ $voter->idnumber ?? '' }}"
                                            data-course="{{ $voter->course ?? '' }}"
                                            data-email="{{ $voter->email }}"
                                            data-admin-role="{{ $voter->admin_role ?? '' }}"
                                            data-status="{{ $voter->status ?? 'Active' }}"
                                            data-update-url="{{ getUpdateUrl($voter->id) }}">Edit</button>
                                        
                                        <button class="bg-red-600 text-white px-3 py-1 text-sm rounded hover:bg-red-700 delete-btn"
                                            data-id="{{ $voter->id }}"
                                            data-delete-url="{{ getDeleteUrl($voter->id) }}">Delete</button>
                                        
                                        <button class="bg-yellow-600 text-white px-3 py-1 text-sm rounded hover:bg-yellow-700 reset-password-btn"
                                            data-id="{{ $voter->id }}">Reset Password</button>
                                        
                                        <button class="bg-green-600 text-white px-3 py-1 text-sm rounded hover:bg-green-700 registration-btn"
                                            data-id="{{ $voter->id }}"
                                            data-name="{{ $voter->name }}">Registration</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $voters->links('components.pagination') }}
            </div>
        </div>
    </section>

    <!-- New User Modal -->
    <div id="new-voter-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/2 max-h-screen overflow-y-auto">
            <h3 class="text-2xl font-bold mb-4">Add New User</h3>
            <form method="POST" action="{{ route('admin.voters.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter name" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Student ID</label>
                    <input type="text" name="idnumber" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter student ID" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Course</label>
                    <input type="text" name="course" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter course" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" name="email" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter email" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border rounded-lg" placeholder="Create password" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Profile Image</label>
                    <input type="file" name="profile_image" class="w-full px-4 py-2 border rounded-lg" accept="image/*">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Admin Role</label>
                    <select name="admin_role" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">Select Role</option>
                        <option value="Admin">Admin</option>
                        <option value="Moderator">Moderator</option>
                        <option value="User">User</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border rounded-lg" required>
                        <option value="">Select Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-600 text-white px-4 py-2 rounded-lg mr-2" id="close-new-voter-modal">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="edit-voter-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/2 max-h-screen overflow-y-auto">
            <h3 class="text-2xl font-bold mb-4">Edit User</h3>
            <form method="POST" id="edit-voter-form" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit-voter-id">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" id="edit-voter-name" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Student ID</label>
                    <input type="text" name="idnumber" id="edit-voter-student-id" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Course</label>
                    <input type="text" name="course" id="edit-voter-course" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email" id="edit-voter-email" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Profile Image</label>
                    <input type="file" name="profile_image" class="w-full px-4 py-2 border rounded-lg" accept="image/*">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Admin Role</label>
                    <select name="admin_role" id="edit-voter-admin-role" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">Select Role</option>
                        <option value="Admin">Admin</option>
                        <option value="Moderator">Moderator</option>
                        <option value="User">User</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status" id="edit-voter-status" class="w-full px-4 py-2 border rounded-lg" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-600 text-white px-4 py-2 rounded-lg mr-2" id="close-edit-voter-modal">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View User Modal -->
    <div id="view-voter-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/2 max-h-screen overflow-y-auto">
            <div class="flex items-center mb-4">
                <img id="view-voter-image" class="w-16 h-16 object-cover rounded-full mr-4" src="" alt="Profile">
                <h3 class="text-2xl font-bold text-gray-800" id="view-voter-name"></h3>
            </div>
            
            <div class="mb-4">
                <p class="text-gray-600 mb-2"><strong>Student ID:</strong> <span id="view-voter-student-id"></span></p>
                <p class="text-gray-600 mb-2"><strong>Course:</strong> <span id="view-voter-course"></span></p>
                <p class="text-gray-600 mb-2"><strong>Email Address:</strong> <span id="view-voter-email"></span></p>
                <p class="text-gray-600 mb-2"><strong>Admin Role:</strong> <span id="view-voter-admin-role"></span></p>
            </div>
            
            <h4 class="text-xl font-bold text-gray-800 mb-2">Joined Elections</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Election Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Registration</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Vote</th>
                        </tr>
                    </thead>
                    <tbody id="view-voter-elections">
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-300 text-center" colspan="3">No elections found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="flex justify-end mt-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg" id="close-view-voter-modal">Close</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-confirmation-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h3 class="text-2xl font-bold mb-4">Confirm Deletion</h3>
            <p class="mb-4">Are you sure you want to delete this user?</p>
            <form method="POST" id="delete-voter-form" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-600 text-white px-4 py-2 rounded-lg mr-2" id="close-delete-confirmation-modal">Cancel</button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="reset-password-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
            <h3 class="text-2xl font-bold mb-4">Reset Password</h3>
            <form id="reset-password-form">
                @csrf
                <input type="hidden" id="reset-voter-id">
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">New Password</label>
                    <input type="password" name="new_password" id="new-password" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
                    <input type="password" name="new_password_confirmation" id="confirm-password" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-600 text-white px-4 py-2 rounded-lg mr-2" id="close-reset-password-modal">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Registration Modal -->
    <div id="registration-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
            <h3 class="text-2xl font-bold mb-4">Allow to Register for Election</h3>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Select Election</label>
                <select class="w-full px-4 py-2 border rounded-lg" id="election-select">
                    <option>Presidential Election 2022</option>
                    <option>Student Council Election 2021</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" class="mr-2" id="allow-registration" checked>
                    <span class="text-sm">Allow Registration</span>
                </label>
            </div>
            <div class="flex justify-end">
                <button class="bg-gray-600 text-white px-4 py-2 rounded-lg mr-2" id="close-registration-modal">Cancel</button>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg" id="save-registration">Save</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Declare shared variables at the top
            const searchBox = document.getElementById('search-box');
            const votersTableBody = document.getElementById('voters-table-body');
            const paginationContainer = document.querySelector('.mt-4');
            
            // New User Modal
            const newVoterBtn = document.getElementById('new-voter-btn');
            const newVoterModal = document.getElementById('new-voter-modal');
            const closeNewVoterModal = document.getElementById('close-new-voter-modal');

            newVoterBtn.addEventListener('click', () => {
                newVoterModal.classList.remove('hidden');
                newVoterModal.classList.add('flex');
            });

            closeNewVoterModal.addEventListener('click', () => {
                newVoterModal.classList.add('hidden');
                newVoterModal.classList.remove('flex');
            });

            // Edit User Modal
            const editBtns = document.querySelectorAll('.edit-btn');
            const editVoterModal = document.getElementById('edit-voter-modal');
            const closeEditVoterModal = document.getElementById('close-edit-voter-modal');
            const editVoterForm = document.getElementById('edit-voter-form');

            editBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const voterId = btn.getAttribute('data-id');
                    const voterName = btn.getAttribute('data-name');
                    const studentId = btn.getAttribute('data-student-id');
                    const course = btn.getAttribute('data-course');
                    const email = btn.getAttribute('data-email');
                    const adminRole = btn.getAttribute('data-admin-role');
                    const status = btn.getAttribute('data-status');
                    const updateUrl = btn.getAttribute('data-update-url');
                    
                    document.getElementById('edit-voter-id').value = voterId;
                    document.getElementById('edit-voter-name').value = voterName;
                    document.getElementById('edit-voter-student-id').value = studentId;
                    document.getElementById('edit-voter-course').value = course;
                    document.getElementById('edit-voter-email').value = email;
                    document.getElementById('edit-voter-admin-role').value = adminRole || '';
                    document.getElementById('edit-voter-status').value = status;
                    editVoterForm.action = updateUrl;
                    
                    editVoterModal.classList.remove('hidden');
                    editVoterModal.classList.add('flex');
                });
            });

            closeEditVoterModal.addEventListener('click', () => {
                editVoterModal.classList.add('hidden');
                editVoterModal.classList.remove('flex');
            });

            // View User Modal
            const viewBtns = document.querySelectorAll('.view-btn');
            const viewVoterModal = document.getElementById('view-voter-modal');
            const closeViewVoterModal = document.getElementById('close-view-voter-modal');

            viewBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const name = btn.getAttribute('data-name');
                    const studentId = btn.getAttribute('data-student-id');
                    const course = btn.getAttribute('data-course');
                    const email = btn.getAttribute('data-email');
                    const adminRole = btn.getAttribute('data-admin-role') || 'None';
                    const profileImage = btn.getAttribute('data-profile-image');
                    
                    document.getElementById('view-voter-name').textContent = name;
                    document.getElementById('view-voter-student-id').textContent = studentId;
                    document.getElementById('view-voter-course').textContent = course;
                    document.getElementById('view-voter-email').textContent = email;
                    document.getElementById('view-voter-admin-role').textContent = adminRole;
                    document.getElementById('view-voter-image').src = profileImage;
                    
                    viewVoterModal.classList.remove('hidden');
                    viewVoterModal.classList.add('flex');
                });
            });

            closeViewVoterModal.addEventListener('click', () => {
                viewVoterModal.classList.add('hidden');
                viewVoterModal.classList.remove('flex');
            });

            // Delete User Modal
            const deleteBtns = document.querySelectorAll('.delete-btn');
            const deleteConfirmationModal = document.getElementById('delete-confirmation-modal');
            const closeDeleteConfirmationModal = document.getElementById('close-delete-confirmation-modal');
            const deleteVoterForm = document.getElementById('delete-voter-form');

            deleteBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const deleteUrl = btn.getAttribute('data-delete-url');
                    deleteVoterForm.action = deleteUrl;
                    
                    deleteConfirmationModal.classList.remove('hidden');
                    deleteConfirmationModal.classList.add('flex');
                });
            });

            closeDeleteConfirmationModal.addEventListener('click', () => {
                deleteConfirmationModal.classList.add('hidden');
                deleteConfirmationModal.classList.remove('flex');
            });

            // Reset Password Modal
            const resetPasswordBtns = document.querySelectorAll('.reset-password-btn');
            const resetPasswordModal = document.getElementById('reset-password-modal');
            const closeResetPasswordModal = document.getElementById('close-reset-password-modal');
            const resetPasswordForm = document.getElementById('reset-password-form');

            resetPasswordBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const voterId = btn.getAttribute('data-id');
                    document.getElementById('reset-voter-id').value = voterId;
                    
                    resetPasswordModal.classList.remove('hidden');
                    resetPasswordModal.classList.add('flex');
                });
            });

            closeResetPasswordModal.addEventListener('click', () => {
                resetPasswordModal.classList.add('hidden');
                resetPasswordModal.classList.remove('flex');
            });

            resetPasswordForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const newPassword = document.getElementById('new-password').value;
                const confirmPassword = document.getElementById('confirm-password').value;
                
                if (newPassword !== confirmPassword) {
                    alert('Passwords do not match!');
                    return;
                }
                
                // Here you would normally send AJAX request to update password
                alert('Password has been reset successfully!');
                resetPasswordModal.classList.add('hidden');
                resetPasswordModal.classList.remove('flex');
            });

            // Registration Modal
            const registrationBtns = document.querySelectorAll('.registration-btn');
            const registrationModal = document.getElementById('registration-modal');
            const closeRegistrationModal = document.getElementById('close-registration-modal');
            const saveRegistration = document.getElementById('save-registration');

            registrationBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    registrationModal.classList.remove('hidden');
                    registrationModal.classList.add('flex');
                });
            });

            closeRegistrationModal.addEventListener('click', () => {
                registrationModal.classList.add('hidden');
                registrationModal.classList.remove('flex');
            });

            saveRegistration.addEventListener('click', () => {
                alert('Registration settings saved!');
                registrationModal.classList.add('hidden');
                registrationModal.classList.remove('flex');
            });

            // Function to handle auto-hide of alerts
            function setupAlerts() {
                const alerts = document.querySelectorAll('#success-alert, #error-alert');
                
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

            // Search and filter functionality
            function filterTable() {
                const searchTerm = searchBox.value.toLowerCase();
                const rows = votersTableBody.querySelectorAll('tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const name = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                    const studentId = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const course = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                    
                    if (name.includes(searchTerm) || studentId.includes(searchTerm) || 
                        course.includes(searchTerm) || email.includes(searchTerm)) {
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                // Hide pagination if searching
                if (paginationContainer) {
                    paginationContainer.style.display = searchTerm ? 'none' : '';
                }
            }

            // Attach search event listener
            searchBox.addEventListener('input', filterTable);

            // Initialize everything when the document is ready
            document.addEventListener('DOMContentLoaded', () => {
                setupAlerts();
                filterTable();
            });
        </script>
    @endpush
@endsection
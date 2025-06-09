@extends('layouts.admin')

@section('title', 'Admin Roles')
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

    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Admin Roles</h2>
        <p class="text-gray-600 text-lg">Here you can manage the roles and permissions of the administrators.</p>
    </section>

    <section class="my-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-3xl font-bold text-gray-800">Roles List</h3>
            <button id="new-role-button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">New Role</button>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="overflow-x-auto">
                <form id="bulk-action-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Role Name</th>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Description</th>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Permissions</th>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr data-role-id="{{ $role->id }}">
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <input type="checkbox" 
                                               class="form-checkbox h-5 w-5 text-blue-600 role-checkbox" 
                                               name="role_ids[]" 
                                               value="{{ $role->id }}">
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300">{{ $role->name }}</td>
                                    <td class="py-2 px-4 border-b border-gray-300">{{ $role->description }}</td>
                                    <td class="py-2 px-4 border-b border-gray-300">{{ $role->formatted_permissions }}</td>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 edit-button mr-2" 
                                                data-role-id="{{ $role->id }}"
                                                data-role-name="{{ $role->name }}"
                                                data-role-description="{{ $role->description }}"
                                                data-role-permissions="{{ json_encode($role->permissions) }}">
                                            Edit
                                        </button>
                                        <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 delete-button" 
                                                data-role-id="{{ $role->id }}"
                                                data-role-name="{{ $role->name }}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                                        No roles found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </section>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h3 class="text-2xl font-bold mb-4">Confirm Deletion</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to delete this role?</p>
            <div class="flex justify-end space-x-4">
                <button id="cancel-delete" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Cancel</button>
                <button id="confirm-delete" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div id="edit-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-screen overflow-y-auto">
            <h3 class="text-2xl font-bold mb-4">Edit Role</h3>
            <form id="edit-role-form" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-role-name">Role Name</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="edit-role-name" 
                           name="name" 
                           type="text" 
                           placeholder="Role Name" 
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-role-description">Description</label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                              id="edit-role-description" 
                              name="description" 
                              placeholder="Role Description" 
                              rows="3"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Permissions</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @php
                            $permissionGroups = [
                                'Manage Elections' => ['manage-elections-view', 'manage-elections-add', 'manage-elections-edit', 'manage-elections-delete'],
                                'Manage Candidates' => ['manage-candidates-view', 'manage-candidates-add', 'manage-candidates-edit', 'manage-candidates-delete'],
                                'Manage Positions' => ['manage-positions-view', 'manage-positions-add', 'manage-positions-edit', 'manage-positions-delete'],
                                'Manage Voters' => ['manage-voters-view', 'manage-voters-add', 'manage-voters-edit', 'manage-voters-delete'],
                                'Manage Articles' => ['manage-articles-view', 'manage-articles-add', 'manage-articles-edit', 'manage-articles-delete'],
                                'View Results' => ['view-results'],
                                'Reports' => ['reports'],
                                'Admin Roles' => ['admin-roles-view', 'admin-roles-add', 'admin-roles-edit', 'admin-roles-delete'],
                                'Audit Logs' => ['audit-logs-view'],
                                'Verify Voters' => ['verify-voters-view', 'verify-voters-add', 'verify-voters-edit', 'verify-voters-delete'],
                                'Set Academic Year' => ['set-academic-year-view', 'set-academic-year-add', 'set-academic-year-edit', 'set-academic-year-delete']
                            ];
                        @endphp

                        @foreach($permissionGroups as $groupName => $permissions)
                            <div>
                                <h4 class="font-bold">{{ $groupName }}</h4>
                                @foreach($permissions as $permission)
                                    <label class="block">
                                        <input type="checkbox" 
                                               class="mr-2 edit-permission-checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission }}">
                                        {{ ucfirst(str_replace('-', ' ', explode('-', $permission)[count(explode('-', $permission))-1])) }}
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancel-edit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- New Role Modal -->
    <div id="new-role-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-screen overflow-y-auto">
            <h3 class="text-2xl font-bold mb-4">New Role</h3>
            <form id="new-role-form" method="POST" action="{{ route('admin.admin-roles.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="new-role-name">Role Name</label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="new-role-name" 
                           name="name" 
                           type="text" 
                           placeholder="Role Name" 
                           required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="new-role-description">Description</label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                              id="new-role-description" 
                              name="description" 
                              placeholder="Role Description" 
                              rows="3"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Permissions</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($permissionGroups as $groupName => $permissions)
                            <div>
                                <h4 class="font-bold">{{ $groupName }}</h4>
                                @foreach($permissions as $permission)
                                    <label class="block">
                                        <input type="checkbox" 
                                               class="mr-2" 
                                               name="permissions[]" 
                                               value="{{ $permission }}">
                                        {{ ucfirst(str_replace('-', ' ', explode('-', $permission)[count(explode('-', $permission))-1])) }}
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancel-new-role" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Create</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            let roleToDelete = null;

            // Auto-hide alerts functionality
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

            // Modal functions
            function openModal(modalId) {
                document.getElementById(modalId).classList.remove('hidden');
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
            }

            // Select all functionality
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.role-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // New role button
            document.getElementById('new-role-button').addEventListener('click', function() {
                openModal('new-role-modal');
            });

            // Cancel new role
            document.getElementById('cancel-new-role').addEventListener('click', function() {
                closeModal('new-role-modal');
            });

            // Edit button functionality
            document.querySelectorAll('.edit-button').forEach(button => {
                button.addEventListener('click', function() {
                    const roleId = this.dataset.roleId;
                    const roleName = this.dataset.roleName;
                    const roleDescription = this.dataset.roleDescription;
                    const rolePermissions = JSON.parse(this.dataset.rolePermissions);

                    document.getElementById('edit-role-name').value = roleName;
                    document.getElementById('edit-role-description').value = roleDescription;
                    document.getElementById('edit-role-form').action = `/admin/roles/${roleId}`;

                    // Clear all checkboxes first
                    document.querySelectorAll('.edit-permission-checkbox').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Check the appropriate permissions
                    rolePermissions.forEach(permission => {
                        const checkbox = document.querySelector(`.edit-permission-checkbox[value="${permission}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });

                    openModal('edit-modal');
                });
            });

            // Cancel edit
            document.getElementById('cancel-edit').addEventListener('click', function() {
                closeModal('edit-modal');
            });

            // Delete button functionality
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    roleToDelete = {
                        id: this.dataset.roleId,
                        name: this.dataset.roleName
                    };
                    openModal('delete-modal');
                });
            });

            // Cancel delete
            document.getElementById('cancel-delete').addEventListener('click', function() {
                closeModal('delete-modal');
                roleToDelete = null;
            });

            // Confirm delete
            document.getElementById('confirm-delete').addEventListener('click', function() {
                if (roleToDelete) {
                    // Create a form to submit the delete request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/roles/${roleToDelete.id}`;
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    form.appendChild(csrfToken);
                    form.appendChild(methodField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });

            // Close modals when clicking outside
            document.addEventListener('click', function(event) {
                const modals = ['new-role-modal', 'edit-modal', 'delete-modal'];
                modals.forEach(modalId => {
                    const modal = document.getElementById(modalId);
                    if (event.target === modal) {
                        closeModal(modalId);
                    }
                });
            });

            // Initialize when the document is ready
            document.addEventListener('DOMContentLoaded', () => {
                setupAlerts();
            });
        </script>
    @endpush
@endsection
@extends('layouts.admin')

@section('title', 'Manage Election Party')
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
.modal {
transition: opacity 0.3s ease;
}
</style>

<!-- Success/Error Messages -->
<div class="mt-6">
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
    <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <span class="close-btn float-right cursor-pointer text-red-700 hover:text-red-900">&times;</span>
        <div class="font-semibold mb-1">Please fix the following:</div>
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</div>

<section class="text-center mt-12 mb-8">
    <h2 class="text-4xl font-bold text-gray-800 mb-4">Manage Election Party</h2>
    <p class="text-gray-600 text-lg">Here you can manage all the election parties.</p>
</section>

<section class="my-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-3xl font-bold text-gray-800">Election Party List</h3>
        <div class="flex items-center gap-2">
            <a
                href="{{ ($isArchived ?? false) ? route('admin.election-parties.index') : route('admin.election-parties.index', ['archived' => 1]) }}"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                {{ ($isArchived ?? false) ? 'Back to List' : 'Archive' }}
            </a>

            @if (!($isArchived ?? false))
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 hidden md:block" id="new-party-button">New Party</button>
            @endif
        </div>
    </div>
    @if (!($isArchived ?? false))
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mb-4 md:hidden" id="new-party-button-mobile">New Party</button>
    @endif
    
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="overflow-x-auto">
            <form id="bulk-action-form" method="POST" action="{{ route('admin.election-parties.bulk-delete') }}">
                @csrf
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Logo</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Party Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Description</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($electionParties as $party)
                            <tr data-party-id="{{ $party->id }}">
                                <td class="py-2 px-4 border-b border-gray-300">
                                    @php
                                        $partyLogoUrl = null;
                                        if (!empty($party->logo)) {
                                            $partyLogoFile = basename($party->logo);
                                            $partyLogoUrl = route('storage.image', ['folder' => 'election-parties', 'filename' => $partyLogoFile]) . '?v=' . urlencode($party->logo);
                                        }
                                    @endphp
                                    @if($partyLogoUrl)
                                        <img src="{{ $partyLogoUrl }}" alt="{{ $party->name }}" class="w-10 h-10 object-cover rounded" onerror="this.onerror=null;this.style.display='none';" />
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded"></div>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300">{{ $party->name }}</td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    {{ $party->description ?? 'No description available' }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    <div class="flex flex-col md:flex-row md:space-x-2">
                                        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 view-button mb-2 md:mb-0" 
                                                data-party-id="{{ $party->id }}"
                                                data-party-name="{{ $party->name }}"
                                                data-party-description="{{ $party->description }}"
                                                data-party-logo-url="{{ $partyLogoUrl }}">
                                            View
                                        </button>
                                        @if (!($isArchived ?? false))
                                            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 edit-button mb-2 md:mb-0" 
                                                    data-party-id="{{ $party->id }}"
                                                    data-party-name="{{ $party->name }}"
                                                    data-party-description="{{ $party->description }}"
                                                    data-party-status="{{ $party->status }}">
                                                Edit
                                            </button>
                                            <button type="button" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 delete-button" 
                                                    data-party-id="{{ $party->id }}"
                                                    data-party-name="{{ $party->name }}"
                                                    data-delete-url="{{ route('admin.election-parties.destroy', $party->id) }}">
                                                Delete
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('admin.election-parties.restore', $party->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 mb-2 md:mb-0">Restore</button>
                                            </form>
                                            <button type="button" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800 delete-button" 
                                                    data-party-id="{{ $party->id }}"
                                                    data-party-name="{{ $party->name }}"
                                                    data-delete-url="{{ route('admin.election-parties.forceDestroy', $party->id) }}">
                                                Permanent Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                                    No election parties found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</section>

<!-- View Modal -->
<div class="fixed z-10 inset-0 overflow-y-auto hidden" id="party-modal">
    <div class="flex items-center justify-center min-h-screen">
        <div aria-hidden="true" class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-white p-6">
                <div class="flex items-center mb-4">
                    <h3 class="text-2xl font-bold text-gray-800" id="view-party-name">Party Name</h3>
                </div>
                <div class="mb-4">
                    <img id="view-party-logo" class="w-24 h-24 object-cover rounded hidden" alt="" />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                    <p id="view-party-description" class="text-gray-700">Description</p>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" id="close-modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="fixed z-10 inset-0 overflow-y-auto hidden" id="delete-modal">
    <div class="flex items-center justify-center min-h-screen">
        <div aria-hidden="true" class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-white p-6">
                <h2 class="text-2xl font-bold mb-4">Confirm Deletion</h2>
                <p>Are you sure you want to delete the party "<span id="delete-party-name"></span>"?</p>
                <div class="flex justify-end mt-4">
                    <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 mr-2" id="confirm-delete">Delete</button>
                    <button class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700" id="cancel-delete">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Party Modal -->
<div class="fixed z-10 inset-0 overflow-y-auto hidden" id="new-party-modal">
    <div class="flex items-center justify-center min-h-screen">
        <div aria-hidden="true" class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-white p-6">
                <h2 class="text-2xl font-bold mb-4">New Party</h2>
                <form id="new-party-form" method="POST" action="{{ route('admin.election-parties.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="new-party-name">Party Name</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="new-party-name" 
                               name="name" 
                               type="text" 
                               placeholder="Enter party name" 
                               required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="new-party-description">Description</label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="new-party-description" 
                                name="description" 
                                data-quill="1"
                                rows="3"
                                placeholder="Enter party description" 
                                required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="new-party-logo">Logo / Banner</label>
                        <input class="block w-full text-sm text-gray-700" id="new-party-logo" name="logo" type="file" accept="image/*" />
                    </div>
                    <div class="flex justify-end">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mr-2" type="submit">Save</button>
                        <button class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700" type="button" id="cancel-new-party">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Party Modal -->
<div class="fixed z-10 inset-0 overflow-y-auto hidden" id="edit-party-modal">
    <div class="flex items-center justify-center min-h-screen">
        <div aria-hidden="true" class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-white p-6">
                <h2 class="text-2xl font-bold mb-4">Edit Party</h2>
                <form id="edit-party-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-party-status" name="status" value="">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-party-name">Party Name</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="edit-party-name" 
                               name="name" 
                               type="text" 
                               placeholder="Enter party name" 
                               required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-party-description">Description</label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="edit-party-description" 
                                name="description" 
                                data-quill="1"
                                rows="3"
                                placeholder="Enter party description" 
                                required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-party-logo">Logo / Banner</label>
                        <input class="block w-full text-sm text-gray-700" id="edit-party-logo" name="logo" type="file" accept="image/*" />
                    </div>
                    <div class="flex justify-end">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mr-2" type="submit">Save</button>
                        <button class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700" type="button" id="cancel-edit-party">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
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

        // View Modal functionality
        const viewButtons = document.querySelectorAll('.view-button');
        const viewModal = document.getElementById('party-modal');
        const closeModal = document.getElementById('close-modal');

        viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                const partyName = button.getAttribute('data-party-name');
                const partyDescription = button.getAttribute('data-party-description');
                const partyLogoUrl = button.getAttribute('data-party-logo-url');
                
                document.getElementById('view-party-name').textContent = partyName;
                document.getElementById('view-party-description').textContent = partyDescription || 'No description available';

                const logoEl = document.getElementById('view-party-logo');
                if (logoEl) {
                    if (partyLogoUrl) {
                        logoEl.src = partyLogoUrl;
                        logoEl.classList.remove('hidden');
                    } else {
                        logoEl.removeAttribute('src');
                        logoEl.classList.add('hidden');
                    }
                }
                
                viewModal.classList.remove('hidden');
            });
        });

        closeModal.addEventListener('click', () => {
            viewModal.classList.add('hidden');
        });

        // Delete Confirmation Modal functionality
        const deleteButtons = document.querySelectorAll('.delete-button');
        const deleteModal = document.getElementById('delete-modal');
        const confirmDelete = document.getElementById('confirm-delete');
        const cancelDelete = document.getElementById('cancel-delete');
        let deletePartyId = null;

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                deletePartyId = this.getAttribute('data-party-id');
                const partyName = this.getAttribute('data-party-name');
                document.getElementById('delete-party-name').textContent = partyName;
                deleteModal.classList.remove('hidden');
            });
        });

        cancelDelete.addEventListener('click', function() {
            deleteModal.classList.add('hidden');
            deletePartyId = null;
        });

        confirmDelete.addEventListener('click', function() {
            if (deletePartyId) {
                // Create a form and submit it for deletion
                const form = document.createElement('form');
                form.method = 'POST';
                
                const row = document.querySelector(`tr[data-party-id="${deletePartyId}"]`);
                const url = row ? row.querySelector('.delete-button')?.getAttribute('data-delete-url') : null;
                form.action = url || `{{ route('admin.election-parties.index') }}/${deletePartyId}`;
                
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

        // New Party Modal functionality
        const newPartyButton = document.getElementById('new-party-button');
        const newPartyButtonMobile = document.getElementById('new-party-button-mobile');
        const newPartyModal = document.getElementById('new-party-modal');
        const cancelNewParty = document.getElementById('cancel-new-party');

        if (newPartyButton) {
            newPartyButton.addEventListener('click', () => {
                newPartyModal.classList.remove('hidden');
            });
        }

        if (newPartyButtonMobile) {
            newPartyButtonMobile.addEventListener('click', () => {
                newPartyModal.classList.remove('hidden');
            });
        }

        if (cancelNewParty) {
            cancelNewParty.addEventListener('click', () => {
                newPartyModal.classList.add('hidden');
            });
        }

        // Edit Party Modal functionality
        const editButtons = document.querySelectorAll('.edit-button');
        const editPartyModal = document.getElementById('edit-party-modal');
        const editPartyForm = document.getElementById('edit-party-form');
        const editPartyName = document.getElementById('edit-party-name');
        const editPartyDescription = document.getElementById('edit-party-description');
        const editPartyStatus = document.getElementById('edit-party-status');

        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                const partyId = button.getAttribute('data-party-id');
                const partyName = button.getAttribute('data-party-name');
                const partyDescription = button.getAttribute('data-party-description');
                const partyStatus = button.getAttribute('data-party-status');

                editPartyName.value = partyName;
                editPartyDescription.value = partyDescription || '';
                if (editPartyStatus) {
                    editPartyStatus.value = partyStatus || '';
                }
                editPartyForm.action = `{{ route('admin.election-parties.index') }}/${partyId}`;

                editPartyModal.classList.remove('hidden');
            });
        });

        const cancelEditParty = document.getElementById('cancel-edit-party');
        if (cancelEditParty) {
            cancelEditParty.addEventListener('click', () => {
                editPartyModal.classList.add('hidden');
            });
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === deleteModal) {
                deleteModal.classList.add('hidden');
            }
            if (event.target === newPartyModal) {
                newPartyModal.classList.add('hidden');
            }
            if (event.target === editPartyModal) {
                editPartyModal.classList.add('hidden');
            }
            if (event.target === viewModal) {
                viewModal.classList.add('hidden');
            }
        });

        // Initialize when the document is ready
        document.addEventListener('DOMContentLoaded', () => {
            setupAlerts();
        });
    </script>
@endpush
@endsection
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
    <h2 class="text-4xl font-bold text-gray-800 mb-4">Manage Election Party</h2>
    <p class="text-gray-600 text-lg">Here you can manage all the election parties.</p>
</section>

<section class="my-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-3xl font-bold text-gray-800">Election Party List</h3>
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 hidden md:block" id="new-party-button">New Party</button>
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mb-4 md:hidden" id="new-party-button-mobile">New Party</button>
    
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="overflow-x-auto">
            <form id="bulk-action-form" method="POST">
                @csrf
                @method('DELETE')
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Party Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Description</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($electionParties as $party)
                            <tr data-party-id="{{ $party->id }}">
                                <td class="py-2 px-4 border-b border-gray-300">{{ $party->name }}</td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    {{ $party->description ?? 'No description available' }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    <div class="flex flex-col md:flex-row md:space-x-2">
                                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 view-button mb-2 md:mb-0" 
                                                data-party-id="{{ $party->id }}"
                                                data-party-name="{{ $party->name }}"
                                                data-party-description="{{ $party->description }}">
                                            View
                                        </button>
                                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 edit-button mb-2 md:mb-0" 
                                                data-party-id="{{ $party->id }}"
                                                data-party-name="{{ $party->name }}"
                                                data-party-description="{{ $party->description }}">
                                            Edit
                                        </button>
                                        <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 delete-button" 
                                                data-party-id="{{ $party->id }}"
                                                data-party-name="{{ $party->name }}">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 px-4 text-center text-gray-500">
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
                <form id="new-party-form" method="POST" action="{{ route('admin.election-parties.store') }}">
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
                                rows="3"
                                placeholder="Enter party description" 
                                required></textarea>
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
                <form id="edit-party-form" method="POST">
                    @csrf
                    @method('PUT')
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
                                rows="3"
                                placeholder="Enter party description" 
                                required></textarea>
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
                
                document.getElementById('view-party-name').textContent = partyName;
                document.getElementById('view-party-description').textContent = partyDescription || 'No description available';
                
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
                form.action = `{{ route('admin.election-parties.index') }}/${deletePartyId}`;
                
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

        newPartyButton.addEventListener('click', () => {
            newPartyModal.classList.remove('hidden');
        });

        newPartyButtonMobile.addEventListener('click', () => {
            newPartyModal.classList.remove('hidden');
        });

        cancelNewParty.addEventListener('click', () => {
            newPartyModal.classList.add('hidden');
        });

        // Edit Party Modal functionality
        const editButtons = document.querySelectorAll('.edit-button');
        const editPartyModal = document.getElementById('edit-party-modal');
        const editPartyForm = document.getElementById('edit-party-form');
        const editPartyName = document.getElementById('edit-party-name');
        const editPartyDescription = document.getElementById('edit-party-description');

        editButtons.forEach(button => {
            button.addEventListener('click', () => {
                const partyId = button.getAttribute('data-party-id');
                const partyName = button.getAttribute('data-party-name');
                const partyDescription = button.getAttribute('data-party-description');
                
                editPartyName.value = partyName;
                editPartyDescription.value = partyDescription || '';
                editPartyForm.action = `{{ route('admin.election-parties.index') }}/${partyId}`;
                
                editPartyModal.classList.remove('hidden');
            });
        });

        document.getElementById('cancel-edit-party').addEventListener('click', () => {
            editPartyModal.classList.add('hidden');
        });

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
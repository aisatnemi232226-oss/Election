@extends('layouts.admin')

@section('title', 'Position')
@section('dashboard-title', 'Positions')

@section('content')
    @php
        function getUpdateUrl($id) {
            return route('admin.position.update', $id);
        }

        function getDeleteUrl($id) {
            return route('admin.position.destroy', $id);
        }
    @endphp
    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Manage Positions</h2>
        <p class="text-gray-600 text-lg">Here you can manage all the positions for the elections.</p>
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
            <h1 class="text-2xl font-bold text-gray-800">Positions List</h1>
            
            <div class="flex items-center space-x-4">
                <!-- Search box -->
                <input type="text" 
                    placeholder="Search positions..." 
                    class="px-4 py-2 border rounded-lg w-64" 
                    id="search-box">
                            
                <button 
                    class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                    id="new-position-btn">
                    New Position
                </button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Position Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold hidden md:table-cell">Description</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold hidden md:table-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="positions-table-body">
                        @foreach($positions as $position)
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    <div class="md:flex md:items-center">
                                        <div class="md:flex-grow">{{ $position->name }}</div>
                                        <div class="mt-2 md:mt-0 md:ml-4 md:hidden">
                                            <div class="text-gray-600">{{ $position->description }}</div>
                                            <div class="mt-2 md:mt-0 md:ml-4">
                                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 edit-btn"
                                                    data-id="{{ $position->id }}"
                                                    data-position-name="{{ $position->name }}"
                                                    data-description="{{ $position->description }}"
                                                    data-update-url="{{ getUpdateUrl($position->id) }}">Edit</button>
                                                <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 delete-btn"
                                                    data-id="{{ $position->id }}"
                                                    data-delete-url="{{ getDeleteUrl($position->id) }}">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300 hidden md:table-cell">{{ $position->description }}</td>
                                <td class="py-2 px-4 border-b border-gray-300 hidden md:table-cell">
                                    <div class="flex items-center">
                                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 edit-btn"
                                            data-id="{{ $position->id }}"
                                            data-position-name="{{ $position->name }}"
                                            data-description="{{ $position->description }}"
                                            data-update-url="{{ getUpdateUrl($position->id) }}">Edit</button>
                                        <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 delete-btn ml-2"
                                            data-id="{{ $position->id }}"
                                            data-delete-url="{{ getDeleteUrl($position->id) }}">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $positions->links('components.pagination') }}
            </div>
        </div>
    </section>

    <!-- New Position Modal - FIXED: Removed 'flex' class -->
    <div id="new-position-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
            <h3 class="text-2xl font-bold mb-4">Create New Position</h3>
            <form method="POST" action="{{ route('admin.position.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700">Position Name</label>
                    <input type="text" name="name" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter position name" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Description</label>
                    <textarea name="description" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter description" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-600 text-white px-4 py-2 rounded-lg mr-2" id="close-new-position-modal">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Create</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Position Modal - FIXED: Removed 'flex' class -->
    <div id="edit-position-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/2">
            <h3 class="text-2xl font-bold mb-4">Edit Position</h3>
            <form method="POST" id="edit-position-form" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit-position-id">
                <div class="mb-4">
                    <label class="block text-gray-700">Position Name</label>
                    <input type="text" name="name" id="edit-position-name" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter position name" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Description</label>
                    <textarea name="description" id="edit-description" class="w-full px-4 py-2 border rounded-lg" placeholder="Enter description" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-600 text-white px-4 py-2 rounded-lg mr-2" id="close-edit-position-modal">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal - FIXED: Removed 'flex' class -->
    <div id="delete-confirmation-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h3 class="text-2xl font-bold mb-4">Confirm Deletion</h3>
            <p class="mb-4">Are you sure you want to delete this position?</p>
            <form method="POST" id="delete-position-form" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-600 text-white px-4 py-2 rounded-lg mr-2" id="close-delete-confirmation-modal">Cancel</button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg">Delete</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Declare shared variables at the top
            const searchBox = document.getElementById('search-box');
            const positionsTableBody = document.getElementById('positions-table-body');
            const paginationContainer = document.querySelector('.mt-4');
            
            // New Position Modal
            const newPositionBtn = document.getElementById('new-position-btn');
            const newPositionModal = document.getElementById('new-position-modal');
            const closeNewPositionModal = document.getElementById('close-new-position-modal');

            // UPDATED: Add/remove flex class when showing/hiding modal
            newPositionBtn.addEventListener('click', () => {
                newPositionModal.classList.remove('hidden');
                newPositionModal.classList.add('flex');
            });

            closeNewPositionModal.addEventListener('click', () => {
                newPositionModal.classList.add('hidden');
                newPositionModal.classList.remove('flex');
            });

            // Edit Position Modal
            const editBtns = document.querySelectorAll('.edit-btn');
            const editPositionModal = document.getElementById('edit-position-modal');
            const closeEditPositionModal = document.getElementById('close-edit-position-modal');
            const editPositionForm = document.getElementById('edit-position-form');
            const editPositionId = document.getElementById('edit-position-id');
            const editPositionName = document.getElementById('edit-position-name');
            const editDescription = document.getElementById('edit-description');

            // UPDATED: Add/remove flex class when showing/hiding modal
            editBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const positionId = btn.getAttribute('data-id');
                    const positionName = btn.getAttribute('data-position-name');
                    const description = btn.getAttribute('data-description');
                    const updateUrl = btn.getAttribute('data-update-url');
                    
                    editPositionId.value = positionId;
                    editPositionName.value = positionName;
                    editDescription.value = description;
                    editPositionForm.action = updateUrl || `/admin/position/${positionId}`;
                    
                    editPositionModal.classList.remove('hidden');
                    editPositionModal.classList.add('flex');
                });
            });

            closeEditPositionModal.addEventListener('click', () => {
                editPositionModal.classList.add('hidden');
                editPositionModal.classList.remove('flex');
            });

            // Delete Position Modal
            const deleteBtns = document.querySelectorAll('.delete-btn');
            const deleteConfirmationModal = document.getElementById('delete-confirmation-modal');
            const closeDeleteConfirmationModal = document.getElementById('close-delete-confirmation-modal');
            const deletePositionForm = document.getElementById('delete-position-form');

            // UPDATED: Add/remove flex class when showing/hiding modal
            deleteBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const deleteUrl = btn.getAttribute('data-delete-url');
                    deletePositionForm.action = deleteUrl;
                    
                    deleteConfirmationModal.classList.remove('hidden');
                    deleteConfirmationModal.classList.add('flex');
                });
            });

            closeDeleteConfirmationModal.addEventListener('click', () => {
                deleteConfirmationModal.classList.add('hidden');
                deleteConfirmationModal.classList.remove('flex');
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
                const rows = positionsTableBody.querySelectorAll('tr');
                let visibleCount = 0;
                
                rows.forEach(row => {
                    const positionName = row.querySelector('td').textContent.toLowerCase();
                    const description = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                    
                    if (positionName.includes(searchTerm) || description.includes(searchTerm)) {
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                // Update pagination info
                const showingStart = document.getElementById('pagination-showing-start');
                const showingEnd = document.getElementById('pagination-showing-end');
                const total = document.getElementById('pagination-total');
                
                if (showingStart && showingEnd && total) {
                    if (searchTerm) {
                        showingStart.textContent = visibleCount > 0 ? '1' : '0';
                        showingEnd.textContent = visibleCount;
                        total.textContent = visibleCount;
                    }
                }

                // Hide pagination if searching
                if (paginationContainer) {
                    paginationContainer.style.display = searchTerm ? 'none' : '';
                }
            }

            // Attach search event listener
            searchBox.addEventListener('input', filterTable);

            // Pagination functionality
            document.addEventListener('click', (e) => {
                const link = e.target.closest('.pagination a');
                if (link) {
                    e.preventDefault();
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page');
                    
                    // Clear search when changing pages
                    searchBox.value = '';
                    filterTable();
                    
                    // Fetch new page
                    fetch(link.href, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        const temp = document.createElement('div');
                        temp.innerHTML = html;
                        
                        const newTableBody = temp.querySelector('#positions-table-body');
                        if (newTableBody) {
                            positionsTableBody.innerHTML = newTableBody.innerHTML;
                        }
                        
                        const newPagination = temp.querySelector('.mt-4');
                        if (newPagination && paginationContainer) {
                            paginationContainer.innerHTML = newPagination.innerHTML;
                        }
                        
                        window.history.pushState({}, '', link.href);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                }
            });

            // Initialize everything when the document is ready
            document.addEventListener('DOMContentLoaded', () => {
                setupAlerts();
                filterTable();
            });
        </script>
    @endpush
@endsection
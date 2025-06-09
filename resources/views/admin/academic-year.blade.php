@extends('layouts.admin')

@section('title', 'Set Current Academic Year')
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
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Set Current Academic Year</h2>
        <p class="text-gray-600 text-lg">Set the current academic year for the university.</p>
    </section>

    <section class="my-8">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl mx-auto">
            <div class="mb-4 flex flex-col md:flex-row justify-between items-center">
                <div class="w-full md:w-1/2 mb-4 md:mb-0">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="filter">Filter Academic Years</label>
                    <form method="GET" action="{{ route('admin.academic-year.index') }}" id="filter-form">
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="filter" 
                               name="filter" 
                               type="text" 
                               placeholder="Search by academic year"
                               value="{{ request('filter') }}">
                    </form>
                </div>
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 w-full md:w-auto">
                    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 w-full md:w-auto md:flex-1" 
                            id="new-button"
                            onclick="openModal('newAcademicYearModal')">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 w-full md:w-auto md:flex-1" 
                            id="remove-button">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 w-full md:w-auto md:flex-1" 
                            id="edit-button">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 w-full md:w-auto md:flex-1" 
                            id="toggle-status-button">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <form id="bulk-action-form" method="POST">
                    @csrf
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" id="select-all">
                                </th>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Academic Year</th>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody id="academic-year-table">
                            @forelse($combinedData as $item)
                                <tr data-academic-year-id="{{ $item->academic_year_id }}">
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <input type="checkbox" 
                                               class="form-checkbox h-5 w-5 text-blue-600 academic-year-checkbox" 
                                               name="academic_year_ids[]" 
                                               value="{{ $item->academic_year_id }}">
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <span class="academic-year-name" data-name="{{ $item->formatted_name }}">
                                            {{ $item->formatted_name }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        @if($item->is_active)
                                            <i class="fas fa-check text-green-600"></i>
                                        @else
                                            <i class="fas fa-times text-red-600"></i>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 px-4 text-center text-gray-500">
                                        No academic years found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </section>

    <!-- New Academic Year Modal -->
    <div id="newAcademicYearModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Create New Academic Year</h3>
                <form method="POST" action="{{ route('admin.academic-year.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="academic-year-name">
                            Academic Year
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="academic-year-name" 
                               name="name" 
                               type="text" 
                               placeholder="e.g., 2024-2025" 
                               required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="academic-year-status">
                            Status
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="academic-year-status" 
                                name="status" 
                                required>
                            <option value="inactive">Inactive</option>
                            <option value="active">Active</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" 
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600" 
                                onclick="closeModal('newAcademicYearModal')">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Create Academic Year
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Academic Year Modal -->
    <div id="editAcademicYearModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Edit Academic Year</h3>
                <form method="POST" id="edit-academic-year-form">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-academic-year-name">
                            Academic Year
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="edit-academic-year-name" 
                               name="name" 
                               type="text" 
                               placeholder="e.g., 2024-2025" 
                               required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-academic-year-status">
                            Status
                        </label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="edit-academic-year-status" 
                                name="status" 
                                required>
                            <option value="inactive">Inactive</option>
                            <option value="active">Active</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" 
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600" 
                                onclick="closeModal('editAcademicYearModal')">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Update Academic Year
                        </button>
                    </div>
                </form>
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

            // Modal functions
            function openModal(modalId) {
                document.getElementById(modalId).classList.remove('hidden');
            }

            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
            }

            // Filter functionality with debounce
            document.getElementById('filter').addEventListener('input', function() {
                const form = document.getElementById('filter-form');
                clearTimeout(this.filterTimeout);
                this.filterTimeout = setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // Select all functionality
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.academic-year-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Delete functionality
            document.getElementById('remove-button').addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.academic-year-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one academic year to delete.');
                    return;
                }

                if (confirm('Are you sure you want to delete the selected academic years?')) {
                    const form = document.getElementById('bulk-action-form');
                    form.action = '{{ route("admin.academic-year.bulk-delete") }}';
                    form.submit();
                }
            });

            // Edit functionality
            document.getElementById('edit-button').addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.academic-year-checkbox:checked');
                if (selectedCheckboxes.length !== 1) {
                    alert('Please select exactly one academic year to edit.');
                    return;
                }

                const academicYearId = selectedCheckboxes[0].value;
                const academicYearRow = selectedCheckboxes[0].closest('tr');
                const academicYearName = academicYearRow.querySelector('.academic-year-name').dataset.name;

                // Extract just the year part (remove semester info for editing)
                const yearPart = academicYearName.split(' ').slice(-1)[0]; // Gets "2024-2025" from "1st Semester 2024-2025"
                
                document.getElementById('edit-academic-year-name').value = yearPart;
                document.getElementById('edit-academic-year-form').action = `/admin/academic-year/${academicYearId}`;
                
                openModal('editAcademicYearModal');
            });

            // Toggle status functionality
            document.getElementById('toggle-status-button').addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.academic-year-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one academic year to toggle status.');
                    return;
                }

                if (confirm('Are you sure you want to toggle the status of selected academic years?')) {
                    const form = document.getElementById('bulk-action-form');
                    form.action = '{{ route("admin.academic-year.toggle-status") }}';
                    form.submit();
                }
            });

            // Close modals when clicking outside
            document.addEventListener('click', function(event) {
                const modals = ['newAcademicYearModal', 'editAcademicYearModal'];
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
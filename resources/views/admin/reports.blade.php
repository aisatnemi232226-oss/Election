@extends('layouts.admin')

@section('title', 'Reports')
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
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Reports</h2>
        <p class="text-gray-600 text-lg">View and generate reports for the election system.</p>
    </section>

    <section class="my-8">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl mx-auto">
            <div class="mb-4 flex flex-col md:flex-row justify-between items-center">
                <div class="w-full md:w-1/2 mb-4 md:mb-0">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="filter">Filter Reports</label>
                    <form method="GET" action="{{ route('admin.reports.index') }}" id="filter-form">
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="filter" 
                               name="filter" 
                               type="text" 
                               placeholder="Search by report name or date" 
                               value="{{ request('filter') }}">
                    </form>
                </div>
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 w-full md:w-auto">
                    <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 w-full md:w-auto md:flex-1" 
                            id="new-report-button" 
                            data-toggle="modal" 
                            data-target="#newReportModal">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 w-full md:w-auto md:flex-1" 
                            id="delete-report-button">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 w-full md:w-auto md:flex-1" 
                            id="edit-report-button">
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="{{ route('admin.reports.export') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 w-full md:w-auto md:flex-1 text-center inline-block">
                        <i class="fas fa-file-export"></i>
                    </a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <form id="bulk-action-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">
                                    <input type="checkbox" class="form-checkbox h-5 w-5 text-blue-600" id="select-all">
                                </th>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Report Name</th>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Date</th>
                                <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody id="reports-table">
                            @forelse($reports as $report)
                                <tr data-report-id="{{ $report->id }}">
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <input type="checkbox" 
                                               class="form-checkbox h-5 w-5 text-blue-600 report-checkbox" 
                                               name="report_ids[]" 
                                               value="{{ $report->id }}">
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        <a href="#" class="text-blue-600 hover:underline report-name" data-name="{{ $report->name }}">
                                            {{ $report->name }}
                                        </a>
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        {{ $report->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="py-2 px-4 border-b border-gray-300">
                                        @if($report->status === 'completed')
                                            <i class="fas fa-check text-green-600"></i>
                                        @else
                                            <i class="fas fa-times text-red-600"></i>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                                        No reports found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </section>

    <!-- New Report Modal -->
    <div id="newReportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Create New Report</h3>
                <form method="POST" action="{{ route('admin.reports.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="report-name">
                            Report Name
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="report-name" 
                               name="name" 
                               type="text" 
                               placeholder="Enter report name" 
                               required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="report-description">
                            Description
                        </label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                  id="report-description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Enter report description"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" 
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600" 
                                onclick="closeModal('newReportModal')">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Create Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Report Modal -->
    <div id="editReportModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Edit Report</h3>
                <form method="POST" id="edit-report-form">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-report-name">
                            Report Name
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="edit-report-name" 
                               name="name" 
                               type="text" 
                               placeholder="Enter report name" 
                               required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="edit-report-description">
                            Description
                        </label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                  id="edit-report-description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Enter report description"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" 
                                class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600" 
                                onclick="closeModal('editReportModal')">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Update Report
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

            // Filter functionality
            document.getElementById('filter').addEventListener('input', function() {
                const form = document.getElementById('filter-form');
                clearTimeout(this.filterTimeout);
                this.filterTimeout = setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // Select all functionality
            document.getElementById('select-all').addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.report-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Delete functionality
            document.getElementById('delete-report-button').addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.report-checkbox:checked');
                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one report to delete.');
                    return;
                }

                if (confirm('Are you sure you want to delete the selected reports?')) {
                    const form = document.getElementById('bulk-action-form');
                    form.action = '{{ route("admin.reports.destroy") }}';
                    form.submit();
                }
            });

            // Edit functionality
            document.getElementById('edit-report-button').addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.report-checkbox:checked');
                if (selectedCheckboxes.length !== 1) {
                    alert('Please select exactly one report to edit.');
                    return;
                }

                const reportId = selectedCheckboxes[0].value;
                const reportRow = selectedCheckboxes[0].closest('tr');
                const reportName = reportRow.querySelector('.report-name').dataset.name;

                document.getElementById('edit-report-name').value = reportName;
                document.getElementById('edit-report-form').action = `/admin/reports/${reportId}`;
                
                openModal('editReportModal');
            });

            // New report functionality
            document.getElementById('new-report-button').addEventListener('click', function() {
                openModal('newReportModal');
            });

            // Close modals when clicking outside
            document.addEventListener('click', function(event) {
                const modals = ['newReportModal', 'editReportModal'];
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
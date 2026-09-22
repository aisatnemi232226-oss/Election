@extends('layouts.admin')

@section('title', 'Audit Logs')
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
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Audit Logs</h2>
        <p class="text-gray-600 text-lg">View and manage the audit logs of the system to track user activities.</p>
    </section>

    <section class="my-8">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="mb-4 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="w-full md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="filter">Filter Logs</label>
                    <form method="GET" action="{{ route('admin.audit-logs.index') }}" id="filter-form">
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="filter" 
                               name="filter" 
                               type="text" 
                               placeholder="Search by user, action, or details"
                               value="{{ request('filter') }}">
                        <input type="hidden" name="log_type" value="{{ request('log_type') }}">
                        <input type="hidden" name="operation" value="{{ request('operation') }}">
                    </form>
                </div>
                <div class="w-full md:w-1/4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="log-type">Log Type</label>
                    <form method="GET" action="{{ route('admin.audit-logs.index') }}" id="log-type-form">
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                        <input type="hidden" name="operation" value="{{ request('operation') }}">
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="log-type" 
                                name="log_type" 
                                onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach(($logTypes ?? ['AUDIT','SECURITY','USER_ACTIVITY','TRANSACTION']) as $t)
                                <option value="{{ $t }}" {{ request('log_type') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="w-full md:w-1/4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="operation">Operation</label>
                    <form method="GET" action="{{ route('admin.audit-logs.index') }}" id="operation-form">
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                        <input type="hidden" name="log_type" value="{{ request('log_type') }}">
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                                id="operation" 
                                name="operation" 
                                onchange="this.form.submit()">
                            <option value="">All</option>
                            @foreach(($operations ?? ['INSERT','UPDATE','DELETE','READ','LOGIN','LOGOUT']) as $op)
                                <option value="{{ $op }}" {{ request('operation') == $op ? 'selected' : '' }}>{{ $op }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <a href="{{ route('admin.audit-logs.export') }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 w-full md:w-auto">
                    Export Logs
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Timestamp</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">User</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Log Type</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Operation</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Table</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Record</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Action</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Details</th>
                        </tr>
                    </thead>
                    <tbody id="logs-table">
                        @forelse($logs as $log)
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    {{ $log->formatted_timestamp }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    {{ $log->user_name }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    {{ $log->log_type }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    {{ $log->operation }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    {{ $log->table_name }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    {{ $log->record_id }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    {{ $log->action }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    {{ $log->description }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 px-4 text-center text-gray-500">
                                    No audit logs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="mt-4">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </section>

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

            // Filter functionality with debounce
            document.getElementById('filter').addEventListener('input', function() {
                const form = document.getElementById('filter-form');
                clearTimeout(this.filterTimeout);
                this.filterTimeout = setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // Initialize when the document is ready
            document.addEventListener('DOMContentLoaded', () => {
                setupAlerts();
            });
        </script>
    @endpush
@endsection
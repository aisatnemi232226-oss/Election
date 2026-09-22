@extends('layouts.admin') 

@section('title', 'Verified Voters')
@section('dashboard-title', 'Admin Dashboard')

@section('content')

{{-- CSRF Token for AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<section class="text-center my-8">
    <h2 class="text-4xl font-bold text-gray-800 mb-4">Verify Voters</h2>
    <p class="text-gray-600 text-lg">Manage and verify voters for the upcoming elections.</p>
</section>

<section class="my-8">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <!-- Filter Section -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Labels Row -->
                <div class="md:col-span-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Filter Voters</label>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Academic Year</label>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Rows</label>
                </div>
            </div>
        
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Search Box -->
                <div class="md:col-span-5">
                    <input 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="search-input" 
                        type="text" 
                        placeholder="Search by name or ID number"
                        aria-label="Search by name or ID number"
                    >
                </div>
        
                <!-- Filter by Academic Year Dropdown -->
                <div class="md:col-span-3">
                    <select 
                        id="filter-acadyr-sem" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline"
                    >
                        <option value="">All Years</option>
                        @foreach (($academicYearSemesters ?? collect()) as $item)
                            @if (!empty($item['id']) && !empty($item['name']))
                                <option value="{{ $item['id'] }}">{{ $item['name'] }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
        
                <!-- Rows per page dropdown -->
                <div class="md:col-span-4">
                    <select 
                        id="rows-per-page" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline"
                    >
                        <option value="10">10 rows</option>
                        <option value="50">50 rows</option>
                        <option value="100">100 rows</option>
                        <option value="all">All rows</option>
                    </select>
                </div>
            </div>
        
            <!-- Import/Export Buttons BELOW the filters -->
            <div class="mt-4 flex flex-wrap gap-2 justify-start">
                <button 
                    id="import-button" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 rounded-md transition duration-200 flex items-center justify-center"
                >
                    <i class="fas fa-file-import mr-1"></i> Import
                </button>
                <button 
                    id="export-button" 
                    class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-3 rounded-md transition duration-200 flex items-center justify-center"
                >
                    <i class="fas fa-file-export mr-1"></i> Export
                </button>
            </div>
        </div>

        <!-- Success/Error Message -->
        <div id="message-box" class="hidden text-center p-4 mb-4 rounded-lg"></div>

        <!-- Import Failure Messages -->
        @if(session('importFailures'))
        <div id="import-failures" class="p-4 bg-red-100 text-red-800 rounded-lg mx-6 mb-4">
            <h4 class="font-bold mb-2">Import Failures:</h4>
            <ul class="list-disc list-inside">
                @foreach(session('importFailures') as $failure)
                    <li>Row {{ $failure['row'] }}: {{ implode(', ', $failure['errors']) }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Verified Voters Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Year</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="voters-table" class="bg-white divide-y divide-gray-200">
                    @php
                        // If $voters is a paginator, get its items; otherwise use the collection/array directly
                        $voterItems = $voters instanceof \Illuminate\Pagination\AbstractPaginator ? $voters->items() : ($voters ?? []);
                        $votersCollection = collect($voterItems);
                        $unverified = $votersCollection->filter(function($u) { return empty($u->verifiedVoter); });
                        $verified = $votersCollection->filter(function($u) { return !empty($u->verifiedVoter); });
                    @endphp

                    {{-- Render unverified first, then verified --}}
                    @foreach (($unverified ?? collect()) as $user)
                        @php($verified = $user->verifiedVoter)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->idnumber ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ optional($verified?->acadyr)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ optional($verified?->semesters)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    class="verify-voter bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-1.5 px-3 rounded-md transition duration-200"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                >
                                    Verify
                                </button>
                            </td>
                        </tr>
                    @endforeach

                    @foreach (($verified ?? collect()) as $user)
                        @php($verified = $user->verifiedVoter)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->idnumber ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ optional($verified?->acadyr)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ optional($verified?->semesters)->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    class="remove-voter bg-red-600 hover:bg-red-700 text-white font-medium py-1.5 px-3 rounded-md transition duration-200"
                                    data-id="{{ $verified->id }}"
                                >
                                    Remove
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Container - Outside the white box -->
    <div class="pagination-container mt-4"></div>
</section>

    {{-- Import Modal --}}
    <div id="import-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-auto">
            <h3 class="text-xl font-bold mb-4 text-gray-800">Import Verified Voters</h3>
            <p class="mb-4 text-gray-600">Upload a CSV file to import verified voters. You can download a sample file <a href="{{ route('admin.verified-voters.sample') }}" class="text-blue-600 hover:text-blue-800 underline">here</a>.</p>
            
            <div class="mb-4 border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                <input type="file" id="import-file-input" accept=".csv, .xlsx" class="hidden">
                <label for="import-file-input" class="cursor-pointer bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-2 px-4 rounded-md transition duration-200 inline-block">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Choose File
                </label>
                <p class="mt-2 text-sm text-gray-500" id="file-name-display">No file selected</p>
            </div>
            
            <div class="flex justify-end space-x-2">
                <button id="cancel-import-button" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition duration-200">Cancel</button>
                <button id="confirm-import-button" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">Import</button>
            </div>
        </div>
    </div>
</section>

<div id="verify-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-auto">
        <h3 class="text-xl font-bold mb-4 text-gray-800">Verify Voter</h3>
        <div class="mb-4 text-sm text-gray-700" id="verify-modal-name"></div>

        <input type="hidden" id="verify-user-id" value="">

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Academic Year</label>
            <select id="verify-acadyr" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                <option value="">Select Academic Year</option>
                @foreach (($acadyrs ?? collect()) as $ay)
                    <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Semester</label>
            <select id="verify-sem" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                <option value="">Select Semester</option>
                @foreach (($semesters ?? collect()) as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end space-x-2">
            <button id="cancel-verify-button" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 transition duration-200">Cancel</button>
            <button id="confirm-verify-button" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition duration-200">Verify</button>
        </div>
    </div>
</div>

{{-- Scripts for Search, Filtering, Import & Export --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    let currentPage = 1;

    $(document).ready(function() {
        fetchFilteredVoters(1);
    });

    function fetchFilteredVoters(page = 1) {
        $('#voters-table').html('<tr><td colspan="5" class="text-center py-8"><div class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div><span class="ml-2">Loading...</span></td></tr>');

        let search = $('#search-input').val();
        let acadyrSemester = $('#filter-acadyr-sem').val();
        let perPage = $('#rows-per-page').val();
        
        if (currentPage !== page && page === 1) {
            currentPage = 1;
        } else {
            currentPage = page;
        }
        
        let url = new URL(window.location);
        if (acadyrSemester) url.searchParams.set('acadyrSemester', acadyrSemester);
        else url.searchParams.delete('acadyrSemester');
        
        if (search) url.searchParams.set('search', search);
        else url.searchParams.delete('search');
        
        if (perPage) url.searchParams.set('perPage', perPage);
        else url.searchParams.delete('perPage');
        
        url.searchParams.set('page', currentPage);
        window.history.pushState({}, '', url);
        
        $.ajax({
            url: '{{ route("admin.verified-voters.search") }}',
            type: 'GET',
            data: { 
                search: search, 
                acadyrSemester: acadyrSemester,
                page: currentPage,
                perPage: perPage
            },
            success: function(response) {
                updateVotersTable(response.voters);
                updatePagination(response.pagination);
                applyFocusFromUrl();
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
                $('#voters-table').html('<tr><td colspan="5" class="text-center py-4 text-red-600">Error fetching data. Please try again.</td></tr>');
            }
        });
    }

    function updatePagination(pagination) {
        let html = '';

        if (pagination.total > pagination.per_page) {
            html += '<nav class="flex justify-between items-center">';
            
            // Results info
            html += '<div class="text-sm text-gray-700">';
            html += 'Showing <span class="font-medium">' + pagination.from + '</span>';
            html += ' to <span class="font-medium">' + pagination.to + '</span>';
            html += ' of <span class="font-medium">' + pagination.total + '</span> results';
            html += '</div>';

            // Pagination controls
            html += '<div class="flex gap-1">';

            // Previous Button
            if (pagination.current_page > 1) {
                html += '<a href="#" class="page-link px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-100" data-page="' + (pagination.current_page - 1) + '">&laquo;</a>';
            } else {
                html += '<span class="px-3 py-2 text-gray-400 bg-gray-200 rounded-l-md cursor-default">&laquo;</span>';
            }

            // Page Numbers
            let startPage = Math.max(1, pagination.current_page - 2);
            let endPage = Math.min(pagination.last_page, pagination.current_page + 2);

            for (let i = startPage; i <= endPage; i++) {
                if (i === pagination.current_page) {
                    html += '<span class="px-4 py-2 bg-blue-600 text-white font-medium border border-blue-600 rounded-md">' + i + '</span>';
                } else {
                    html += '<a href="#" class="page-link px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100" data-page="' + i + '">' + i + '</a>';
                }
            }

            // Next Button
            if (pagination.current_page < pagination.last_page) {
                html += '<a href="#" class="page-link px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-100" data-page="' + (pagination.current_page + 1) + '">&raquo;</a>';
            } else {
                html += '<span class="px-3 py-2 text-gray-400 bg-gray-200 rounded-r-md cursor-default">&raquo;</span>';
            }

            html += '</div></nav>';
        }

        $('.pagination-container').html(html);
    }

    // Add click handler for pagination links
    $(document).on('click', '.page-link', function (e) {
        e.preventDefault();
        let page = $(this).data('page');
        fetchFilteredVoters(page);
    });

    let debounceTimer;
    $('#search-input').on('keyup', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchFilteredVoters(1), 500);
    });

    $('#filter-acadyr-sem, #rows-per-page').on('change', function() {
        fetchFilteredVoters(1);
    });

    function updateVotersTable(voters) {
        let tableBody = $('#voters-table');
        tableBody.empty();
        // Ensure unverified voters are displayed first
        try {
            if (Array.isArray(voters)) {
                voters.sort(function(a, b) {
                    if ((a.is_verified === undefined ? false : a.is_verified) === (b.is_verified === undefined ? false : b.is_verified)) return 0;
                    return (a.is_verified ? 1 : -1);
                });
            }
        } catch (e) {
            console.error('Error sorting voters:', e);
        }

        if (!voters || voters.length === 0) {
            tableBody.append('<tr><td colspan="5" class="text-center py-8 text-gray-500">No voters found</td></tr>');
            return;
        }
        
        voters.forEach(v => {
            const userId = v.user ? v.user.id : '';
            const name = v.user ? (v.user.name || 'N/A') : 'N/A';
            const idnumber = v.user ? (v.user.idnumber || 'N/A') : 'N/A';
            const acadyr = v.acadyr ? (v.acadyr.name || 'N/A') : 'N/A';
            const sem = v.semesters ? (v.semesters.name || 'N/A') : 'N/A';
            const isVerified = !!v.is_verified;
            const verifiedId = v.verified_voter_id || '';

            const actionHtml = isVerified
                ? `<button class="remove-voter bg-red-600 hover:bg-red-700 text-white font-medium py-1.5 px-3 rounded-md transition duration-200" data-id="${verifiedId}">Remove</button>`
                : `<button class="verify-voter bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-1.5 px-3 rounded-md transition duration-200" data-user-id="${userId}" data-user-name="${name}">Verify</button>`;

            let row = `
                <tr class="hover:bg-gray-50" data-user-id="${userId}">
                    <td class="px-6 py-4 whitespace-nowrap">${name}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${idnumber}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${acadyr}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${sem}</td>
                    <td class="px-6 py-4 whitespace-nowrap">${actionHtml}</td>
                </tr>`;
            tableBody.append(row);
        });
    }

    function applyFocusFromUrl() {
        const params = new URLSearchParams(window.location.search);
        const focus = params.get('focus');
        if (!focus) return;
        const row = document.querySelector(`#voters-table tr[data-user-id="${focus}"]`);
        if (!row) return;
        row.scrollIntoView({ block: 'center' });
        row.classList.add('bg-yellow-50');
        setTimeout(() => row.classList.remove('bg-yellow-50'), 2500);
    }

    $(document).on('click', '.remove-voter', function () {
        let voterId = $(this).data('id');
        if (confirm("Are you sure you want to remove this voter?")) {
            $.ajax({
                url: `/admin/verified-voters/${voterId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    fetchFilteredVoters(currentPage);
                    
                    $('#message-box')
                        .removeClass('hidden bg-red-100 text-red-800')
                        .addClass('bg-green-100 text-green-800')
                        .text('Voter removed successfully!')
                        .show();
                    
                    setTimeout(() => $('#message-box').fadeOut('slow', function() {
                        $(this).addClass('hidden');
                    }), 3000);
                },
                error: function() {
                    $('#message-box')
                        .removeClass('hidden bg-green-100 text-green-800')
                        .addClass('bg-red-100 text-red-800')
                        .text('Failed to remove voter. Please try again.')
                        .show();
                    
                    setTimeout(() => $('#message-box').fadeOut('slow', function() {
                        $(this).addClass('hidden');
                    }), 3000);
                }
            });
        }
    });

    $(document).on('click', '.verify-voter', function () {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        $('#verify-user-id').val(userId);
        $('#verify-modal-name').text(userName ? `Verify: ${userName}` : 'Verify voter');
        $('#verify-acadyr').val('');
        $('#verify-sem').val('');
        $('#verify-modal').removeClass('hidden').addClass('flex');
    });

    $('#cancel-verify-button').click(function() {
        $('#verify-modal').removeClass('flex').addClass('hidden');
    });

    $('#verify-modal').click(function(e) {
        if (e.target === this) {
            $(this).removeClass('flex').addClass('hidden');
        }
    });

    $('#confirm-verify-button').click(function() {
        const userId = $('#verify-user-id').val();
        const acadyrId = $('#verify-acadyr').val();
        const semId = $('#verify-sem').val();

        if (!userId || !acadyrId || !semId) {
            alert('Please select Academic Year and Semester.');
            return;
        }

        $.ajax({
            url: '{{ route("admin.verified-voters.store") }}',
            type: 'POST',
            data: {
                user_id: userId,
                acadyr_id: acadyrId,
                sem_id: semId
            },
            beforeSend: function() {
                $('#confirm-verify-button')
                    .prop('disabled', true)
                    .text('Verifying...');
            },
            success: function() {
                $('#verify-modal').removeClass('flex').addClass('hidden');
                fetchFilteredVoters(currentPage);
                $('#message-box')
                    .removeClass('hidden bg-red-100 text-red-800')
                    .addClass('bg-green-100 text-green-800')
                    .text('Voter verified successfully!')
                    .show();
                setTimeout(() => $('#message-box').fadeOut('slow', function() {
                    $(this).addClass('hidden');
                }), 3000);
            },
            error: function(xhr) {
                let msg = 'Failed to verify voter. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                $('#message-box')
                    .removeClass('hidden bg-green-100 text-green-800')
                    .addClass('bg-red-100 text-red-800')
                    .text(msg)
                    .show();
                setTimeout(() => $('#message-box').fadeOut('slow', function() {
                    $(this).addClass('hidden');
                }), 5000);
            },
            complete: function() {
                $('#confirm-verify-button')
                    .prop('disabled', false)
                    .text('Verify');
            }
        });
    });

    $('#export-button').on('click', function () {
        let semesterId = $('#filter-acadyr-sem').val();
        let exportUrl = '{{ route("admin.verified-voters.export") }}' + (semesterId ? '?semester_id=' + semesterId : '');
        window.location.href = exportUrl;
    });

    $('#import-button').click(function() {
        $('#import-modal').removeClass('hidden').addClass('flex');
        $('#import-file-input').val('');
        $('#file-name-display').text('No file selected');
    });

    $('#import-file-input').change(function() {
        if (this.files.length > 0) {
            $('#file-name-display').text(this.files[0].name);
        } else {
            $('#file-name-display').text('No file selected');
        }
    });

    $('#confirm-import-button').click(function() {
        let fileInput = $('#import-file-input')[0];
        if (fileInput.files.length === 0) {
            alert('Please select a file to import.');
            return;
        }
        
        let formData = new FormData();
        formData.append('importFile', fileInput.files[0]);
        
        $.ajax({
            url: '{{ route("admin.verified-voters.import") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#confirm-import-button')
                    .prop('disabled', true)
                    .html('<span class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span> Importing...');
            },
            success: function(response) {
                $('#import-modal').removeClass('flex').addClass('hidden');
                fetchFilteredVoters(1);
                
                $('#message-box')
                    .removeClass('hidden bg-red-100 text-red-800')
                    .addClass('bg-green-100 text-green-800')
                    .text('Voters imported successfully!')
                    .show();
                
                setTimeout(() => $('#message-box').fadeOut('slow', function() {
                    $(this).addClass('hidden');
                }), 3000);
            },
            error: function(xhr) {
                console.error("Import error:", xhr);
                let errorMsg = 'Error importing voters';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg += ': ' + xhr.responseJSON.message;
                }
                
                $('#message-box')
                    .removeClass('hidden bg-green-100 text-green-800')
                    .addClass('bg-red-100 text-red-800')
                    .text(errorMsg)
                    .show();
                
                setTimeout(() => $('#message-box').fadeOut('slow', function() {
                    $(this).addClass('hidden');
                }), 5000);
            },
            complete: function() {
                $('#confirm-import-button')
                    .prop('disabled', false)
                    .html('Import');
            }
        });
    });

    $('#cancel-import-button').click(function() {
        $('#import-modal').removeClass('flex').addClass('hidden');
    });

    $('#import-modal').click(function(e) {
        if (e.target === this) {
            $(this).removeClass('flex').addClass('hidden');
        }
    });
});
</script>

@endsection

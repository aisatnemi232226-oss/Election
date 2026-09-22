@extends('layouts.admin')

@section('title', 'Manage Elections')
@section('dashboard-title', 'Manage Elections')

@section('content')
    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Manage Elections</h2>
        <p class="text-gray-600 text-lg">Here you can manage all the elections.</p>
    </section>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-800">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="my-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-3xl font-bold text-gray-800">Elections List</h3>
            <div class="flex items-center gap-2">
                <a
                    href="{{ ($isArchived ?? false) ? route('admin.elections.index') : route('admin.elections.index', ['archived' => 1]) }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    {{ ($isArchived ?? false) ? 'Back to List' : 'Archive' }}
                </a>

                @if (!($isArchived ?? false))
                    <button type="button" id="openCreateElectionModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">New</button>
                @endif
            </div>
        </div>

        <div id="createElectionModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-black/50" data-modal-backdrop></div>
            <div class="relative mx-auto mt-24 w-full max-w-lg px-4">
                <div class="bg-white rounded-lg shadow-lg">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h4 class="text-lg font-bold text-gray-800">Create Election</h4>
                        <button type="button" id="closeCreateElectionModal" class="text-gray-500 hover:text-gray-700" aria-label="Close modal">
                            <span class="text-2xl leading-none">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.elections.store') }}" method="POST" class="px-6 py-5">
                        @csrf

                        <div class="space-y-4">
                            <div>
                                <label for="election_name" class="block text-sm font-medium text-gray-700">Election Name</label>
                                <input id="election_name" name="name" type="text" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="election_start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <input id="election_start_date" name="start_date" type="date" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                                <div>
                                    <label for="election_end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                    <input id="election_end_date" name="end_date" type="date" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="election_reg_start" class="block text-sm font-medium text-gray-700">Registration Start</label>
                                    <input id="election_reg_start" name="reg_start" type="date" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                                <div>
                                    <label for="election_reg_end" class="block text-sm font-medium text-gray-700">Registration End</label>
                                    <input id="election_reg_end" name="reg_end" type="date" required class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                </div>
                            </div>

                            <div>
                                <label for="election_academic_year" class="block text-sm font-medium text-gray-700">Allowed Academic Year</label>
                                <select id="election_academic_year" name="academic_year_id" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Academic Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="election_candidates_group" class="block text-sm font-medium text-gray-700">Candidates Group</label>
                                <input id="election_candidates_group" name="candidates_group" type="text" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., College of Engineering" />
                            </div>

                            <div>
                                <label for="election_candidates_result_announcements" class="block text-sm font-medium text-gray-700">Candidates Result Announcements</label>
                                <textarea id="election_candidates_result_announcements" name="candidates_result_announcements" rows="3" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Announcement text for results"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" id="cancelCreateElectionModal" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300">Cancel</button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-center text-gray-600 font-bold ">Election Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-center text-gray-600 font-bold">Start Date</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-center text-gray-600 font-bold">End Date</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-center text-gray-600 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($elections as $election)
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-300 text-center">{{ $election->name }}</td>
                                <td class="py-2 px-4 border-b border-gray-300 text-center">{{ \Carbon\Carbon::parse($election->start_date)->format('Y-m-d') }}</td>
                                <td class="py-2 px-4 border-b border-gray-300 text-center">{{ \Carbon\Carbon::parse($election->end_date)->format('Y-m-d') }}</td>

                                <td class="py-2 px-4 border-b border-gray-300 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('admin.view-election.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-block">View</a>

                                        @if (!($isArchived ?? false))
                                            <a href="{{ route('admin.elections.edit', $election->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-block">Edit</a>
                                            <form action="{{ route('admin.elections.destroy', $election->id) }}" method="POST" class="inline-block align-middle">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.elections.restore', $election->id) }}" method="POST" class="inline-block align-middle">
                                                @csrf
                                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Restore</button>
                                            </form>
                                            <form action="{{ route('admin.elections.forceDestroy', $election->id) }}" method="POST" class="inline-block align-middle">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800">Permanent Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-2 px-4 border-b border-gray-300 text-center">No elections found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('createElectionModal');
        const openBtn = document.getElementById('openCreateElectionModal');
        const closeBtn = document.getElementById('closeCreateElectionModal');
        const cancelBtn = document.getElementById('cancelCreateElectionModal');
        const backdrop = modal ? modal.querySelector('[data-modal-backdrop]') : null;
        const firstInput = document.getElementById('election_name');

        function openModal() {
            if (!modal) return;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            if (firstInput) firstInput.focus();
        }

        function closeModal() {
            if (!modal) return;
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        if (openBtn) openBtn.addEventListener('click', openModal);
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
        if (backdrop) backdrop.addEventListener('click', closeModal);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });
    });
</script>
@endpush

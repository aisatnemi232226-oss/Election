@extends('layouts.admin')

@section('content')
    <main class="container mx-auto p-4">
        <section class="text-center my-8">
            <h2 class="text-4xl font-bold text-gray-800 mb-2">Enrolled Students</h2>
            <p class="text-gray-600 text-lg">List of registered student users.</p>
        </section>

        <section class="my-8 bg-white p-6 rounded-lg shadow-lg">
            <form id="user-list-filters" method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-wrap items-end justify-between gap-4">
                <div class="flex min-w-0 flex-1 flex-wrap items-end gap-2">
                    <div>
                        <label for="user-search-type" class="mb-1 block text-sm font-medium text-gray-700">Search in</label>
                        <select id="user-search-type" name="search_type" class="w-36 rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">
                            <option value="all" @selected($searchType === 'all')>All fields</option>
                            <option value="name" @selected($searchType === 'name')>Name</option>
                            <option value="email" @selected($searchType === 'email')>Email</option>
                            <option value="idnumber" @selected($searchType === 'idnumber')>Student No.</option>
                        </select>
                    </div>
                    <div>
                        <label for="user-search" class="mb-1 block text-sm font-medium text-gray-700">Search</label>
                        <input id="user-search" type="search" name="search" value="{{ $search }}" placeholder="Enter search term" class="w-full min-w-64 rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200 md:w-96">
                    </div>
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">Search</button>
                </div>
                <div class="ml-auto flex flex-wrap items-end gap-3">
                    <div>
                        <label for="user-sort" class="mb-1 block text-sm font-medium text-gray-700">Sort by</label>
                        <select id="user-sort" name="sort" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" onchange="this.form.requestSubmit()">
                            <option value="name" @selected($sort === 'name')>Name</option>
                            <option value="email" @selected($sort === 'email')>Email</option>
                            <option value="idnumber" @selected($sort === 'idnumber')>Student No.</option>
                            <option value="course" @selected($sort === 'course')>Course</option>
                            <option value="year_level" @selected($sort === 'year_level')>Year Level</option>
                            <option value="verified" @selected($sort === 'verified')>Verified Status</option>
                        </select>
                    </div>
                    <div>
                        <label for="user-sort-direction" class="mb-1 block text-sm font-medium text-gray-700">Order</label>
                        <select id="user-sort-direction" name="direction" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" onchange="this.form.requestSubmit()">
                            <option value="asc" @selected($direction === 'asc')>A–Z / Low–High</option>
                            <option value="desc" @selected($direction === 'desc')>Z–A / High–Low</option>
                        </select>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Email</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Student No.</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">
                                <label for="user-course-filter" class="sr-only">Filter by course</label>
                                <select id="user-course-filter" form="user-list-filters" name="course" class="max-w-48 rounded-md border border-gray-300 bg-white px-2 py-1 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" onchange="this.form.requestSubmit()">
                                    <option value="">All Courses</option>
                                    <option value="__none__" @selected($courseFilter === '__none__')>No Course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course }}" @selected($courseFilter === $course)>{{ $course }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">
                                <label for="user-year-level-filter" class="sr-only">Filter by year level</label>
                                <select id="user-year-level-filter" form="user-list-filters" name="year_level" class="max-w-48 rounded-md border border-gray-300 bg-white px-2 py-1 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" onchange="this.form.requestSubmit()">
                                    <option value="">All Year Levels</option>
                                    <option value="__none__" @selected($yearLevelFilter === '__none__')>No Year Level</option>
                                    @foreach ($yearLevels as $yearLevel)
                                        <option value="{{ $yearLevel }}" @selected($yearLevelFilter === $yearLevel)>{{ $yearLevel }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">
                                <label for="user-verified-filter" class="sr-only">Filter by verification status</label>
                                <select id="user-verified-filter" form="user-list-filters" name="verified" class="max-w-48 rounded-md border border-gray-300 bg-white px-2 py-1 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" onchange="this.form.requestSubmit()">
                                    <option value="">All Students</option>
                                    <option value="verified" @selected($verifiedFilter === 'verified')>Verified</option>
                                    <option value="unverified" @selected($verifiedFilter === 'unverified')>Not Verified</option>
                                </select>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $user->name }}</td>
                                <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $user->email }}</td>
                                <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $user->idnumber ?: 'N/A' }}</td>
                                <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $user->course ?: 'N/A' }}</td>
                                <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $user->year_level ?: 'N/A' }}</td>
                                <td class="py-2 px-4 border-b border-gray-200">
                                    @if ($user->verified_voter_exists)
                                        <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Verified</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">Not Verified</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 px-4 text-center text-gray-500">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </section>
    </main>
@endsection

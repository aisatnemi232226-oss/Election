@extends('layouts.admin')

@section('content')
    <main class="users-page container mx-auto p-4">
        <section class="users-page-heading text-center my-8">
            <h2 class="text-4xl font-bold text-gray-800 mb-2">Enrolled Students</h2>
            <p class="text-gray-600 text-lg">List of registered student users.</p>
        </section>

        <section class="users-list-card my-8 bg-white p-6 rounded-lg shadow-lg">
            <form id="user-list-filters" method="GET" action="{{ route('admin.users.index') }}" class="users-filter-form">
                <div class="users-search-group">
                    <div class="users-filter-field">
                        <label for="user-search-type">Search in</label>
                        <select id="user-search-type" name="search_type">
                            <option value="all" @selected($searchType === 'all')>All fields</option>
                            <option value="name" @selected($searchType === 'name')>Name</option>
                            <option value="email" @selected($searchType === 'email')>Email</option>
                            <option value="idnumber" @selected($searchType === 'idnumber')>Student No.</option>
                        </select>
                    </div>
                    <div class="users-filter-field">
                        <label for="user-search">Search</label>
                        <input id="user-search" type="search" name="search" value="{{ $search }}" placeholder="Enter search term">
                    </div>
                </div>
                <div class="users-filter-field">
                    <label for="user-sort">Sort by</label>
                    <select id="user-sort" name="sort" onchange="this.form.requestSubmit()">
                        <option value="name" @selected($sort === 'name')>Name</option>
                        <option value="email" @selected($sort === 'email')>Email</option>
                        <option value="idnumber" @selected($sort === 'idnumber')>Student No.</option>
                        <option value="course" @selected($sort === 'course')>Course</option>
                        <option value="year_level" @selected($sort === 'year_level')>Year Level</option>
                        <option value="verified" @selected($sort === 'verified')>Verified Status</option>
                    </select>
                </div>
                <div class="users-filter-field">
                    <label for="user-sort-direction">Order</label>
                    <select id="user-sort-direction" name="direction" onchange="this.form.requestSubmit()">
                        <option value="asc" @selected($direction === 'asc')>A–Z / Low–High</option>
                        <option value="desc" @selected($direction === 'desc')>Z–A / High–Low</option>
                    </select>
                </div>
                <button type="submit" class="users-search-button rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">Search students</button>
            </form>

            <p class="users-table-hint">Scroll horizontally to see all student details.</p>
            <div class="users-table-scroll" role="region" aria-label="Enrolled students table" tabindex="0">
                <table class="users-table min-w-full bg-white">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Student No.</th>
                            <th scope="col">
                                <label class="users-table-filter-label" for="user-course-filter">Course</label>
                                <select id="user-course-filter" form="user-list-filters" name="course" aria-label="Filter by course" onchange="this.form.requestSubmit()">
                                    <option value="" @selected($courseFilter === '')>All Courses</option>
                                    <option value="__none__" @selected($courseFilter === '__none__')>No Course</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course }}" @selected($courseFilter === $course)>{{ $course }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th scope="col">
                                <label class="users-table-filter-label" for="user-year-level-filter">Year level</label>
                                <select id="user-year-level-filter" form="user-list-filters" name="year_level" aria-label="Filter by year level" onchange="this.form.requestSubmit()">
                                    <option value="" @selected($yearLevelFilter === '')>All Year Levels</option>
                                    <option value="__none__" @selected($yearLevelFilter === '__none__')>No Year Level</option>
                                    @foreach ($yearLevels as $yearLevel)
                                        <option value="{{ $yearLevel }}" @selected($yearLevelFilter === $yearLevel)>{{ $yearLevel }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th scope="col">
                                <label class="users-table-filter-label" for="user-verified-filter">Verification</label>
                                <select id="user-verified-filter" form="user-list-filters" name="verified" aria-label="Filter by verification status" onchange="this.form.requestSubmit()">
                                    <option value="" @selected($verifiedFilter === '')>All Students</option>
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

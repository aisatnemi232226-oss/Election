@extends('layouts.admin')

@section('title', 'Manage Elections')
@section('dashboard-title', 'Manage Elections')

@section('content')
    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Manage Elections</h2>
        <p class="text-gray-600 text-lg">Here you can manage all the elections.</p>
    </section>

    <section class="my-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-3xl font-bold text-gray-800">Elections List</h3>
            <a href="{{ route('admin.elections.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">New</a>
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
                                        <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-block">Edit</a>
                                        <form action="{{ route('admin.elections.destroy', $election->id) }}" method="POST" class="inline-block align-middle">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</button>
                                        </form>
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

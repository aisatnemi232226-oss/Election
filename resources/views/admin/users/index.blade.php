@extends('layouts.admin')

@section('content')
    <main class="container mx-auto p-4">
        <section class="text-center my-8">
            <h2 class="text-4xl font-bold text-gray-800 mb-2">Enrolled Students</h2>
            <p class="text-gray-600 text-lg">List of registered student users.</p>
        </section>

        <section class="my-8 bg-white p-6 rounded-lg shadow-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Email</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Student No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $user->name }}</td>
                                <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $user->email }}</td>
                                <td class="py-2 px-4 border-b border-gray-200 text-gray-700">{{ $user->idnumber }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 px-4 text-center text-gray-500">No students found.</td>
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

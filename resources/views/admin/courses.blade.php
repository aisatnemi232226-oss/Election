@extends('layouts.admin')

@section('title', 'Courses')
@section('dashboard-title', 'Admin Dashboard')

@section('content')
    <style>
        body {
            font-family: 'Roboto', sans-serif;
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
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Course List</h2>
        <p class="text-gray-600 text-lg">Here you can view and manage the list of courses offered.</p>
    </section>

    <section class="my-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-3xl font-bold text-gray-800">Courses</h3>
            <a href="{{ route('admin.courses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                New Course
            </a>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-lg">            
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Course ID</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Course Code</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Course Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-300">{{ $course->id }}</td>
                                <td class="py-2 px-4 border-b border-gray-300">{{ $course->coursecode }}</td>
                                <td class="py-2 px-4 border-b border-gray-300">{{ $course->name }}</td>
                                <td class="py-2 px-4 border-b border-gray-300">
                                    <form method="POST" action="{{ route('admin.courses.destroy', $course->id) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this course?')" 
                                          style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                                    No courses found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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

            // Initialize when the document is ready
            document.addEventListener('DOMContentLoaded', () => {
                setupAlerts();
            });
        </script>
    @endpush
@endsection
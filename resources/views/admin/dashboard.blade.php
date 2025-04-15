@extends('layouts.admin')

@section('content')
    <main class="container mx-auto p-4">
        <section class="text-center my-8">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Admin Dashboard</h2>
            <p class="text-gray-600 text-lg">Welcome to the admin dashboard. Here you can manage elections, candidates, voters, and view reports.</p>
        </section>
        
        <section class="my-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Total Elections</h3>
                <p class="text-4xl font-bold text-blue-600">5</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Total Candidates</h3>
                <p class="text-4xl font-bold text-green-600">20</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Total Voters</h3>
                <p class="text-4xl font-bold text-red-600">1500</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">Total Enrolled Students</h3>
                <p class="text-4xl font-bold text-purple-600">2000</p>
            </div>
        </section>

        <section class="my-8 bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-3xl font-bold text-gray-800 mb-4">Recent Elections</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Election Name</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Date</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Status</th>
                            <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Voter Turnout</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-300">Presidential Election 2023</td>
                            <td class="py-2 px-4 border-b border-gray-300">2023-05-01</td>
                            <td class="py-2 px-4 border-b border-gray-300">Completed</td>
                            <td class="py-2 px-4 border-b border-gray-300">1200</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-300">Student Council Election 2023</td>
                            <td class="py-2 px-4 border-b border-gray-300">2023-04-15</td>
                            <td class="py-2 px-4 border-b border-gray-300">Completed</td>
                            <td class="py-2 px-4 border-b border-gray-300">1100</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
@endsection

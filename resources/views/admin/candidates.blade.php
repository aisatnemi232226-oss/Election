@extends('layouts.admin')

@section('title', 'Candidate')
@section('dashboard-title', 'Candidates')

@section('content')
    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Manage Candidates</h2>
        <p class="text-gray-600 text-lg">Here you can manage all the candidates for the elections.</p>
    </section>

    <section class="my-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-3xl font-bold text-gray-800">Candidates List</h3>
            <a href="{{ route('admin.candidates.new') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">New Candidate</a>

        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border-collapse">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Name</th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Course</th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Party</th>
                            <th class="py-2 px-4 border-b-2 text-left text-gray-600 font-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $candidate)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ $candidate->name }}</td>
                                <td class="py-2 px-4 border-b">{{ optional($candidate->course)->name }}</td>
                                <td class="py-2 px-4 border-b">{{ $candidate->party ? $candidate->party->name : 'No Party' }}</td>
                                
                                <td class="py-2 px-4 border-b flex gap-2">
                                    <button 
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 view-button"
                                        data-candidate="{{ $candidate->name }}"
                                        data-course="{{ optional($candidate->course)->name }}"
                                        data-party="{{ $candidate->party ? $candidate->party->name : 'No Party' }}"
                                        data-description="{{ $candidate->description }}"
                                        data-profile-image="{{ $candidate->profile_img ? asset('storage/upload/profile-picture/' . $candidate->profile_img) : asset('images/default-profile.jpg') }}"
                                        data-banner-image="{{ $candidate->banner_img ? asset('storage/upload/banner/' . $candidate->banner_img) : asset('images/default-banner.png') }}"
                                        data-platforms="{{ $candidate->platforms ?? 'No Platforms' }}"
                                        data-related-events="Organized Tech Fest 2022, Led Community Service Day 2021"
                                        data-joined-elections='[{"year": "2022", "position": "Vice President", "winner": true}, {"year": "2021", "position": "Representative", "winner": false}]'>
                                        View
                                    </button>

                                    <button onclick="window.location.href= '{{ route('admin.candidates.edit', $candidate->id) }}'"
                                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Edit</button>

                                    <form method="POST" action="{{ route('admin.candidates.destroy', $candidate->id) }}" onsubmit="return confirmDelete(event);">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Candidate Modal -->
    <div class="fixed z-[100] inset-0 overflow-y-auto hidden" id="candidate-modal">
        <div class="flex items-center justify-center min-h-screen">
            <div aria-hidden="true" class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="bg-white p-6">
                    <img id="candidate-banner-image" class="w-full h-32 object-cover rounded-lg mb-4" alt="Banner Image">
                    <div class="flex items-center mb-4">
                        <img id="candidate-profile-image" class="w-16 h-16 object-cover rounded-full mr-4" alt="Profile Image">
                        <h3 class="text-2xl font-bold" id="candidate-name"></h3>
                    </div>
                    <p class="text-gray-800 mb-2"><strong>Course:</strong> <span id="candidate-course"></span></p>
                    <p class="text-gray-800 mb-2"><strong>Party:</strong> <span id="candidate-party"></span></p>
                    <p class="text-gray-800 mb-2" id="candidate-description"></p>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Platforms</h4>
                    <ul class="list-disc list-inside text-gray-800 mb-2" id="candidate-platforms"></ul>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Related Events</h4>
                    <ul class="list-disc list-inside text-gray-800 mb-2" id="candidate-related-events"></ul>
                    <h4 class="text-xl font-bold text-gray-800 mb-2">Joined Elections</h4>
                    <ul class="list-disc list-inside text-gray-800 mb-2" id="candidate-joined-elections"></ul>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mt-4" id="close-modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.view-button').forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('candidate-name').textContent = button.getAttribute('data-candidate') || "N/A";
                    document.getElementById('candidate-course').textContent = button.getAttribute('data-course') || "N/A";
                    document.getElementById('candidate-party').textContent = button.getAttribute('data-party') || "N/A";
                    document.getElementById('candidate-description').textContent = button.getAttribute('data-description') || "No description available.";

                    let profileImage = document.getElementById('candidate-profile-image');
                    if (profileImage) profileImage.src = button.getAttribute('data-profile-image') || "default-profile.jpg";

                    let bannerImage = document.getElementById('candidate-banner-image');
                    if (bannerImage) bannerImage.src = button.getAttribute('data-banner-image') || "default-banner.png";

                    // ✅ Fix: Ensure elements exist before modifying them
                    let platformList = document.getElementById('candidate-platforms');
                    platformList.innerHTML = '';
                    let platforms = button.getAttribute('data-platforms') ? button.getAttribute('data-platforms').split(', ') : [];
                    platforms.forEach(platform => {
                        let li = document.createElement('li');
                        li.textContent = platform;
                        platformList.appendChild(li);
                    });

                    let eventList = document.getElementById('candidate-related-events');
                    eventList.innerHTML = '';
                    let events = button.getAttribute('data-related-events') ? button.getAttribute('data-related-events').split(', ') : [];
                    events.forEach(event => {
                        let li = document.createElement('li');
                        li.textContent = event;
                        eventList.appendChild(li);
                    });

                    let electionList = document.getElementById('candidate-joined-elections');
                    electionList.innerHTML = '';
                    let electionsData = button.getAttribute('data-joined-elections');
                    if (electionsData) {
                        try {
                            let elections = JSON.parse(electionsData);
                            elections.forEach(election => {
                                let li = document.createElement('li');
                                li.textContent = `${election.year} - ${election.position} ${election.winner ? '(Winner)' : ''}`;
                                electionList.appendChild(li);
                            });
                        } catch (error) {
                            console.error("Error parsing joined elections:", error);
                        }
                    }

                    document.getElementById('candidate-modal').classList.remove('hidden');
                });
            });

            document.getElementById('close-modal').addEventListener('click', () => {
                document.getElementById('candidate-modal').classList.add('hidden');
            });
        });

        function confirmDelete(event) {
            if (!confirm('Are you sure you want to delete this candidate? This action cannot be undone.')) {
                event.preventDefault();
            }
        }
    </script>
    @endpush
@endsection

@extends('layouts.admin')

@section('title', 'View Results')
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

    <section class="text-center my-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Election Results</h2>
        <p class="text-gray-600 text-lg">View the results of the recent elections.</p>
    </section>

    <section class="my-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
                <img alt="Result for Presidential Election showing the winning candidate" 
                     class="rounded-lg mx-auto mb-4" 
                     height="200" 
                     src="https://storage.googleapis.com/a1aa/image/Fxij7q9yDiZMLRqOKK1E9eF9D8jv8mS1NNInJlyLUgfzCvGUA.jpg" 
                     width="300"/>
                <h4 class="text-2xl font-bold text-gray-800 mb-2">Presidential Election</h4>
                <p class="text-gray-600">Total Votes: 1200</p>
                <p class="text-gray-600">Total Registered: 1500</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
                <img alt="Result for Vice Presidential Election showing the winning candidate" 
                     class="rounded-lg mx-auto mb-4" 
                     height="200" 
                     src="https://storage.googleapis.com/a1aa/image/ehlyeLQdiOnekpoFelpHRbK67Sw9wWdnk3HsCXjoZCUeW41gC.jpg" 
                     width="300"/>
                <h4 class="text-2xl font-bold text-gray-800 mb-2">Vice Presidential Election</h4>
                <p class="text-gray-600">Total Votes: 1100</p>
                <p class="text-gray-600">Total Registered: 1500</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
                <img alt="Result for Election 2025 showing the winning candidates for all positions" 
                     class="rounded-lg mx-auto mb-4" 
                     height="200" 
                     src="https://storage.googleapis.com/a1aa/image/e3zfovjnPqhB60pmwjFfrmvZPqKu9uLuh79qVuvSJLFpFeaQB.jpg" 
                     width="300"/>
                <h4 class="text-2xl font-bold text-gray-800 mb-2">Election 2025 (All Positions)</h4>
                <p class="text-gray-600">Total Votes: 1050</p>
                <p class="text-gray-600">Total Registered: 1500</p>
            </div>
        </div>
    </section>

    <section class="my-8">
        <h3 class="text-3xl font-bold text-gray-800 mb-4">Poll Results</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
                <img alt="Result for Favorite Presidential Candidate poll" 
                     class="rounded-lg mx-auto mb-4" 
                     height="200" 
                     src="https://storage.googleapis.com/a1aa/image/mPeWnsgHfAgSZklazzsc6QqvNiE7X1Vesfe7wxAuCTt1W41gC.jpg" 
                     width="300"/>
                <h4 class="text-2xl font-bold text-gray-800 mb-2">Favorite Presidential Candidate</h4>
                <p class="text-gray-600">Most Voted: John Doe</p>
                <p class="text-gray-600">Votes: 800</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
                <img alt="Result for Most Important Election Issue poll" 
                     class="rounded-lg mx-auto mb-4" 
                     height="200" 
                     src="https://storage.googleapis.com/a1aa/image/0ifxZa28k00Wf0ODn1oBGrdqGJq8eG7PE4BYtp6Zi7gqFeaQB.jpg" 
                     width="300"/>
                <h4 class="text-2xl font-bold text-gray-800 mb-2">Most Important Election Issue</h4>
                <p class="text-gray-600">Most Voted: Campus Safety</p>
                <p class="text-gray-600">Votes: 900</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
                <img alt="Result for Preferred Voting Method poll" 
                     class="rounded-lg mx-auto mb-4" 
                     height="200" 
                     src="https://storage.googleapis.com/a1aa/image/vmrKfoRjlKyFYqXU6yTQK9KsY9cnWIxMmIfpMKEO0hfkFeaQB.jpg" 
                     width="300"/>
                <h4 class="text-2xl font-bold text-gray-800 mb-2">Preferred Voting Method</h4>
                <p class="text-gray-600">Most Voted: Online Voting</p>
                <p class="text-gray-600">Votes: 950</p>
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
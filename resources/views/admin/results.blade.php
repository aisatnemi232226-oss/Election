@extends('layouts.admin')

@section('title', 'View Results')
@section('dashboard-title', 'Admin Dashboard')

@section('content')
@php
    // ensure variables exist to avoid "Undefined variable" errors
    $elections = $elections ?? [];
    $polls     = $polls ?? [];
@endphp

    <style>
        /* filepath: styles for responsive cards and progress */
        body { font-family: 'Roboto', sans-serif; }
        .page-container { max-width: 1200px; margin: 0 auto; padding: 1rem; }
        .section-card { background: #fff; border-radius: .75rem; padding: 1rem; box-shadow: 0 6px 18px rgba(17,24,39,0.06); }
        .tile { transition: transform .18s ease, box-shadow .18s ease; }
        .tile:hover, .tile:focus-within { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(17,24,39,0.08); }
        .media { display: flex; gap: 1rem; align-items: center; }
        .media img.rounded-img { width: 96px; height: 96px; border-radius: 0.5rem; object-fit: cover; flex-shrink: 0; }
        .card-body { flex: 1; min-width: 0; }
        .muted { color: #6b7280; font-size: .92rem; }
        .pill { display:inline-block; padding: .15rem .5rem; border-radius: .5rem; background: #eef2ff; color:#3730a3; font-weight:600; font-size:.8rem; }
        .progress-wrap { background: #eef2f7; border-radius: 999px; height: 10px; overflow:hidden; margin-top:.5rem; }
        .progress-bar { height:100%; background: linear-gradient(90deg,#3b82f6,#60a5fa); width:0%; transition: width 1s ease; }
        .meta-row { display:flex; gap: .75rem; align-items:center; margin-top:.5rem; flex-wrap:wrap; }
        @media (max-width: 768px) {
            .media { flex-direction: row; }
            .media img.rounded-img { width:72px; height:72px; }
        }
    </style>

    <section class="text-center my-6">
        <div class="page-container">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Election Results</h1>
                    <p class="muted">View the detailed results of the election.</p>
                </div>
                <div class="flex items-center gap-2">
                    @if(\Illuminate\Support\Facades\Route::has('admin.elections.index'))
                        <a href="{{ route('admin.elections.index') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded shadow">Manage Elections</a>
                    @endif
                    @if(\Illuminate\Support\Facades\Route::has('admin.polls.index'))
                        <a href="{{ route('admin.polls.index') }}" class="inline-block bg-gray-100 text-gray-800 px-4 py-2 rounded shadow">Manage Polls</a>
                    @endif
                </div>
            </div>

            {{-- Elections Section --}}
            <div class="section-card mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-3">Candidate Tally</h2>

                @if(!empty($elections) && count($elections))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($elections as $election)
                            <article class="tile p-4 rounded-lg" tabindex="0" role="group" aria-label="{{ data_get($election,'title','Election') }}">
                                <div class="media">
                                    <img alt="{{ data_get($election,'title','Election Image') }}" src="{{ data_get($election,'image_url','https://via.placeholder.com/300x200?text=No+Image') }}" class="rounded-img">
                                    <div class="card-body">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-semibold text-gray-800 no-wrap">{{ data_get($election,'title','Unnamed Election') }}</h3>
                                            @if(data_get($election,'winner_name'))
                                                <span class="pill" title="Winner">Winner</span>
                                            @endif
                                        </div>

                                        <div class="meta-row">
                                            @if(data_get($election,'winner_name'))
                                                <div class="muted">Top: <strong class="text-gray-800">{{ data_get($election,'winner_name') }}</strong></div>
                                            @endif
                                            <div class="muted">Votes: <strong>{{ number_format(data_get($election,'total_votes',0)) }}</strong></div>
                                            <div class="muted">Registered: <strong>{{ number_format(data_get($election,'total_registered',0)) }}</strong></div>
                                        </div>

                                        <div class="progress-wrap" aria-hidden="true">
                                            <div class="progress-bar" data-percent="{{ min(100, (int) round((data_get($election,'total_votes',0) / max(1, data_get($election,'total_registered',0))) * 100)) }}"></div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="text-center muted py-6">No election results available.</div>
                @endif
            </div>

            {{-- Polls Section --}}
            <div class="section-card">
                <h2 class="text-xl font-semibold text-gray-800 mb-3">Poll Results</h2>

                @if(!empty($polls) && count($polls))
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($polls as $poll)
                            <article class="tile p-4 rounded-lg" tabindex="0" role="group" aria-label="{{ data_get($poll,'title','Poll') }}">
                                <div class="media">
                                    <img alt="{{ data_get($poll,'title','Poll Image') }}" src="{{ data_get($poll,'image_url','https://via.placeholder.com/300x200?text=No+Image') }}" class="rounded-img">
                                    <div class="card-body">
                                        <h3 class="text-lg font-semibold text-gray-800">{{ data_get($poll,'title','Unnamed Poll') }}</h3>
                                        <div class="muted">Most Voted: <strong>{{ data_get($poll,'top_choice','—') }}</strong></div>
                                        <div class="muted">Votes: <strong>{{ number_format(data_get($poll,'votes',0)) }}</strong></div>
                                        <div class="progress-wrap" aria-hidden="true">
                                            <div class="progress-bar" data-percent="{{ min(100, (int)round((data_get($poll,'votes',0) / max(1, data_get($poll,'votes',0))) * 100)) }}"></div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="text-center muted py-6">No poll results available.</div>
                @endif
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

                document.querySelectorAll('.progress-bar').forEach(bar => {
                    const percent = Number(bar.dataset.percent || 0);
                    // small visual floor for zero
                    const width = percent > 0 ? percent : 6;
                    setTimeout(() => { bar.style.width = width + '%'; }, 120);
                });
            });
        </script>
    @endpush
@endsection
@extends('layouts.voter')

@section('title', 'Vote | PCU Online Election')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('error') || session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: @json(session('error') ? 'error' : 'success'),
        title: @json(session('error') ? 'Unable to complete your vote' : 'Vote recorded successfully'),
        text: @json(session('error') ?? session('success')),
        confirmButtonColor: '#2563eb'
    });
});
</script>
@endif
@endpush

@section('content')
<div class="page-content vote-overview-page">
    <header class="page-intro">
        <h1>Choose an Election Event to Vote</h1>
        <p>Select the election event you want to participate in and cast your vote.</p>
    </header>

    @php
        $votePositions = $positions ?? collect();
        $voteParties = $parties ?? collect();
        $tileImages = [
            'https://storage.googleapis.com/a1aa/image/lhFfvIk4Y8wBXyF0s6f7lFgIlrmPYRftwJHFFDDFv3naqMNoA.jpg',
            'https://storage.googleapis.com/a1aa/image/Qxcl5e8XJ7ThBC4DmUe30TM93oX1e5Rn453sqkiafWdrUZaQB.jpg',
            'https://storage.googleapis.com/a1aa/image/7ePWeky3U5qsXECimTkVewb9UDfufN1xXmzhEZ5yyTfDTlpBF.jpg',
        ];
    @endphp

    <section class="surface-card">
        <h2>Election Positions</h2>
        <div class="surface-grid vote-overview-grid">
            @forelse($votePositions as $p)
                @php
                    $defaultTileImage = $tileImages[$loop->index % count($tileImages)];
                    $tileImage = $p->tile_img && \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $p->tile_img)
                        ? route('storage.image', ['folder' => 'upload', 'filename' => $p->tile_img])
                        : $defaultTileImage;
                @endphp
                <a href="{{ route('election.details') }}" class="surface-subcard vote-overview-card">
                    <img src="{{ $tileImage }}" alt="{{ $p->name }}" loading="lazy">
                    <h3>{{ $p->name }}</h3>
                    <p>Click to view candidates.</p>
                </a>
            @empty
                <p class="muted-text">No election positions available.</p>
            @endforelse
        </div>
    </section>

    <section class="surface-card">
        <h2>Parties</h2>
        <div class="surface-grid vote-overview-grid">
            @forelse($voteParties as $party)
                @php
                    $partyLogoUrl = $party->logo
                        ? route('storage.image', ['folder' => 'election-parties', 'filename' => basename($party->logo)])
                        : null;
                @endphp
                <a href="{{ route('party.roster', ['party' => $party->id]) }}" class="surface-subcard vote-overview-card">
                    @if($partyLogoUrl)
                        <img src="{{ $partyLogoUrl }}" alt="{{ $party->name }} logo" loading="lazy" onerror="this.onerror=null;this.style.display='none'">
                    @else
                        <div class="vote-overview-placeholder" aria-hidden="true">PCU</div>
                    @endif
                    <h3>{{ $party->name }}</h3>
                    <p>{{ $party->description ?: 'No description available.' }}</p>
                    <span class="muted-text">View party roster</span>
                </a>
            @empty
                <p class="muted-text">No parties available.</p>
            @endforelse
        </div>
    </section>

    <section class="surface-card">
        <h2>Polls</h2>
        <div class="surface-grid poll-card-grid">
            <article class="surface-subcard poll-card">
                <img alt="Favorite Presidential Candidate poll" src="https://storage.googleapis.com/a1aa/image/wJkVcZJ1sxqCM11HKBqB8zuXmgu8eew9UW9XhnxnMZNS6uGUA.jpg" loading="lazy">
                <h3>Favorite Presidential Candidate</h3><p>Vote for your favorite presidential candidate.</p>
            </article>
            <article class="surface-subcard poll-card">
                <img alt="Most Important Election Issue poll" src="https://storage.googleapis.com/a1aa/image/beKzm39DdG1EVqTnlI6O61nZVPu1vTidW2xhJYCFLJRKdXDKA.jpg" loading="lazy">
                <h3>Most Important Election Issue</h3><p>Vote on the most important issue in this election.</p>
            </article>
            <article class="surface-subcard poll-card">
                <img alt="Preferred Voting Method poll" src="https://storage.googleapis.com/a1aa/image/sS9cRez5QAXbdilAZOwC3D4wP6OSfIUARcT3oNeWys4m0dNoA.jpg" loading="lazy">
                <h3>Preferred Voting Method</h3><p>Vote for your preferred method of voting.</p>
            </article>
        </div>
    </section>

    <section class="surface-card">
        <h2>Vote History</h2>
        <ul class="surface-list vote-history-list">
            <li><strong>Presidential Election</strong><span>Election Vote · March 1, 2023</span></li>
            <li><strong>Vice Presidential Election</strong><span>Election Vote · March 2, 2023</span></li>
            <li><strong>Secretary Election</strong><span>Election Vote · March 3, 2023</span></li>
            <li><strong>Campus Facilities Survey</strong><span>Poll Vote · March 4, 2023</span></li>
            <li><strong>Cafeteria Food Quality Survey</strong><span>Poll Vote · March 5, 2023</span></li>
        </ul>
    </section>
</div>
@endsection

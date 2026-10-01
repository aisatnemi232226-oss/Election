@extends('layouts.voter')

@section('title', 'Election Results')

@section('content')
<div class="page-content">
    <header class="page-intro">
        <h1>Election Results</h1>
        <p>View the results of the recent elections.</p>
    </header>

    <section aria-label="Results by position">
        <div class="surface-grid result-position-grid">
            @forelse(($positions ?? collect()) as $position)
                @php
                    $tileRelative = null;
                    if (!empty($position->tile_img)) {
                        $rawTile = $position->tile_img;
                        if (\Illuminate\Support\Str::startsWith($rawTile, 'upload/')) {
                            $rawTile = substr($rawTile, strlen('upload/'));
                        }
                        $tileRelative = \Illuminate\Support\Str::contains($rawTile, '/')
                            ? $rawTile
                            : ('position-tiles/' . $rawTile);
                    }
                    $tileExists = $tileRelative
                        ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $tileRelative)
                        : false;
                    $tileUrl = $tileExists
                        ? route('storage.image', ['folder' => 'upload', 'filename' => $tileRelative])
                        : asset('images/default-banner.png');
                @endphp
                <a href="{{ route('resultTally', ['position' => $position->id]) }}" class="surface-card result-tile">
                    <img src="{{ $tileUrl }}" alt="Results for {{ $position->name }}" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
                    <h2>{{ $position->name }}</h2>
                </a>
            @empty
                <div class="empty-card">No election positions are available.</div>
            @endforelse

            <a href="{{ route('resultTally') }}" class="surface-card result-tile">
                <img src="{{ asset('images/default-banner.png') }}" alt="Results for all positions" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
                <h2>All Positions</h2>
            </a>
        </div>
    </section>

    <section>
        <div class="section-heading"><div><h2>Poll results</h2></div></div>
        <div class="surface-grid poll-card-grid">
            @foreach([
                ['Top President Candidate', 'president', 'https://storage.googleapis.com/a1aa/image/mPeWnsgHfAgSZklazzsc6QqvNiE7X1Vesfe7wxAuCTt1W41gC.jpg'],
                ['Top Vice President Candidate', 'vice_president', 'https://storage.googleapis.com/a1aa/image/0ifxZa28k00Wf0ODn1oBGrdqGJq8eG7PE4BYtp6Zi7gqFeaQB.jpg'],
                ['Top Candidate (All Positions)', 'overall', 'https://storage.googleapis.com/a1aa/image/vmrKfoRjlKyFYqXU6yTQK9KsY9cnWIxMmIfpMKEO0hfkFeaQB.jpg'],
            ] as [$pollTitle, $pollKey, $pollImage])
                <article class="surface-card poll-card">
                    <img src="{{ $pollImage }}" alt="" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
                    <h3>{{ $pollTitle }}</h3>
                    <p>Most Voted: {{ data_get($pollCards, $pollKey . '.name', '—') }}</p>
                    <p>Votes: {{ number_format((int) data_get($pollCards, $pollKey . '.votes', 0)) }}</p>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection

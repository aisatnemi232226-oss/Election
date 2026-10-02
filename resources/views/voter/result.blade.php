@extends('layouts.voter')

@section('title', 'Election Results')
@section('body-class', 'results-view')

@push('styles')
<style>
    body.results-view .results-page {
        width: min(1240px, 100% - 2 * clamp(18px, 5vw, 48px));
        padding-block: clamp(28px, 5vw, 60px) clamp(48px, 7vw, 80px);
    }

    body.results-view .results-page > .page-intro {
        position: relative;
        isolation: isolate;
        display: flex;
        min-height: clamp(220px, 28vw, 330px);
        flex-direction: column;
        justify-content: flex-end;
        overflow: hidden;
        margin: 0 0 clamp(34px, 5vw, 54px);
        padding: clamp(26px, 5vw, 56px);
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 26px;
        background:
            linear-gradient(100deg, rgba(5, 17, 42, .9), rgba(5, 20, 48, .65) 64%, rgba(5, 20, 48, .35)),
            linear-gradient(0deg, rgba(4, 14, 32, .45), transparent 78%),
            url("{{ asset('images/background1.jpg') }}") center 42% / cover no-repeat;
        box-shadow: 0 18px 42px rgba(15, 27, 51, .14);
        color: #f7faff;
        text-align: left;
        animation: results-intro-in .55s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.results-view .results-page > .page-intro::before {
        margin-bottom: 10px;
        color: #c9dcff;
        content: "PCU ONLINE ELECTION";
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .14em;
    }

    body.results-view .results-page > .page-intro h1 {
        color: #fff;
        font-size: clamp(2rem, 5vw, 3.25rem);
        letter-spacing: -.045em;
        text-wrap: balance;
    }

    body.results-view .results-page > .page-intro p {
        max-width: 56ch;
        margin: 12px 0 0;
        color: rgba(239, 245, 255, .88);
        font-size: clamp(1rem, 1.8vw, 1.15rem);
    }

    body.results-view .results-page > section + section {
        margin-top: clamp(38px, 5vw, 58px);
    }

    body.results-view .results-section-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    body.results-view .results-section-heading h2 {
        margin: 0;
        font-size: clamp(1.4rem, 2.5vw, 1.85rem);
        letter-spacing: -.03em;
    }

    body.results-view .results-section-kicker {
        margin: 0 0 5px;
        color: var(--brand);
        font-size: .73rem;
        font-weight: 800;
        letter-spacing: .13em;
    }

    body.results-view .result-position-grid,
    body.results-view .poll-card-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr));
        align-items: stretch;
        gap: clamp(14px, 2vw, 20px);
    }

    body.results-view .result-position-grid > *,
    body.results-view .poll-card-grid > * {
        margin: 0;
    }

    body.results-view .result-tile {
        position: relative;
        isolation: isolate;
        display: flex;
        min-height: clamp(220px, 21vw, 270px);
        flex-direction: column;
        justify-content: flex-end;
        overflow: hidden;
        padding: 0;
        border-color: color-mix(in srgb, var(--line), transparent 8%);
        border-radius: 20px;
        background: var(--tint);
        box-shadow: 0 5px 14px rgba(15, 27, 51, .045), 0 18px 38px rgba(15, 27, 51, .075);
        text-align: left;
        animation: results-card-in .42s cubic-bezier(.2, .75, .25, 1) both;
        transition: transform .22s cubic-bezier(.2, .75, .25, 1), border-color .22s ease, box-shadow .22s ease;
    }

    body.results-view .result-position-grid > :nth-child(2),
    body.results-view .poll-card-grid > :nth-child(2) { animation-delay: 55ms; }
    body.results-view .result-position-grid > :nth-child(3),
    body.results-view .poll-card-grid > :nth-child(3) { animation-delay: 110ms; }
    body.results-view .result-position-grid > :nth-child(4) { animation-delay: 165ms; }

    body.results-view .result-tile::after {
        position: absolute;
        z-index: 1;
        background: linear-gradient(180deg, rgba(5, 17, 42, .02) 25%, rgba(5, 17, 42, .84) 100%);
        content: "";
        inset: 0;
    }

    body.results-view .result-tile img {
        position: absolute;
        z-index: 0;
        width: 100%;
        height: 100%;
        border-radius: 0;
        background: var(--tint);
        object-fit: cover;
        transition: transform .45s cubic-bezier(.2, .75, .25, 1), filter .25s ease;
    }

    body.results-view .result-tile h2 {
        position: relative;
        z-index: 2;
        margin: 0;
        padding: 24px;
        color: #fff;
        font-size: clamp(1.15rem, 2vw, 1.35rem);
        line-height: 1.25;
        text-shadow: 0 2px 12px rgba(0, 0, 0, .28);
        text-wrap: balance;
    }

    body.results-view .result-tile:hover {
        transform: translateY(-5px);
        border-color: color-mix(in srgb, var(--brand), transparent 52%);
        box-shadow: 0 12px 24px rgba(15, 27, 51, .09), 0 24px 46px rgba(37, 99, 235, .13);
    }

    body.results-view .result-tile:hover img {
        transform: scale(1.045);
        filter: saturate(1.08);
    }

    body.results-view .result-tile:focus-visible {
        outline-offset: 5px;
    }

    body.results-view .poll-card {
        position: relative;
        display: flex;
        min-width: 0;
        flex-direction: column;
        overflow: hidden;
        padding: clamp(20px, 3vw, 28px);
        border-color: color-mix(in srgb, var(--line), transparent 8%);
        border-radius: 20px;
        background: linear-gradient(145deg, var(--card), color-mix(in srgb, var(--tint), var(--card) 55%));
        box-shadow: 0 5px 14px rgba(15, 27, 51, .045), 0 18px 38px rgba(15, 27, 51, .075);
        animation: results-card-in .42s cubic-bezier(.2, .75, .25, 1) both;
        transition: transform .22s cubic-bezier(.2, .75, .25, 1), border-color .22s ease, box-shadow .22s ease;
    }

    body.results-view .poll-card:hover {
        transform: translateY(-4px);
        border-color: color-mix(in srgb, var(--brand), transparent 65%);
        box-shadow: 0 10px 20px rgba(15, 27, 51, .06), 0 22px 42px rgba(37, 99, 235, .1);
    }

    body.results-view .poll-card-label {
        display: inline-flex;
        align-self: flex-start;
        align-items: center;
        gap: 7px;
        margin-bottom: 15px;
        color: var(--brand);
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    body.results-view .poll-card-label::before {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--brand), transparent 88%);
        content: "";
    }

    body.results-view .poll-card h3 {
        margin: 0 0 18px;
        font-size: 1.15rem;
        letter-spacing: -.02em;
        line-height: 1.35;
    }

    body.results-view .poll-card-winner {
        min-width: 0;
        margin: auto 0 20px;
        color: var(--ink);
        font-size: clamp(1.12rem, 2vw, 1.35rem);
        font-weight: 800;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    body.results-view .poll-card-count {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        padding-top: 14px;
        border-top: 1px solid var(--line);
        color: var(--mut);
        font-size: .9rem;
    }

    body.results-view .poll-card-count strong {
        color: var(--brand);
        font-size: 1.45rem;
        font-variant-numeric: tabular-nums;
        line-height: 1;
    }

    body.results-view .results-empty {
        grid-column: 1 / -1;
        margin: 0;
        padding: 28px;
        border: 1px dashed var(--line);
        border-radius: 18px;
        background: var(--card);
        color: var(--mut);
        text-align: center;
    }

    @keyframes results-intro-in {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes results-card-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 560px) {
        body.results-view .results-page {
            width: min(100% - 32px, 1240px);
            padding-block: 24px 48px;
        }

        body.results-view .results-page > .page-intro {
            min-height: 220px;
            border-radius: 20px;
            background-position: 56% center;
        }

        body.results-view .result-position-grid,
        body.results-view .poll-card-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        body.results-view .result-tile {
            min-height: 210px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        body.results-view *,
        body.results-view *::before,
        body.results-view *::after {
            scroll-behavior: auto !important;
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }
    }
</style>
@endpush

@section('content')
<div class="page-content results-page">
    <header class="page-intro">
        <h1>Election Results</h1>
        <p>Explore the latest election tallies by position and see which candidates are leading.</p>
    </header>

    <section aria-labelledby="position-results-title">
        <div class="results-section-heading">
            <div>
                <p class="results-section-kicker">EXPLORE THE TALLY</p>
                <h2 id="position-results-title">Results by position</h2>
            </div>
        </div>
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
                    <img src="{{ $tileUrl }}" alt="" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
                    <h2>{{ $position->name }}</h2>
                </a>
            @empty
                <p class="results-empty">No election positions are available.</p>
            @endforelse

            <a href="{{ route('resultTally') }}" class="surface-card result-tile">
                <img src="{{ asset('images/default-banner.png') }}" alt="" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
                <h2>All Positions</h2>
            </a>
        </div>
    </section>

    <section class="results-poll-section" aria-labelledby="poll-results-title">
        <div class="results-section-heading">
            <div>
                <p class="results-section-kicker">AT A GLANCE</p>
                <h2 id="poll-results-title">Poll results</h2>
            </div>
        </div>
        <div class="surface-grid poll-card-grid">
            @foreach([
                ['Top President Candidate', 'president'],
                ['Top Vice President Candidate', 'vice_president'],
                ['Top Candidate (All Positions)', 'overall'],
            ] as [$pollTitle, $pollKey])
                @php
                    $pollName = data_get($pollCards, $pollKey . '.name', '—');
                    $pollVotes = (int) data_get($pollCards, $pollKey . '.votes', 0);
                @endphp
                <article class="surface-card poll-card">
                    <span class="poll-card-label">Leading candidate</span>
                    <h3>{{ $pollTitle }}</h3>
                    <p class="poll-card-winner">{{ $pollName }}</p>
                    <div class="poll-card-count">
                        <span>Votes received</span>
                        <strong>{{ number_format($pollVotes) }}</strong>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection

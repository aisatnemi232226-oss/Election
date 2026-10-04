@extends('layouts.voter')

@section('title', 'Election Results')
@section('body-class', 'results-view')

@push('styles')
<style>
    body.results-view #main-content {
        --results-bg-start: #eaf2ff;
        --results-bg-mid: #d7e6ff;
        --results-bg-end: #f0f6ff;
        position: relative;
        isolation: isolate;
        overflow: hidden;
        background:
            radial-gradient(ellipse at 8% 12%, rgba(56, 111, 229, .2), transparent 38%),
            radial-gradient(ellipse at 100% 46%, rgba(83, 145, 255, .17), transparent 42%),
            linear-gradient(135deg, var(--results-bg-start), var(--results-bg-mid) 48%, var(--results-bg-end));
    }

    body.results-view #main-content::before,
    body.results-view #main-content::after {
        position: absolute;
        z-index: 0;
        pointer-events: none;
        content: "";
    }

    body.results-view #main-content::before {
        inset: 0;
        background-image: radial-gradient(rgba(40, 87, 158, .16) .8px, transparent .8px);
        background-size: 26px 26px;
        opacity: .34;
        mask-image: linear-gradient(125deg, #000, transparent 68%);
    }

    body.results-view #main-content::after {
        top: 330px;
        right: -210px;
        width: min(62vw, 760px);
        aspect-ratio: 1;
        border: 1px solid rgba(255, 255, 255, .52);
        border-radius: 50%;
        box-shadow: 0 0 0 42px rgba(255, 255, 255, .16), 0 0 0 84px rgba(255, 255, 255, .1);
        animation: results-background-orbit 32s ease-in-out infinite alternate;
    }

    body.results-view .results-page {
        position: relative;
        z-index: 1;
    }

    :root[data-theme="dark"] body.results-view #main-content {
        --results-bg-start: #17396b;
        --results-bg-mid: #24508c;
        --results-bg-end: #162f58;
    }

    :root[data-theme="dark"] body.results-view #main-content::before {
        background-image: radial-gradient(rgba(215, 230, 255, .18) .8px, transparent .8px);
        opacity: .23;
    }

    :root[data-theme="dark"] body.results-view #main-content::after {
        border-color: rgba(220, 234, 255, .2);
        box-shadow: 0 0 0 42px rgba(220, 234, 255, .055), 0 0 0 84px rgba(220, 234, 255, .035);
    }

    @keyframes results-background-orbit {
        from { transform: translate3d(0, 0, 0); }
        to { transform: translate3d(-18px, 14px, 0); }
    }

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

    body.results-view .results-unpublished {
        position: relative;
        isolation: isolate;
        display: grid;
        grid-template-columns: minmax(150px, .7fr) minmax(0, 1.6fr);
        align-items: center;
        gap: clamp(24px, 5vw, 64px);
        overflow: hidden;
        margin: 0 auto;
        padding: clamp(26px, 5vw, 56px);
        border: 1px solid color-mix(in srgb, var(--brand), var(--line) 72%);
        border-radius: 28px;
        background:
            radial-gradient(ellipse at 12% 50%, color-mix(in srgb, var(--brand), transparent 90%), transparent 42%),
            linear-gradient(135deg, var(--card), color-mix(in srgb, var(--tint), var(--card) 42%));
        box-shadow: 0 8px 22px rgba(15, 27, 51, .055), 0 24px 58px rgba(37, 99, 235, .09);
        animation: results-card-in .48s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.results-view .results-unpublished::before,
    body.results-view .results-unpublished::after {
        position: absolute;
        z-index: -1;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 90%);
        border-radius: 50%;
        content: "";
        pointer-events: none;
    }

    body.results-view .results-unpublished::before {
        top: -150px;
        right: -70px;
        width: 330px;
        height: 330px;
        box-shadow: 0 0 0 28px color-mix(in srgb, var(--brand), transparent 96%), 0 0 0 58px color-mix(in srgb, var(--brand), transparent 97%);
    }

    body.results-view .results-unpublished::after {
        right: 15%;
        bottom: -235px;
        width: 290px;
        height: 290px;
        border-color: color-mix(in srgb, var(--brand), transparent 94%);
    }

    body.results-view .results-unpublished-art {
        position: relative;
        display: grid;
        width: min(100%, 220px);
        aspect-ratio: 1;
        place-items: center;
        justify-self: center;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 83%);
        border-radius: 50%;
        background:
            radial-gradient(circle, color-mix(in srgb, var(--brand), var(--card) 92%) 0 43%, transparent 44%),
            repeating-conic-gradient(from -8deg, color-mix(in srgb, var(--brand), transparent 92%) 0deg 1deg, transparent 1deg 15deg);
        box-shadow: inset 0 0 0 14px color-mix(in srgb, var(--brand), transparent 97%);
    }

    body.results-view .results-unpublished-art::before,
    body.results-view .results-unpublished-art::after {
        position: absolute;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 80%);
        border-radius: 50%;
        content: "";
    }

    body.results-view .results-unpublished-art::before {
        inset: 12%;
    }

    body.results-view .results-unpublished-art::after {
        inset: 22%;
        border-style: dashed;
        opacity: .7;
    }

    body.results-view .results-unpublished-lock {
        z-index: 1;
        display: grid;
        width: 86px;
        height: 86px;
        place-items: center;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 72%);
        border-radius: 26px;
        background: var(--card);
        color: var(--brand);
        box-shadow: 0 12px 28px rgba(15, 27, 51, .12), inset 0 1px rgba(255, 255, 255, .8);
        transform: rotate(-7deg);
    }

    body.results-view .results-unpublished-lock .icon {
        width: 40px;
        height: 40px;
        stroke-width: 1.7;
    }

    body.results-view .results-unpublished-copy {
        min-width: 0;
    }

    body.results-view .results-unpublished-kicker {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin: 0 0 16px;
        color: var(--brand);
        font-size: .72rem;
        font-weight: 850;
        letter-spacing: .14em;
    }

    body.results-view .results-unpublished-kicker::before {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: currentColor;
        box-shadow: 0 0 0 5px color-mix(in srgb, var(--brand), transparent 89%);
        content: "";
    }

    body.results-view .results-unpublished-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-left: 4px;
        padding: 6px 10px;
        border: 1px solid color-mix(in srgb, var(--wait), transparent 72%);
        border-radius: 999px;
        background: color-mix(in srgb, var(--wait-bg), var(--card) 38%);
        color: var(--wait);
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .06em;
        line-height: 1;
        vertical-align: middle;
    }

    body.results-view .results-unpublished-copy h2 {
        max-width: 18ch;
        margin: 0;
        font-size: clamp(1.7rem, 3.5vw, 2.55rem);
        letter-spacing: -.045em;
        line-height: 1.12;
        text-wrap: balance;
    }

    body.results-view .results-unpublished-description {
        max-width: 58ch;
        margin: 14px 0 0;
        color: var(--mut);
        font-size: clamp(.96rem, 1.5vw, 1.05rem);
        line-height: 1.7;
    }

    body.results-view .results-unpublished-note {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        max-width: 58ch;
        margin-top: 22px;
        padding: 13px 15px;
        border: 1px solid color-mix(in srgb, var(--line), transparent 12%);
        border-radius: 14px;
        background: color-mix(in srgb, var(--card), transparent 18%);
        color: var(--mut);
        font-size: .85rem;
        line-height: 1.5;
    }

    body.results-view .results-unpublished-note .icon {
        width: 18px;
        height: 18px;
        flex: none;
        margin-top: 1px;
        color: var(--brand);
    }

    body.results-view .results-unpublished-footer {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px 20px;
        margin-top: 24px;
    }

    body.results-view .results-unpublished-update {
        color: var(--mut);
        font-size: .82rem;
        font-weight: 650;
    }

    body.results-view .results-unpublished-link {
        display: inline-flex;
        min-height: 44px;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 0 17px;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 72%);
        border-radius: 12px;
        background: var(--card);
        color: var(--brand);
        font-size: .88rem;
        font-weight: 800;
        box-shadow: 0 4px 12px rgba(15, 27, 51, .055);
        transition: transform .18s ease, background-color .18s ease, color .18s ease, box-shadow .18s ease;
    }

    body.results-view .results-unpublished-link:hover {
        transform: translateY(-2px);
        background: var(--brand);
        color: var(--brand-contrast);
        box-shadow: 0 8px 18px color-mix(in srgb, var(--brand), transparent 78%);
    }

    body.results-view .results-unpublished-link:focus-visible {
        outline-offset: 4px;
    }

    body.results-view .results-unpublished-link .icon {
        width: 17px;
        height: 17px;
    }

    :root[data-theme="dark"] body.results-view .results-unpublished {
        background:
            radial-gradient(ellipse at 12% 50%, color-mix(in srgb, var(--brand), transparent 88%), transparent 42%),
            linear-gradient(135deg, var(--card), color-mix(in srgb, var(--tint), var(--card) 28%));
        box-shadow: 0 12px 30px rgba(0, 0, 0, .2), 0 26px 60px color-mix(in srgb, var(--brand), transparent 90%);
    }

    :root[data-theme="dark"] body.results-view .results-unpublished-lock {
        box-shadow: 0 12px 28px rgba(0, 0, 0, .3);
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

        body.results-view .results-unpublished {
            grid-template-columns: minmax(0, 1fr);
            gap: 24px;
            padding: 26px 22px;
            border-radius: 22px;
        }

        body.results-view .results-unpublished-art {
            width: 158px;
        }

        body.results-view .results-unpublished-lock {
            width: 68px;
            height: 68px;
            border-radius: 21px;
        }

        body.results-view .results-unpublished-lock .icon {
            width: 32px;
            height: 32px;
        }

        body.results-view .results-unpublished-kicker {
            flex-wrap: wrap;
            font-size: .66rem;
        }

        body.results-view .results-unpublished-copy h2 {
            font-size: clamp(1.65rem, 8vw, 2rem);
        }

        body.results-view .results-unpublished-footer {
            align-items: stretch;
            flex-direction: column;
        }

        body.results-view .results-unpublished-link {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        body.results-view #main-content::after {
            animation: none !important;
        }

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
        <p>{{ $election ? 'Results for ' . $election->name . '.' : 'Explore the latest election tallies by position and see which candidates are leading.' }}</p>
    </header>

    @if(!($resultsVisible ?? false))
        <section class="results-unpublished" role="status" aria-labelledby="results-unpublished-title">
            <div class="results-unpublished-art" aria-hidden="true">
                <span class="results-unpublished-lock">
                    <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="10" width="16" height="11" rx="2"></rect>
                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                        <path d="M12 14v3"></path>
                    </svg>
                </span>
            </div>
            <div class="results-unpublished-copy">
                <p class="results-unpublished-kicker">
                    OFFICIAL RESULTS
                    <span class="results-unpublished-status">NOT PUBLISHED</span>
                </p>
                <h2 id="results-unpublished-title">Official results are not available yet.</h2>
                <p class="results-unpublished-description">
                    @if($election)
                        Candidate vote totals for <strong>{{ $election->name }}</strong> are being kept private until they are officially released.
                    @else
                        Candidate vote totals are kept private until they are officially released.
                    @endif
                </p>
                <div class="results-unpublished-note">
                    <svg class="icon" aria-hidden="true"><use href="#pcu-chart"/></svg>
                    <span>Once published, official totals and position-by-position tallies will appear here.</span>
                </div>
                <div class="results-unpublished-footer">
                    <span class="results-unpublished-update">Check back here when the election team announces publication.</span>
                    <a class="results-unpublished-link" href="{{ route('election.details', $election ? ['election_id' => $election->id] : []) }}">
                        View election details
                        <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
                    </a>
                </div>
            </div>
        </section>
    @else
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
                <a href="{{ route('resultTally', ['position' => $position->id, 'election_id' => $election->id]) }}" class="surface-card result-tile">
                    <img src="{{ $tileUrl }}" alt="" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
                    <h2>{{ $position->name }}</h2>
                </a>
            @empty
                <p class="results-empty">No election positions are available.</p>
            @endforelse

            <a href="{{ route('resultTally', ['election_id' => $election->id]) }}" class="surface-card result-tile">
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
    @endif
</div>
@endsection

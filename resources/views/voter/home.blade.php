@extends('layouts.voter')

@section('title', 'PCU Online Election System')
@section('body-class', 'voter-home-page')

@push('styles')
<style>
    body.voter-home-page .hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        background-color: #07162f;
        background:
            linear-gradient(90deg, rgba(5, 17, 42, .88) 0%, rgba(5, 20, 48, .72) 48%, rgba(5, 20, 48, .46) 100%),
            linear-gradient(0deg, rgba(4, 14, 32, .62) 0%, rgba(4, 14, 32, .08) 58%, rgba(4, 14, 32, .24) 100%),
            url("{{ asset('images/background1.jpg') }}") center 48% / cover no-repeat;
        color: #f7faff;
    }

    body.voter-home-page .hero::before,
    body.voter-home-page .hero::after {
        position: absolute;
        z-index: -1;
        inset: 0;
        pointer-events: none;
        content: "";
    }

    body.voter-home-page .hero::before {
        background: radial-gradient(circle, rgba(255, 255, 255, .5) 1px, transparent 1.5px);
        background-size: 34px 34px;
        opacity: .12;
        mask-image: linear-gradient(110deg, #000, transparent 75%);
    }

    body.voter-home-page .hero::after {
        inset: auto -9rem -17rem auto;
        width: 34rem;
        aspect-ratio: 1;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 50%;
        box-shadow: 0 0 0 3rem rgba(255, 255, 255, .025), 0 0 0 6rem rgba(255, 255, 255, .02);
    }

    body.voter-home-page .hero-inner {
        position: relative;
        z-index: 0;
        width: min(1180px, 100% - 2 * clamp(20px, 5vw, 44px));
        gap: clamp(32px, 6vw, 76px);
        padding-block: clamp(48px, 8vw, 96px);
    }

    body.voter-home-page .hero-copy {
        animation: home-intro .7s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .hero-status {
        border: 1px solid rgba(255, 255, 255, .24);
        background: rgba(255, 255, 255, .1);
        color: #e3edff;
        backdrop-filter: blur(8px);
    }

    body.voter-home-page .hero-status.status-open {
        background: rgba(34, 197, 94, .16);
        color: #bbf7d0;
    }

    body.voter-home-page .hero-status.status-ended {
        background: rgba(255, 255, 255, .12);
        color: #e2e8f0;
    }

    body.voter-home-page .hero h1 {
        max-width: 13ch;
        margin-top: 22px;
        color: #fff;
        font-size: clamp(2.75rem, 6.5vw, 5.35rem);
        line-height: .99;
        letter-spacing: -.055em;
        text-wrap: balance;
    }

    body.voter-home-page .hero-lead {
        max-width: 43ch;
        margin-top: 20px;
        color: rgba(235, 242, 255, .78);
        font-size: clamp(1.05rem, 1.7vw, 1.22rem);
        line-height: 1.7;
    }

    body.voter-home-page .button-row {
        gap: 14px;
        margin-top: 30px;
    }

    body.voter-home-page .button {
        min-height: 54px;
        border-radius: 15px;
        transition: transform .22s ease, box-shadow .22s ease, background-color .22s ease;
    }

    body.voter-home-page .button-primary {
        border-color: #78a7ff;
        background: linear-gradient(135deg, #4c83f1, #2860dc);
        box-shadow: 0 10px 28px rgba(25, 82, 197, .38), inset 0 1px rgba(255, 255, 255, .22);
        color: #fff;
    }

    body.voter-home-page .button-primary:hover {
        box-shadow: 0 15px 34px rgba(25, 82, 197, .48), inset 0 1px rgba(255, 255, 255, .28);
    }

    body.voter-home-page .button-secondary {
        border-color: rgba(255, 255, 255, .32);
        background: rgba(255, 255, 255, .08);
        color: #f7faff;
    }

    body.voter-home-page .button-secondary:hover {
        background: rgba(255, 255, 255, .15);
    }

    body.voter-home-page .steps-card {
        --bg: #f5f8fd;
        --card: rgba(255, 255, 255, .97);
        --ink: #0f1b33;
        --mut: #4a5873;
        --line: #dfe6f1;
        --brand: #2563eb;
        --brand-contrast: #fff;
        --tint: #eaf1ff;
        --ok: #15803d;
        --ok-bg: #dcfce7;
        color: var(--ink);
        padding: clamp(20px, 3vw, 30px);
        border-color: rgba(255, 255, 255, .72);
        border-radius: 26px;
        box-shadow: 0 28px 72px rgba(1, 12, 34, .34), 0 2px 8px rgba(1, 12, 34, .08);
        animation: home-intro .8s .12s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .steps-card h2 {
        font-size: 1.3rem;
        letter-spacing: -.025em;
    }

    body.voter-home-page .steps-list {
        gap: 11px;
    }

    body.voter-home-page .step-item {
        min-height: 72px;
        padding: 13px 15px;
        border-radius: 16px;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    body.voter-home-page .step-item:hover {
        transform: translateX(3px);
        box-shadow: 0 8px 18px rgba(15, 27, 51, .07);
    }

    body.voter-home-page .step-item.is-current {
        box-shadow: inset 3px 0 #2563eb, 0 8px 20px rgba(37, 99, 235, .09);
    }

    body.voter-home-page .content-section {
        position: relative;
        padding-block: clamp(42px, 6vw, 72px);
        border-bottom: 1px solid color-mix(in srgb, var(--line), transparent 25%);
    }

    body.voter-home-page #how-it-works,
    body.voter-home-page #events,
    body.voter-home-page #guidelines {
        background:
            radial-gradient(ellipse at 100% 0%, color-mix(in srgb, var(--tint), transparent 32%), transparent 50%),
            var(--tint);
    }

    body.voter-home-page .section-heading {
        align-items: flex-end;
        margin-bottom: 24px;
    }

    body.voter-home-page .section-heading h2 {
        letter-spacing: -.035em;
        text-wrap: balance;
    }

    body.voter-home-page .section-heading p {
        max-width: 58ch;
    }

    body.voter-home-page .action-card,
    body.voter-home-page .date-card,
    body.voter-home-page .election-banner,
    body.voter-home-page .event-card,
    body.voter-home-page .article-card,
    body.voter-home-page .faq-item,
    body.voter-home-page .guidelines-card,
    body.voter-home-page .empty-card {
        border-color: color-mix(in srgb, var(--line), transparent 12%);
        border-radius: 20px;
        box-shadow: 0 3px 8px rgba(15, 27, 51, .035), 0 16px 36px rgba(15, 27, 51, .065);
    }

    body.voter-home-page .action-card {
        position: relative;
        min-height: 248px;
        overflow: hidden;
        padding: clamp(22px, 3vw, 30px);
        transition: transform .24s cubic-bezier(.2, .75, .25, 1), border-color .24s ease, box-shadow .24s ease;
    }

    body.voter-home-page .action-card::after {
        position: absolute;
        right: -38px;
        bottom: -66px;
        width: 145px;
        aspect-ratio: 1;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 86%);
        border-radius: 50%;
        box-shadow: 0 0 0 18px color-mix(in srgb, var(--brand), transparent 96%);
        content: "";
        pointer-events: none;
        transition: transform .35s ease;
    }

    body.voter-home-page .action-card:hover {
        transform: translateY(-6px);
        border-color: color-mix(in srgb, var(--brand), transparent 40%);
        box-shadow: 0 9px 18px rgba(15, 27, 51, .06), 0 24px 48px rgba(37, 99, 235, .13);
    }

    body.voter-home-page .action-card:hover::after {
        transform: scale(1.12);
    }

    body.voter-home-page .action-icon {
        width: 56px;
        height: 56px;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 86%);
        border-radius: 17px;
        background: linear-gradient(145deg, var(--tint), color-mix(in srgb, var(--card), var(--tint) 55%));
    }

    body.voter-home-page .action-card h3 {
        letter-spacing: -.02em;
    }

    body.voter-home-page .action-link {
        transition: gap .2s ease;
    }

    body.voter-home-page .action-card:hover .action-link {
        gap: 10px;
    }

    body.voter-home-page .date-card {
        min-height: 128px;
        justify-content: center;
        padding: 20px;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    body.voter-home-page .date-card:hover {
        transform: translateY(-3px);
        border-color: color-mix(in srgb, var(--brand), transparent 55%);
        box-shadow: var(--sh);
    }

    body.voter-home-page .date-card .icon {
        width: 44px;
        height: 44px;
        padding: 10px;
        border-radius: 14px;
        background: var(--tint);
    }

    body.voter-home-page .election-banner {
        background: linear-gradient(120deg, var(--card), color-mix(in srgb, var(--tint), var(--card) 64%));
    }

    body.voter-home-page .event-card,
    body.voter-home-page .article-card {
        transition: transform .24s cubic-bezier(.2, .75, .25, 1), border-color .24s ease, box-shadow .24s ease;
    }

    body.voter-home-page .event-card:hover,
    body.voter-home-page .article-card:hover {
        transform: translateY(-5px);
        border-color: color-mix(in srgb, var(--brand), transparent 45%);
        box-shadow: 0 18px 42px rgba(15, 27, 51, .13);
    }

    body.voter-home-page .event-image,
    body.voter-home-page .article-image {
        transition: transform .45s cubic-bezier(.2, .75, .25, 1);
    }

    body.voter-home-page .event-card:hover .event-image,
    body.voter-home-page .article-card:hover .article-image {
        transform: scale(1.035);
    }

    body.voter-home-page .faq-item {
        overflow: hidden;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    body.voter-home-page .faq-item[open] {
        border-color: color-mix(in srgb, var(--brand), transparent 62%);
        box-shadow: var(--sh);
    }

    body.voter-home-page .faq-item summary {
        transition: color .18s ease;
    }

    body.voter-home-page .faq-item summary:hover {
        color: var(--brand);
    }

    @keyframes home-intro {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes home-reveal {
        from { opacity: 0; transform: translateY(22px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @supports (animation-timeline: view()) and (animation-range: entry 0% cover 25%) {
        @media (prefers-reduced-motion: no-preference) {
            body.voter-home-page .action-card,
            body.voter-home-page .date-card,
            body.voter-home-page .election-banner,
            body.voter-home-page .event-card,
            body.voter-home-page .article-card,
            body.voter-home-page .faq-item,
            body.voter-home-page .guidelines-card,
            body.voter-home-page .empty-card {
                animation: home-reveal auto linear both;
                animation-timeline: view();
                animation-range: entry 0% cover 25%;
            }
        }
    }

    @media (max-width: 639px) {
        body.voter-home-page .hero-inner {
            gap: 30px;
            padding-block: 44px 50px;
        }

        body.voter-home-page .hero h1 {
            max-width: 11ch;
            font-size: clamp(2.65rem, 12vw, 4rem);
        }

        body.voter-home-page .hero-lead {
            max-width: 38ch;
            margin-top: 16px;
        }

        body.voter-home-page .button-row {
            margin-top: 24px;
        }

        body.voter-home-page .button {
            min-height: 50px;
            padding-inline: 16px;
        }

        body.voter-home-page .steps-card {
            border-radius: 22px;
        }

        body.voter-home-page .section-heading {
            align-items: flex-start;
        }

        body.voter-home-page .action-card {
            min-height: 220px;
        }

        body.voter-home-page .date-card {
            min-height: 104px;
            flex-direction: row;
            align-items: center;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        body.voter-home-page .hero-copy,
        body.voter-home-page .steps-card,
        body.voter-home-page .action-card,
        body.voter-home-page .date-card,
        body.voter-home-page .election-banner,
        body.voter-home-page .event-card,
        body.voter-home-page .article-card,
        body.voter-home-page .faq-item,
        body.voter-home-page .guidelines-card,
        body.voter-home-page .empty-card {
            animation: none !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $homeUser = Auth::user();
    $homeIsAdmin = $homeUser && $homeUser->is_admin;
    $homeIsVerified = $homeUser && !$homeIsAdmin && $homeUser->isVerifiedVoter();
    $homeVerificationPending = $homeUser && $homeUser->voter_verification_submitted_at !== null;
    $homeElection = $elections->first();
    $eventsLabel = $eventSettings?->label ?: 'Election events';
    $labelImageRelative = $eventSettings?->label_image;
    $labelImageExists = $labelImageRelative
        ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $labelImageRelative)
        : false;
    $labelImageUrl = $labelImageExists
        ? route('storage.image', ['folder' => 'upload', 'filename' => $labelImageRelative])
        : null;

    $events = [];
    for ($i = 1; $i <= 3; $i++) {
        $title = $eventSettings?->{"event_{$i}_title"};
        $date = $eventSettings?->{"event_{$i}_date"};
        $description = $eventSettings?->{"event_{$i}_description"};
        $relativeImage = $eventSettings?->{"event_{$i}_image"};
        $hasImage = $relativeImage
            ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $relativeImage)
            : false;
        $image = $hasImage
            ? route('storage.image', ['folder' => 'upload', 'filename' => $relativeImage])
            : null;

        if ($title || $date || $description || $image) {
            $events[] = compact('title', 'date', 'description', 'image');
        }
    }

    $eventStatus = $eventSettings?->status;
    $eventStatusLabel = match ($eventStatus) {
        'ongoing' => 'Election ongoing',
        'upcoming' => 'Election upcoming',
        'completed' => 'Election completed',
        default => 'Election information',
    };
    $eventStatusClass = match ($eventStatus) {
        'ongoing' => 'status-open',
        'completed' => 'status-ended',
        default => '',
    };

    $formatElectionDate = static fn ($date) => $date
        ? \Illuminate\Support\Carbon::parse($date)->format('F j, Y')
        : null;
    $dateRows = [];
    if ($homeElection) {
        $registrationStart = $formatElectionDate($homeElection->reg_start);
        $registrationEnd = $formatElectionDate($homeElection->reg_end);
        $campaignStart = $formatElectionDate($homeElection->campaign_start);
        $campaignEnd = $formatElectionDate($homeElection->campaign_end);
        $votingStart = $formatElectionDate($homeElection->start_date);
        $votingEnd = $formatElectionDate($homeElection->end_date);
        $resultsDate = $formatElectionDate($homeElection->result_date);

        if ($registrationStart || $registrationEnd) {
            $dateRows[] = ['Registration period', implode(' – ', array_filter([$registrationStart, $registrationEnd])) , 'pcu-grad'];
        }
        if ($campaignStart || $campaignEnd) {
            $dateRows[] = ['Campaign period', implode(' – ', array_filter([$campaignStart, $campaignEnd])) , 'pcu-cal'];
        }
        if ($votingStart || $votingEnd) {
            $dateRows[] = ['Voting period', implode(' – ', array_filter([$votingStart, $votingEnd])) , 'pcu-vote'];
        }
        if ($resultsDate) {
            $dateRows[] = ['Results announcement', $resultsDate, 'pcu-chart'];
        }
    }

    $currentStep = !Auth::check() ? 1 : ($homeIsVerified || $homeIsAdmin ? 3 : 2);
    $stepDescription = !Auth::check()
        ? 'Log in with your student account to get started.'
        : ($homeIsVerified ? 'Your account is verified. You can continue to the ballot.' : ($homeIsAdmin ? 'Review the election and its results.' : 'Verify your account before casting a vote.'));
    $heroLead = !Auth::check()
        ? 'Sign in with your student account to follow the election and cast your vote.'
        : ($homeIsVerified ? 'Your account is verified. Choose your candidates and submit your ballot.' : ($homeIsAdmin ? 'Review current election information and student voting activity.' : 'Your account needs to be verified before you can vote.'));

    $articleQuery = \App\Models\ElectionArticle::query()->with(['articles']);
    if (\Illuminate\Support\Facades\Schema::hasColumn('election_articles', 'show_status')) {
        $articleQuery->where('show_status', 1);
    }
    $latestArticles = $articleQuery
        ->orderByDesc('created_at')
        ->get()
        ->map(fn ($electionArticle) => $electionArticle->articles)
        ->filter();
@endphp

<section class="hero">
    <div class="page-wrap hero-inner">
        <div class="hero-copy">
            <span class="hero-status {{ $eventStatusClass }}">{{ $eventStatusLabel }}</span>
            <h1>Vote for your student leaders</h1>
            <p class="hero-lead">{{ $heroLead }}</p>
            <div class="button-row">
                @guest
                    <a class="button button-primary" href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}">
                        Log in to vote <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
                    </a>
                @else
                    @if($homeIsVerified || $homeIsAdmin)
                        <a class="button button-primary" href="{{ route('election.details') }}">
                            Vote now <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
                        </a>
                    @else
                        <a class="button button-primary" href="{{ route('register.verify') }}">
                            {{ $homeVerificationPending ? 'View application' : 'Verify my account' }}
                            <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
                        </a>
                    @endif
                @endguest
                <a class="button button-secondary" href="#how-it-works">How it works</a>
            </div>
        </div>

        <aside class="steps-card" aria-labelledby="steps-title">
            <h2 id="steps-title">Your steps to vote</h2>
            <p>{{ $stepDescription }}</p>
            <ol class="steps-list">
                @php
                    $steps = [
                        ['Log in', 'Use your student account'],
                        ['Get verified', 'Submit your student details'],
                        ['Cast your vote', 'Choose your candidates'],
                        ['See the results', 'Follow the published results'],
                    ];
                @endphp
                @foreach($steps as $index => [$stepTitle, $stepHint])
                    @php
                        $stepNumber = $index + 1;
                        $stepState = $stepNumber < $currentStep ? 'is-done' : ($stepNumber === $currentStep ? 'is-current' : '');
                    @endphp
                    <li class="step-item {{ $stepState }}">
                        <span class="step-number">
                            @if($stepState === 'is-done')
                                <svg class="icon" aria-hidden="true"><use href="#pcu-ok"/></svg>
                            @else
                                {{ $stepNumber }}
                            @endif
                        </span>
                        <span class="step-copy"><strong>{{ $stepTitle }}</strong><span>{{ $stepHint }}</span></span>
                        @if($stepState)
                            <span class="step-tag">{{ $stepState === 'is-done' ? 'Done' : 'Next step' }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </aside>
    </div>
</section>

<section class="content-section" id="how-it-works">
    <div class="page-wrap">
        <div class="section-heading">
            <div><h2>What would you like to do?</h2><p>Pick a next step to continue.</p></div>
        </div>
        <div class="action-grid">
            <a class="action-card" href="{{ route('register.options') }}">
                <span class="action-icon"><svg class="icon" aria-hidden="true"><use href="#pcu-grad"/></svg></span>
                <h3>Register</h3>
                <p>Choose voter or candidate registration.</p>
                <span class="action-link">Start registration <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg></span>
            </a>
            <a class="action-card" href="{{ route('election.details') }}">
                <span class="action-icon"><svg class="icon" aria-hidden="true"><use href="#pcu-vote"/></svg></span>
                <h3>Vote</h3>
                <p>Choose your candidates and submit your ballot.</p>
                <span class="action-link">Open election <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg></span>
            </a>
            <a class="action-card" href="{{ url('/result') }}">
                <span class="action-icon"><svg class="icon" aria-hidden="true"><use href="#pcu-chart"/></svg></span>
                <h3>Results</h3>
                <p>Review election results when they are published.</p>
                <span class="action-link">View results <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg></span>
            </a>
        </div>
    </div>
</section>

<section class="content-section" id="dates">
    <div class="page-wrap">
        <div class="section-heading">
            <div>
                <h2>Key dates</h2>
                @if($homeElection)
                    <p>{{ $homeElection->name }}</p>
                @else
                    <p>Election schedule</p>
                @endif
            </div>
        </div>
        @if(count($dateRows))
            <div class="date-grid">
                @foreach($dateRows as [$dateTitle, $dateValue, $dateIcon])
                    <article class="date-card">
                        <svg class="icon" aria-hidden="true"><use href="#{{ $dateIcon }}"/></svg>
                        <div><strong>{{ $dateTitle }}</strong><span>{{ $dateValue }}</span></div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-card">Election dates have not been published yet.</div>
        @endif
    </div>
</section>

<section class="content-section" id="events">
    <div class="page-wrap">
        <div class="section-heading">
            <div><h2>News and events</h2><p>{{ $eventsLabel }}</p></div>
            @if(count($events) > 1)
                <div class="carousel-controls" id="event-carousel-controls" aria-label="Election events carousel controls">
                    <button class="icon-button" id="prev-btn" type="button" aria-label="Previous election event"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
                    <button class="icon-button" id="next-btn" type="button" aria-label="Next election event"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
                </div>
            @endif
        </div>

        @if($labelImageUrl || $eventsLabel || $eventSettings?->start_date || $eventSettings?->end_date)
            <div class="election-banner">
                @if($labelImageUrl)
                    <img src="{{ $labelImageUrl }}" alt="{{ $eventsLabel }}">
                @endif
                <div class="election-banner-copy">
                    <span>Election details</span>
                    <strong>{{ $eventsLabel }}</strong>
                    @if($eventSettings?->start_date || $eventSettings?->end_date)
                        <p>
                            {{ $eventSettings?->start_date ? \Illuminate\Support\Carbon::parse($eventSettings->start_date)->format('F j, Y') : '' }}
                            @if($eventSettings?->start_date && $eventSettings?->end_date) – @endif
                            {{ $eventSettings?->end_date ? \Illuminate\Support\Carbon::parse($eventSettings->end_date)->format('F j, Y') : '' }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        @if(count($events))
            <div class="event-grid" id="events-container">
                @foreach($events as $index => $event)
                    <button type="button" class="event-card" data-event-card data-event-title="{{ $event['title'] }}" data-event-date="{{ $event['date'] }}" data-event-image="{{ $event['image'] }}" data-event-description="{{ $event['description'] }}">
                        @if($event['image'])
                            <img class="event-image" src="{{ $event['image'] }}" alt="{{ $event['title'] }}">
                        @else
                            <span class="event-placeholder" aria-hidden="true"><svg class="icon"><use href="#pcu-cal"/></svg></span>
                        @endif
                        <span class="event-card-copy">
                            @if($event['title'])<span class="event-title">{{ $event['title'] }}</span>@endif
                            @if($event['date'])<time>{{ $event['date'] }}</time>@endif
                            @if($event['description'])<span class="event-description">{{ $event['description'] }}</span>@endif
                        </span>
                    </button>
                @endforeach
            </div>
        @else
            <div class="empty-card">Election announcements and event details will appear here when published.</div>
        @endif
    </div>
</section>

<section class="content-section" id="articles">
    <div class="page-wrap">
        <div class="section-heading">
            <div><h2>Latest articles</h2><p>Election updates and announcements.</p></div>
            @if($latestArticles->count() > 1)
                <div class="carousel-controls" id="article-carousel-controls" aria-label="Article carousel controls">
                    <button class="icon-button" id="latest-articles-prev" type="button" aria-label="Previous article"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
                    <button class="icon-button" id="latest-articles-next" type="button" aria-label="Next article"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
                </div>
            @endif
        </div>
        @if($latestArticles->isEmpty())
            <div class="empty-card">No articles found.</div>
        @else
            <div class="article-grid" id="latest-articles-container">
                @foreach($latestArticles as $article)
                    <article class="article-card">
                        <button type="button" class="article-open" data-article-card data-article-id="{{ $article->id }}">
                            <img class="article-image" src="{{ $article->featured_image_url ?? asset('images/logo.png') }}" alt="{{ $article->title }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}'">
                            <span class="article-copy">
                                <span class="article-title">{{ $article->title }}</span>
                                <time>{{ optional($article->created_at)->format('F j, Y') }}</time>
                                <span class="article-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($article->content), 160) }}</span>
                                <span class="article-read-more">Read more <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg></span>
                            </span>
                        </button>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="content-section" id="faq">
    <div class="page-wrap">
        <div class="section-heading"><div><h2>Common questions</h2></div></div>
        <details class="faq-item">
            <summary>Who can vote?<svg class="icon" aria-hidden="true"><use href="#pcu-chev"/></svg></summary>
            <p class="faq-answer">Eligible, verified students can access the ballot using their student account.</p>
        </details>
        <details class="faq-item">
            <summary>How do I get verified?<svg class="icon" aria-hidden="true"><use href="#pcu-chev"/></svg></summary>
            <p class="faq-answer">Open your account menu, choose the verification option, and submit your student details. You can return to view an application that is awaiting review.</p>
        </details>
        <details class="faq-item">
            <summary>Can I change my vote?<svg class="icon" aria-hidden="true"><use href="#pcu-chev"/></svg></summary>
            <p class="faq-answer">Review your selections before submitting your ballot. Contact the election office if you need assistance.</p>
        </details>
        <details class="faq-item">
            <summary>Where can I see election results?<svg class="icon" aria-hidden="true"><use href="#pcu-chev"/></svg></summary>
            <p class="faq-answer">Published results are available from the Results link in the navigation.</p>
        </details>
    </div>
</section>

<section class="content-section" id="guidelines">
    <div class="page-wrap">
        <div class="section-heading"><div><h2>Election guidelines</h2></div></div>
        <div class="guidelines-card">
            <p>Follow the university's election guidelines and review the rules before participating.</p>
            <ul>
                <li>Only eligible students may cast a ballot.</li>
                <li>Each student may vote only once.</li>
                <li>Do not attempt to interfere with election processes.</li>
                <li>Respect candidates and their campaigns.</li>
                <li>Follow the university's code of conduct during the election period.</li>
            </ul>
        </div>
    </div>
</section>

@include('layouts.partials.help-button')

<div class="overlay-dialog" id="article-modal" role="dialog" aria-modal="true" aria-labelledby="article-modal-title" tabindex="-1" hidden>
    <div class="dialog-panel">
        <div class="dialog-head">
            <h2 id="article-modal-title"></h2>
            <button class="icon-button" type="button" data-close-article aria-label="Close article"><svg class="icon" aria-hidden="true"><use href="#pcu-x"/></svg></button>
        </div>
        <div class="dialog-body">
            <div class="dialog-meta"><span id="article-modal-author"></span><span id="article-modal-date"></span></div>
            <img id="article-modal-image" alt="Article image" hidden>
            <div class="dialog-article-content" id="article-modal-content"></div>
        </div>
    </div>
</div>

<div class="overlay-dialog" id="event-modal" role="dialog" aria-modal="true" aria-labelledby="event-modal-title" tabindex="-1" hidden>
    <div class="dialog-panel">
        <div class="dialog-head">
            <h2 id="event-modal-title"></h2>
            <button class="icon-button" type="button" data-close-event aria-label="Close event details"><svg class="icon" aria-hidden="true"><use href="#pcu-x"/></svg></button>
        </div>
        <div class="dialog-body">
            <div class="dialog-meta" id="event-modal-date"></div>
            <img id="event-modal-image" alt="Event image" hidden>
            <div id="event-modal-content"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const eventContainer = document.getElementById('events-container');
        const articleContainer = document.getElementById('latest-articles-container');
        const eventControls = document.getElementById('event-carousel-controls');
        const articleControls = document.getElementById('article-carousel-controls');
        const articleModal = document.getElementById('article-modal');
        const eventModal = document.getElementById('event-modal');
        let eventIndex = 0;
        let articleIndex = 0;
        let previouslyFocused = null;

        function visibleCount(container) {
            if (!container || window.matchMedia('(min-width: 900px)').matches) return 3;
            if (window.matchMedia('(min-width: 640px)').matches) return 2;
            return 1;
        }

        function rotateCards(container, startIndex, direction) {
            if (!container) return startIndex;
            const cards = [...container.children];
            const count = visibleCount(container);
            const maxStart = Math.max(0, cards.length - count);
            if (maxStart === 0) {
                cards.forEach((card) => { card.hidden = false; });
                return 0;
            }
            const nextIndex = startIndex + direction * count;
            const boundedIndex = nextIndex < 0 ? maxStart : (nextIndex > maxStart ? 0 : nextIndex);
            cards.forEach((card, index) => {
                card.hidden = index < boundedIndex || index >= boundedIndex + count;
            });
            return boundedIndex;
        }

        function refreshCarousels() {
            if (eventControls) eventControls.hidden = (eventContainer?.children.length || 0) <= visibleCount(eventContainer);
            if (articleControls) articleControls.hidden = (articleContainer?.children.length || 0) <= visibleCount(articleContainer);
            eventIndex = rotateCards(eventContainer, eventIndex, 0);
            articleIndex = rotateCards(articleContainer, articleIndex, 0);
            if (eventContainer && eventContainer.children.length > visibleCount(eventContainer)) {
                [...eventContainer.children].forEach((card, index) => {
                    card.hidden = index < eventIndex || index >= eventIndex + visibleCount(eventContainer);
                });
            }
            if (articleContainer && articleContainer.children.length > visibleCount(articleContainer)) {
                [...articleContainer.children].forEach((card, index) => {
                    card.hidden = index < articleIndex || index >= articleIndex + visibleCount(articleContainer);
                });
            }
        }

        function setModalOpen(modal, open) {
            if (!modal) return;
            if (open) {
                previouslyFocused = document.activeElement;
                modal.hidden = false;
                modal.querySelector('button')?.focus();
            } else {
                modal.hidden = true;
                previouslyFocused?.focus();
            }
        }

        async function openArticle(card) {
            const articleId = card.dataset.articleId;
            if (!articleId) return;
            setModalOpen(articleModal, true);
            document.getElementById('article-modal-title').textContent = 'Loading...';
            document.getElementById('article-modal-author').textContent = '';
            document.getElementById('article-modal-date').textContent = '';
            document.getElementById('article-modal-content').textContent = '';
            const image = document.getElementById('article-modal-image');
            image.hidden = true;
            image.removeAttribute('src');

            try {
                const response = await fetch(`/article/${encodeURIComponent(articleId)}/json`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (!response.ok) throw new Error(`Article request failed: ${response.status}`);
                const article = await response.json();
                document.getElementById('article-modal-title').textContent = article.title || '';
                document.getElementById('article-modal-author').textContent = article.author ? `By ${article.author}` : '';
                document.getElementById('article-modal-date').textContent = article.created_at ? new Date(article.created_at).toLocaleString() : '';
                document.getElementById('article-modal-content').innerHTML = article.content || '';

                const imageUrl = article.featured_image_url || article.featured_image;
                if (imageUrl) {
                    image.src = imageUrl;
                    image.hidden = false;
                    image.onerror = () => {
                        image.hidden = true;
                        image.removeAttribute('src');
                    };
                }
            } catch (error) {
                console.error(error);
                document.getElementById('article-modal-title').textContent = 'Unable to load article';
                document.getElementById('article-modal-content').textContent = 'Article details could not be loaded. Please try again.';
            }
        }

        function openEvent(card) {
            document.getElementById('event-modal-title').textContent = card.dataset.eventTitle || '';
            document.getElementById('event-modal-date').textContent = card.dataset.eventDate ? `Date: ${card.dataset.eventDate}` : '';
            document.getElementById('event-modal-content').textContent = card.dataset.eventDescription || '';
            const image = document.getElementById('event-modal-image');
            image.hidden = true;
            image.removeAttribute('src');
            if (card.dataset.eventImage) {
                image.src = card.dataset.eventImage;
                image.hidden = false;
                image.onerror = () => {
                    image.hidden = true;
                    image.removeAttribute('src');
                };
            }
            setModalOpen(eventModal, true);
        }

        document.getElementById('prev-btn')?.addEventListener('click', () => {
            eventIndex = rotateCards(eventContainer, eventIndex, -1);
        });
        document.getElementById('next-btn')?.addEventListener('click', () => {
            eventIndex = rotateCards(eventContainer, eventIndex, 1);
        });
        document.getElementById('latest-articles-prev')?.addEventListener('click', () => {
            articleIndex = rotateCards(articleContainer, articleIndex, -1);
        });
        document.getElementById('latest-articles-next')?.addEventListener('click', () => {
            articleIndex = rotateCards(articleContainer, articleIndex, 1);
        });
        window.addEventListener('resize', refreshCarousels);

        document.addEventListener('click', (event) => {
            const target = event.target instanceof Element ? event.target : null;
            if (!target) return;

            const eventCard = target.closest('[data-event-card]');
            const articleCard = target.closest('[data-article-card]');
            if (eventCard) openEvent(eventCard);
            if (articleCard) openArticle(articleCard);
            if (target.closest('[data-close-article]')) setModalOpen(articleModal, false);
            if (target.closest('[data-close-event]')) setModalOpen(eventModal, false);
            if (event.target === articleModal) setModalOpen(articleModal, false);
            if (event.target === eventModal) setModalOpen(eventModal, false);
        });
        document.addEventListener('keydown', (event) => {
            const activeModal = articleModal && !articleModal.hidden ? articleModal : (eventModal && !eventModal.hidden ? eventModal : null);
            if (!activeModal) return;
            if (event.key === 'Escape') {
                setModalOpen(activeModal, false);
                return;
            }
            if (event.key === 'Tab') {
                const focusable = [...activeModal.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])')];
                if (!focusable.length) return;
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        });
        refreshCarousels();
    })();
</script>

@guest
<script>
    (() => {
        const chatbotToggle = document.getElementById('chatbot-toggle');
        const chatbotPanel = document.getElementById('chatbot-panel');
        const chatbotClose = document.getElementById('chatbot-close');
        const chatbotForm = document.getElementById('chatbot-form');
        const chatbotInput = document.getElementById('chatbot-input');
        const chatbotMessages = document.getElementById('chatbot-messages');
        const chatbotQuickReplies = document.getElementById('chatbot-quick-replies');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        if (!chatbotToggle || !chatbotPanel || !chatbotForm || !chatbotInput || !chatbotMessages || !chatbotQuickReplies) return;

        function appendMessage(role, text) {
            const bubble = document.createElement('div');
            bubble.className = `chat-message ${role === 'user' ? 'chat-message-user' : 'chat-message-bot'}`;
            bubble.textContent = text;
            chatbotMessages.append(bubble);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        function renderQuickReplies(replies) {
            chatbotQuickReplies.replaceChildren();
            (replies || []).forEach((reply) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'chat-quick';
                button.textContent = reply;
                button.addEventListener('click', () => {
                    chatbotInput.value = reply;
                    chatbotForm.requestSubmit();
                });
                chatbotQuickReplies.append(button);
            });
        }

        async function sendToBot(message) {
            const response = await fetch("{{ route('chatbot.respond') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf
                },
                credentials: 'same-origin',
                body: JSON.stringify({ message })
            });
            if (!response.ok) throw new Error(`Chatbot request failed: ${response.status}`);
            return response.json();
        }

        function isGibberish(text) {
            const trimmed = String(text || '').trim();
            if (trimmed.length < 2) return true;
            const compact = trimmed.replace(/\s+/g, '');
            if (/^[a-z]+$/i.test(compact) && compact.length >= 8) {
                const lower = compact.toLowerCase();
                if (new Set(lower).size <= 3 || /^(.{1,3})\1+$/.test(lower)) return true;
            }
            const meaningful = (trimmed.match(/[a-z0-9]/gi) || []).length;
            return trimmed.length >= 6 && meaningful <= 2;
        }

        let quickReplies = [
            'How do I log in?',
            'How do I vote?',
            'How do I register as candidate?',
            'Where can I see results?'
        ];

        async function openChatbot() {
            chatbotPanel.hidden = false;
            chatbotToggle.setAttribute('aria-expanded', 'true');
            if (!chatbotMessages.childElementCount) {
                try {
                    const data = await sendToBot('hi');
                    appendMessage('bot', data.reply || "Hi! I'm PCU Assistant.");
                    if (Array.isArray(data.quick_replies) && data.quick_replies.length) quickReplies = data.quick_replies;
                    renderQuickReplies(quickReplies);
                } catch (error) {
                    console.error(error);
                    appendMessage('bot', "Hi! I'm PCU Assistant. Ask how to log in, register as a candidate, vote, or view results.");
                    renderQuickReplies(quickReplies);
                }
            }
            chatbotInput.focus();
        }

        function closeChatbot() {
            chatbotPanel.hidden = true;
            chatbotToggle.setAttribute('aria-expanded', 'false');
        }

        chatbotToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            if (chatbotPanel.hidden) openChatbot();
            else closeChatbot();
        });
        chatbotClose?.addEventListener('click', closeChatbot);
        chatbotPanel.addEventListener('click', (event) => event.stopPropagation());
        chatbotForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const message = chatbotInput.value.trim();
            if (!message) return;
            if (isGibberish(message)) {
                chatbotInput.value = '';
                appendMessage('bot', "I couldn't understand that. Please try a clear question or choose one of these:");
                renderQuickReplies(quickReplies);
                return;
            }
            chatbotInput.value = '';
            appendMessage('user', message);
            try {
                const data = await sendToBot(message);
                appendMessage('bot', data.reply || 'Sorry, I could not respond.');
                renderQuickReplies(data.quick_replies || []);
            } catch (error) {
                console.error(error);
                appendMessage('bot', 'Sorry, something went wrong. Please try again.');
            }
        });
    })();
</script>
@endguest
@endpush

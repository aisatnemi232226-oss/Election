@extends('layouts.voter')

@section('title', 'Election Details | PCU Online Election')
@section('body-class', 'election-details-view')

@push('styles')
<style>
    body.election-details-view .election-details-page {
        width: min(1280px, 100% - 2 * clamp(20px, 5vw, 48px));
        padding-block: clamp(28px, 5vw, 60px) clamp(48px, 7vw, 80px);
    }

    body.election-details-view .election-details-page > .page-intro {
        position: relative;
        isolation: isolate;
        min-height: clamp(210px, 25vw, 300px);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        overflow: hidden;
        margin: 0 0 clamp(24px, 4vw, 36px);
        padding: clamp(26px, 5vw, 54px);
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 26px;
        background:
            linear-gradient(100deg, rgba(5, 17, 42, .88), rgba(5, 20, 48, .68) 58%, rgba(5, 20, 48, .4)),
            linear-gradient(0deg, rgba(4, 14, 32, .5), transparent 78%),
            url("{{ asset('images/background1.jpg') }}") center 42% / cover no-repeat;
        box-shadow: 0 18px 42px rgba(15, 27, 51, .14);
        color: #f7faff;
        text-align: left;
        animation: election-details-intro .55s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.election-details-view .election-details-page > .page-intro::before {
        margin-bottom: 10px;
        color: #c9dcff;
        content: "PCU ONLINE ELECTION";
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .14em;
    }

    body.election-details-view .election-details-page > .page-intro h1 {
        color: #fff;
        font-size: clamp(2rem, 5vw, 3.25rem);
        letter-spacing: -.045em;
        text-wrap: balance;
    }

    body.election-details-view .election-details-page > .page-intro p {
        max-width: 56ch;
        margin: 12px 0 0;
        color: rgba(239, 245, 255, .86);
        font-size: clamp(1rem, 1.8vw, 1.15rem);
    }

    body.election-details-view .candidate-filters {
        margin: 0;
        padding: clamp(20px, 3vw, 30px);
        border-color: color-mix(in srgb, var(--line), transparent 10%);
        border-radius: 22px;
        background: var(--card);
        box-shadow: 0 5px 14px rgba(15, 27, 51, .045), 0 18px 40px rgba(15, 27, 51, .07);
        animation: election-details-intro .55s .08s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.election-details-view .candidate-filters::before {
        display: block;
        margin-bottom: 18px;
        color: var(--brand);
        content: "FIND YOUR CANDIDATE";
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .12em;
    }

    body.election-details-view .candidate-filter-grid {
        gap: 14px;
    }

    body.election-details-view .candidate-filter-grid .field {
        min-width: 0;
        gap: 7px;
    }

    body.election-details-view .candidate-filter-grid .field label {
        font-size: .9rem;
    }

    body.election-details-view .candidate-filter-grid input,
    body.election-details-view .candidate-filter-grid select {
        min-width: 0;
        min-height: 48px;
        border-radius: 12px;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    body.election-details-view .candidate-filter-grid input:focus,
    body.election-details-view .candidate-filter-grid select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 4px color-mix(in srgb, var(--brand), transparent 86%);
    }

    body.election-details-view .candidate-filter-actions {
        gap: 10px;
        margin-top: 20px;
    }

    body.election-details-view .candidate-filter-actions .button {
        min-height: 46px;
        border-radius: 12px;
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    body.election-details-view .candidate-filter-actions .button:hover {
        transform: translateY(-2px);
    }

    body.election-details-view .election-available-section,
    body.election-details-view .election-candidates-section {
        margin-top: clamp(34px, 5vw, 54px);
    }

    body.election-details-view .election-available-section > h2,
    body.election-details-view .election-candidates-section > h2 {
        margin-bottom: 18px;
        font-size: clamp(1.5rem, 3vw, 2rem);
        letter-spacing: -.035em;
        text-wrap: balance;
    }

    body.election-details-view .election-available-section .election-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 300px), 1fr));
        align-items: stretch;
        gap: 16px;
    }

    body.election-details-view .available-election-card {
        position: relative;
        min-width: 0;
        margin-top: 0;
        overflow: hidden;
        border-radius: 19px;
        box-shadow: 0 4px 12px rgba(15, 27, 51, .04), 0 14px 30px rgba(15, 27, 51, .06);
        animation: election-details-intro .4s cubic-bezier(.2, .75, .25, 1) both;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    body.election-details-view .available-election-card::before {
        position: absolute;
        inset: 0 0 auto;
        height: 4px;
        background: linear-gradient(90deg, var(--brand), #75a2ff);
        content: "";
    }

    body.election-details-view .available-election-card:hover {
        transform: translateY(-4px);
        border-color: color-mix(in srgb, var(--brand), transparent 65%);
        box-shadow: 0 10px 22px rgba(15, 27, 51, .07), 0 20px 38px rgba(37, 99, 235, .1);
    }

    body.election-details-view .available-election-card h3 {
        margin: 0 0 14px;
        font-size: 1.2rem;
        letter-spacing: -.02em;
    }

    body.election-details-view .available-election-card p {
        display: flex;
        flex-wrap: wrap;
        gap: 4px 7px;
        margin: 0;
        padding-block: 9px;
        border-top: 1px solid var(--line);
        color: var(--mut);
        font-size: .92rem;
        overflow-wrap: anywhere;
    }

    body.election-details-view .available-election-card p strong {
        color: var(--ink);
    }

    body.election-details-view .election-candidates-section > h2 {
        margin-bottom: 22px;
    }

    body.election-details-view .candidate-position-group + .candidate-position-group {
        margin-top: clamp(26px, 4vw, 38px);
    }

    body.election-details-view .candidate-position-heading {
        min-height: 52px;
        justify-content: space-between;
        margin: 0 0 16px;
        padding: 10px 15px;
        border-color: color-mix(in srgb, var(--brand), transparent 82%);
        border-radius: 14px;
        background: linear-gradient(100deg, var(--tint), color-mix(in srgb, var(--card), var(--tint) 44%));
        color: var(--ink);
        font-size: 1rem;
        font-weight: 800;
    }

    body.election-details-view .candidate-position-heading .tag {
        min-width: 30px;
        justify-content: center;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 82%);
        font-variant-numeric: tabular-nums;
    }

    body.election-details-view .candidate-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 285px), 1fr));
        grid-auto-rows: 1fr;
        align-items: stretch;
        gap: clamp(14px, 2vw, 20px);
    }

    body.election-details-view .candidate-grid > .candidate-card {
        align-self: stretch;
        min-width: 0;
        min-height: 420px;
        margin: 0;
        border-radius: 19px;
        transition: translate .2s ease, filter .2s ease;
    }

    body.election-details-view .candidate-grid > .candidate-card:hover {
        translate: 0 -3px;
    }

    body.election-details-view .candidate-card:focus-visible {
        outline-offset: 5px;
    }

    body.election-details-view .candidate-card-inner {
        min-height: 420px;
    }

    body.election-details-view .candidate-card-face {
        border-color: color-mix(in srgb, var(--line), transparent 6%);
        border-radius: 19px;
        box-shadow: 0 5px 14px rgba(15, 27, 51, .05), 0 16px 34px rgba(15, 27, 51, .07);
    }

    body.election-details-view .candidate-card-front {
        background: linear-gradient(180deg, var(--card), color-mix(in srgb, var(--tint), var(--card) 25%));
    }

    body.election-details-view .candidate-banner {
        height: 124px;
        object-position: center 38%;
    }

    body.election-details-view .candidate-avatar {
        width: 88px;
        height: 88px;
        box-shadow: 0 4px 14px rgba(15, 27, 51, .14);
    }

    body.election-details-view .candidate-card-content {
        min-height: 278px;
        padding-inline: 20px;
    }

    body.election-details-view .candidate-card-content h4 {
        line-height: 1.3;
        text-wrap: balance;
    }

    body.election-details-view .candidate-tags {
        gap: 6px;
    }

    body.election-details-view .candidate-tags .tag {
        max-width: 100%;
        overflow-wrap: anywhere;
    }

    body.election-details-view .candidate-card-actions {
        gap: 9px;
    }

    body.election-details-view .candidate-card-actions .button {
        border-radius: 11px;
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    body.election-details-view .candidate-card-actions .button:not(:disabled):hover {
        transform: translateY(-2px);
    }

    body.election-details-view .candidate-card-back {
        padding: clamp(18px, 3vw, 24px);
    }

    body.election-details-view .candidate-platforms {
        border-radius: 14px;
        line-height: 1.65;
        scrollbar-color: var(--brand) var(--tint);
        scrollbar-width: thin;
    }

    body.election-details-view #candidates-container > .notice,
    body.election-details-view .election-available-section > .notice {
        border-radius: 16px;
        box-shadow: var(--sh);
    }

    body.election-details-view .candidate-modal-panel {
        animation: candidate-modal-enter .24s cubic-bezier(.2, .75, .25, 1) both;
    }

    @keyframes election-details-intro {
        from { opacity: 0; translate: 0 12px; }
        to { opacity: 1; translate: 0 0; }
    }

    @keyframes candidate-modal-enter {
        from { opacity: 0; scale: .985; }
        to { opacity: 1; scale: 1; }
    }

    @media (min-width: 960px) {
        body.election-details-view .candidate-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 639px) {
        body.election-details-view .election-details-page {
            width: min(100% - 32px, 520px);
            padding-block: 26px 48px;
        }

        body.election-details-view .election-details-page > .page-intro {
            min-height: 210px;
            margin-bottom: 20px;
            padding: 24px;
            border-radius: 21px;
            background-position: 58% center;
        }

        body.election-details-view .candidate-filters {
            padding: 19px;
            border-radius: 18px;
        }

        body.election-details-view .candidate-filter-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(100px, .55fr);
        }

        body.election-details-view .candidate-filter-actions .button {
            min-width: 0;
            padding-inline: 12px;
            font-size: .94rem;
        }

        body.election-details-view .candidate-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        body.election-details-view .candidate-grid > .candidate-card,
        body.election-details-view .candidate-card-inner {
            min-height: 410px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        body.election-details-view .election-details-page > .page-intro,
        body.election-details-view .candidate-filters,
        body.election-details-view .available-election-card {
            animation: none;
        }

        body.election-details-view .available-election-card,
        body.election-details-view .candidate-grid > .candidate-card,
        body.election-details-view .candidate-filter-grid input,
        body.election-details-view .candidate-filter-grid select,
        body.election-details-view .candidate-filter-actions .button,
        body.election-details-view .candidate-card-actions .button {
            transition: none;
        }

        body.election-details-view .candidate-modal-panel {
            animation: none;
        }
    }
</style>
@endpush

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
@php
    $selectedId = $selectedPositionId ?? null;
    $pageParties = $parties ?? collect();
    $pageCourses = $courses ?? collect();
    $pagePositions = $positions ?? collect();
    $pageCandidates = $candidates ?? collect();
    $pageElections = $elections ?? collect();
    $selectedParty = $selectedPartyId ?? null;
    $selectedCourse = $selectedCourseId ?? null;
    $q = $searchQuery ?? '';
    $userVotesMap = $userVotesByPosition ?? [];
    $isVerifiedStudent = auth()->check() && !auth()->user()->is_admin && auth()->user()->verifiedVoter()->exists();
    $candidatesByPositionId = $pageCandidates->groupBy(fn ($candidate) => $candidate->position_id ?? 0);
@endphp

<div class="page-content election-details-page">
    <header class="page-intro">
        <h1>Election Details</h1>
        <p>Review the election schedule, explore candidates, and cast your vote.</p>
    </header>

    <section class="surface-card candidate-filters">
        <form id="filter-form" method="GET" action="{{ route('election.details') }}">
            <div class="candidate-filter-grid">
                <div class="field"><label for="candidate-search">Search candidate</label><input id="candidate-search" type="search" name="q" value="{{ $q }}" placeholder="Search name..."></div>
                <div class="field">
                    <label for="candidate-party">Party</label>
                    <select id="candidate-party" name="party"><option value="">All parties</option>
                        @foreach($pageParties as $party)
                            <option value="{{ $party->id }}" @selected((string) $selectedParty === (string) $party->id)>{{ $party->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="candidate-course">Course</label>
                    <select id="candidate-course" name="course"><option value="">All courses</option>
                        @foreach($pageCourses as $course)
                            <option value="{{ $course->id }}" @selected((string) $selectedCourse === (string) $course->id)>{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="candidate-position">Position</label>
                    <select id="candidate-position" name="position"><option value="">All positions</option>
                        @foreach($pagePositions as $pos)
                            <option value="{{ $pos->id }}" @selected((string) ($selectedPositionId ?? '') === (string) $pos->id)>{{ $pos->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="candidate-filter-actions">
                <button type="submit" class="button button-primary">Apply filters</button>
                <a href="{{ route('election.details') }}" class="button button-secondary">Reset</a>
            </div>
        </form>
    </section>

    @if(empty($selectedId))
        <section class="election-available-section">
            <h2>Available Elections</h2>
            @if($pageElections->isEmpty())
                <div class="notice">No elections available.</div>
            @else
                <div class="surface-grid election-grid">
                    @foreach($pageElections as $e)
                        <article class="surface-card available-election-card">
                            <h3>{{ $e->name }}</h3>
                            <p><strong>Start:</strong> {{ \Carbon\Carbon::parse($e->start_date)->format('Y-m-d') }}</p>
                            <p><strong>End:</strong> {{ \Carbon\Carbon::parse($e->end_date)->format('Y-m-d') }}</p>
                            <p><strong>Registration:</strong> {{ \Carbon\Carbon::parse($e->reg_start)->format('Y-m-d') }} - {{ \Carbon\Carbon::parse($e->reg_end)->format('Y-m-d') }}</p>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    <section class="election-candidates-section">
        <h2>Candidates</h2>
        <div id="candidates-container" aria-live="polite">
            @if($pageCandidates->isEmpty())
                <div class="notice">No candidates available.</div>
            @else
                @foreach($pagePositions as $p)
                    @php($positionCandidates = $candidatesByPositionId->get($p->id, collect()))
                    @if($positionCandidates->isNotEmpty())
                        <section class="candidate-position-group">
                            <h3 class="candidate-position-heading"><span>{{ $p->name }}</span><span class="tag">{{ $positionCandidates->count() }}</span></h3>
                            <div class="surface-grid candidate-grid">
                                @foreach($positionCandidates as $c)
                                    @include('voter.partials.candidate-card', ['c' => $c])
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach

                @php($otherCandidates = $candidatesByPositionId->get(0, collect()))
                @if($otherCandidates->isNotEmpty())
                    <section class="candidate-position-group">
                        <h3 class="candidate-position-heading"><span>Other</span><span class="tag">{{ $otherCandidates->count() }}</span></h3>
                        <div class="surface-grid candidate-grid">
                            @foreach($otherCandidates as $c)
                                @include('voter.partials.candidate-card', ['c' => $c])
                            @endforeach
                        </div>
                    </section>
                @endif
            @endif
        </div>
    </section>
</div>

<div id="candidateProfileModal" class="candidate-modal" hidden aria-hidden="true">
    <div class="candidate-modal-backdrop" data-candidate-modal-backdrop></div>
    <section class="candidate-modal-panel" role="dialog" aria-modal="true" aria-labelledby="candidate-modal-title">
        <button type="button" class="candidate-modal-close" aria-label="Close candidate profile" id="candidateProfileModalClose">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <img id="candidate-modal-banner" class="candidate-modal-banner" src="" alt="">
        <div class="candidate-modal-identity">
            <img id="candidate-modal-profile" src="" alt="">
            <div><h2 id="candidate-modal-title">Candidate Profile</h2><div id="candidate-modal-name" class="visually-hidden"></div>
                <div class="candidate-tags">
                    <span class="tag" id="candidate-modal-party"></span>
                    <span class="tag" id="candidate-modal-position"></span>
                    <span class="tag" id="candidate-modal-course"></span>
                    <span class="badge" id="candidate-modal-status"></span>
                </div>
            </div>
        </div>
        <div class="candidate-modal-grid">
            <section class="surface-subcard"><h3>About</h3><div id="candidate-modal-description" class="candidate-modal-copy"></div></section>
            <section class="surface-subcard"><h3>Platforms</h3><div id="candidate-modal-platforms" class="candidate-modal-copy platforms-content"></div></section>
            <section class="surface-subcard"><h3>Related Events</h3><ul id="candidate-modal-related-events"></ul></section>
            <section class="surface-subcard"><h3>Joined Elections</h3><ul id="candidate-modal-joined-elections"></ul></section>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('submit', function (event) {
    const form = event.target.closest('form[data-confirm-vote]');
    if (!form || form.dataset.confirmed === 'true') return;
    event.preventDefault();
    Swal.fire({
        icon: 'question',
        title: 'Confirm your vote',
        text: `Are you sure you want to vote for ${form.dataset.candidateName || 'this candidate'}?`,
        showCancelButton: true,
        confirmButtonText: 'Yes, cast my vote',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            form.dataset.confirmed = 'true';
            form.submit();
        }
    });
});

document.addEventListener('click', function (event) {
    const profileButton = event.target.closest('.view-profile-btn');
    if (profileButton) {
        openCandidateModal(profileButton);
        return;
    }
    if (event.target.closest('[data-no-flip]')) return;
    const card = event.target.closest('[data-flip-card]');
    if (card) card.classList.toggle('is-flipped');
});

document.addEventListener('keydown', function (event) {
    const modal = document.getElementById('candidateProfileModal');
    if (event.key === 'Escape' && !modal.hidden) {
        closeCandidateModal();
        return;
    }
    if (event.target.closest && event.target.closest('[data-no-flip]')) return;
    const card = event.target.closest && event.target.closest('[data-flip-card]');
    if (card && (event.key === 'Enter' || event.key === ' ')) {
        event.preventDefault();
        card.classList.toggle('is-flipped');
    }
});

const candidateModal = document.getElementById('candidateProfileModal');
const candidateModalClose = document.getElementById('candidateProfileModalClose');
const candidateModalBackdrop = candidateModal.querySelector('[data-candidate-modal-backdrop]');
let lastProfileTrigger = null;

function setCandidateStatus(element, status) {
    element.textContent = status;
    element.className = 'badge ' + (status === 'Active' ? 'badge-success' : status === 'Pending' ? 'badge-warning' : status ? 'badge-error' : '');
}

function parseCandidateList(value) {
    return [...new Set(String(value || '').split(/[\n,]+/).map(item => item.trim()).filter(Boolean))];
}

function fillCandidateList(list, values, emptyMessage) {
    list.replaceChildren();
    (values.length ? values : [emptyMessage]).forEach(value => {
        const item = document.createElement('li');
        item.textContent = value;
        list.appendChild(item);
    });
}

function openCandidateModal(button) {
    lastProfileTrigger = button;
    const name = button.dataset.candidateName || '';
    document.getElementById('candidate-modal-title').textContent = name ? `${name} - Profile` : 'Candidate Profile';
    document.getElementById('candidate-modal-name').textContent = name;
    document.getElementById('candidate-modal-party').textContent = button.dataset.candidateParty || '';
    document.getElementById('candidate-modal-position').textContent = button.dataset.candidatePosition || '';
    document.getElementById('candidate-modal-course').textContent = button.dataset.candidateCourse || '';
    setCandidateStatus(document.getElementById('candidate-modal-status'), button.dataset.candidateStatus || '');

    const banner = document.getElementById('candidate-modal-banner');
    banner.src = button.dataset.candidateBannerUrl || '';
    banner.alt = name ? `${name} banner` : 'Candidate banner';
    banner.onerror = function () { this.onerror = null; this.src = @json(asset('images/default-banner.png')); };
    const profile = document.getElementById('candidate-modal-profile');
    profile.src = button.dataset.candidateProfileUrl || '';
    profile.alt = name || 'Candidate profile';
    profile.onerror = function () { this.onerror = null; this.src = @json(asset('images/default-profile.jpg')); };

    let description = button.dataset.candidateDescription || '';
    let platforms = button.dataset.candidatePlatforms || '';
    try { description = JSON.parse(description) || ''; } catch (error) {}
    try { platforms = JSON.parse(platforms) || ''; } catch (error) {}
    document.getElementById('candidate-modal-description').innerHTML = description || 'No description available.';
    document.getElementById('candidate-modal-platforms').innerHTML = platforms || 'No platforms available.';
    fillCandidateList(document.getElementById('candidate-modal-related-events'), parseCandidateList(button.dataset.candidateRelatedEvents), 'No related events available.');

    let joinedElections = [];
    const joinedRaw = button.dataset.candidateJoinedElections || '';
    try {
        const parsed = JSON.parse(joinedRaw);
        if (Array.isArray(parsed)) {
            joinedElections = parsed.map(item => typeof item === 'object' && item
                ? [item.year || '', item.position || ''].filter(Boolean).join(' - ')
                : String(item));
        } else if (parsed) joinedElections = parseCandidateList(parsed);
    } catch (error) {
        joinedElections = parseCandidateList(joinedRaw);
    }
    fillCandidateList(document.getElementById('candidate-modal-joined-elections'), joinedElections, 'No election history available.');
    candidateModal.hidden = false;
    candidateModal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
    candidateModalClose.focus();
}

function closeCandidateModal() {
    candidateModal.hidden = true;
    candidateModal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    if (lastProfileTrigger) lastProfileTrigger.focus();
}

candidateModalClose.addEventListener('click', closeCandidateModal);
candidateModalBackdrop.addEventListener('click', closeCandidateModal);

(function () {
    const form = document.getElementById('filter-form');
    const container = document.getElementById('candidates-container');
    if (!form || !container) return;
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const query = new URLSearchParams(new FormData(form));
        const target = new URL(form.action, window.location.origin);
        target.search = query.toString();
        try {
            const response = await fetch(target.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error(`Candidate filters request failed (${response.status})`);
            const html = await response.text();
            const documentFromResponse = new DOMParser().parseFromString(html, 'text/html');
            const updated = documentFromResponse.querySelector('#candidates-container');
            if (!updated) throw new Error('Filtered response did not include candidate results.');
            container.innerHTML = updated.innerHTML;
            history.replaceState({}, '', target.toString());
        } catch (error) {
            console.error('Could not update candidate results.', error);
            form.submit();
        }
    });
})();
</script>
@endpush

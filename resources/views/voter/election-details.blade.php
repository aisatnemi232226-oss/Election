@extends('layouts.voter')

@section('title', 'Election Details | PCU Online Election')

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

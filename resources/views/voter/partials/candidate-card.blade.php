@php
    $profileRelative = $c->profile_img
        ? (\Illuminate\Support\Str::contains($c->profile_img, '/') ? $c->profile_img : ('profile-picture/' . $c->profile_img))
        : null;
    $bannerRelative = $c->banner_img
        ? (\Illuminate\Support\Str::contains($c->banner_img, '/') ? $c->banner_img : ('banner/' . $c->banner_img))
        : null;
    $profileExists = $profileRelative && \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $profileRelative);
    $bannerExists = $bannerRelative && \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $bannerRelative);
    $profileUrl = $profileExists
        ? route('storage.image', ['folder' => 'upload', 'filename' => $profileRelative])
        : asset('images/default-profile.jpg');
    $bannerUrl = $bannerExists
        ? route('storage.image', ['folder' => 'upload', 'filename' => $bannerRelative])
        : asset('images/default-banner.png');
    $positionId = $c->position_id;
    $votedCandidateId = $positionId ? ($userVotesMap[$positionId] ?? null) : null;
    $hasVotedForPosition = !empty($votedCandidateId);
    $isUserVotedCandidate = $hasVotedForPosition && ((string) $votedCandidateId === (string) $c->id);
@endphp
<article class="candidate-card" data-flip-card tabindex="0" aria-label="{{ $c->name }} candidate profile. Press Enter to view platforms.">
    <div class="candidate-card-inner">
        <div class="candidate-card-face candidate-card-front">
            <img class="candidate-banner" src="{{ $bannerUrl }}" alt="{{ $c->name }} banner" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
            <img class="candidate-avatar" src="{{ $profileUrl }}" alt="{{ $c->name }}" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
            <div class="candidate-card-content">
                <h4>{{ $c->name }}</h4>
                <div class="candidate-tags">
                    <span class="tag">{{ $c->party ? $c->party->name : 'No Party' }}</span>
                    <span class="tag">{{ optional($c->position)->name ?? 'N/A' }}</span>
                    <span class="tag">{{ optional($c->course)->name ?? 'N/A' }}</span>
                </div>
                <span class="badge {{ $c->status === 'Active' ? 'badge-success' : ($c->status === 'Pending' ? 'badge-warning' : 'badge-error') }}">{{ $c->status }}</span>
                <div class="candidate-card-actions" data-no-flip>
                    <button type="button" class="button button-secondary view-profile-btn" data-no-flip
                        data-candidate-id="{{ $c->id }}"
                        data-candidate-name="{{ e($c->name ?? '') }}"
                        data-candidate-party="{{ e($c->party ? $c->party->name : 'No Party') }}"
                        data-candidate-position="{{ e(optional($c->position)->name ?? 'N/A') }}"
                        data-candidate-course="{{ e(optional($c->course)->name ?? 'N/A') }}"
                        data-candidate-status="{{ e($c->status ?? '') }}"
                        data-candidate-profile-url="{{ e($profileUrl) }}"
                        data-candidate-banner-url="{{ e($bannerUrl) }}"
                        data-candidate-description='@json($c->description ?? "")'
                        data-candidate-platforms='@json($c->platforms ?? "")'
                        data-candidate-related-events="{{ e($c->related_events ?? '') }}"
                        data-candidate-joined-elections="{{ e($c->joined_elections ?? '') }}">
                        View Profile
                    </button>
                    @auth
                        @if(!$positionId)
                            <button type="button" class="button button-disabled" disabled>Vote Now</button>
                        @elseif(!$isVerifiedStudent)
                            <button type="button" class="button button-disabled" disabled title="Only verified students can vote">Verification Required</button>
                        @elseif($isUserVotedCandidate)
                            <button type="button" class="button button-disabled" disabled>Voted</button>
                        @elseif($hasVotedForPosition)
                            <button type="button" class="button button-disabled" disabled>Voted</button>
                        @else
                            <form method="POST" action="{{ route('vote.cast') }}" data-no-flip data-confirm-vote data-candidate-name="{{ $c->name }}">
                                @csrf
                                <input type="hidden" name="candidate_id" value="{{ $c->id }}">
                                <button type="submit" class="button button-primary" data-no-flip>Vote Now</button>
                            </form>
                        @endif
                    @endauth
                    @guest
                        <a href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}" class="button button-primary" data-no-flip>Log in to Vote</a>
                    @endguest
                </div>
                <p class="candidate-flip-hint">Select the card to view platforms</p>
            </div>
        </div>
        <div class="candidate-card-face candidate-card-back">
            <h4>Platforms</h4>
            <p class="muted-text">{{ $c->name }}</p>
            <div class="candidate-platforms platforms-content">{!! $c->platforms ?? '' !!}</div>
            <p class="candidate-flip-hint">Select again to return</p>
        </div>
    </div>
</article>

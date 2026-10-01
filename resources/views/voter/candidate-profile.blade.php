@extends('layouts.voter')

@section('title', ($candidate->name ?? 'Candidate') . ' | PCU Online Election')

@section('content')
@php
    $relatedEvents = preg_split('/[\r\n,]+/', (string) ($candidate->related_events ?? ''), -1, PREG_SPLIT_NO_EMPTY);
    $joinedElectionData = json_decode((string) ($candidate->joined_elections ?? ''), true);
    if (!is_array($joinedElectionData)) {
        $joinedElectionData = preg_split('/[\r\n,]+/', (string) ($candidate->joined_elections ?? ''), -1, PREG_SPLIT_NO_EMPTY);
    }
    $profileRelative = $candidate->profile_img
        ? (\Illuminate\Support\Str::contains($candidate->profile_img, '/') ? $candidate->profile_img : ('profile-picture/' . $candidate->profile_img))
        : null;
    $bannerRelative = $candidate->banner_img
        ? (\Illuminate\Support\Str::contains($candidate->banner_img, '/') ? $candidate->banner_img : ('banner/' . $candidate->banner_img))
        : null;
    $profileUrl = $profileRelative && \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $profileRelative)
        ? route('storage.image', ['folder' => 'upload', 'filename' => $profileRelative])
        : asset('images/default-profile.jpg');
    $bannerUrl = $bannerRelative && \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $bannerRelative)
        ? route('storage.image', ['folder' => 'upload', 'filename' => $bannerRelative])
        : asset('images/default-banner.png');
@endphp
<div class="page-content candidate-profile-page">
    <a class="button button-secondary profile-back" href="{{ url()->previous() }}" aria-label="Go back to the previous page">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6M9 12h12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back
    </a>

    <article class="surface-card candidate-profile-card">
        <img class="candidate-profile-banner" src="{{ $bannerUrl }}" alt="{{ $candidate->name }} banner" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
        <div class="candidate-profile-heading">
            <img class="candidate-profile-photo" src="{{ $profileUrl }}" alt="{{ $candidate->name }} profile photo" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
            <div>
                <span class="badge badge-success">{{ $candidate->status ?? 'Candidate' }}</span>
                <h1>{{ $candidate->name }}</h1>
                <p>{{ $candidate->position->name ?? 'Position not specified' }}</p>
                @if($candidate->party)
                    <p class="candidate-profile-party">{{ $candidate->party->name }}</p>
                @endif
            </div>
        </div>

        <div class="candidate-profile-grid">
            <section class="candidate-profile-section">
                <h2>About</h2>
                <div class="candidate-profile-copy">{!! $candidate->description ?? 'No biography has been provided.' !!}</div>
            </section>
            <section class="candidate-profile-section">
                <h2>Platforms</h2>
                <div class="candidate-profile-copy">{!! $candidate->platforms ?? 'No platform information has been provided.' !!}</div>
            </section>
        </div>

        <section class="candidate-profile-section candidate-profile-events">
            <h2>Candidate Information</h2>
            <dl>
                <div><dt>Course</dt><dd>{{ $candidate->course->name ?? 'Not specified' }}</dd></div>
                <div><dt>Position</dt><dd>{{ $candidate->position->name ?? 'Not specified' }}</dd></div>
                <div><dt>Party</dt><dd>{{ $candidate->party->name ?? 'Independent' }}</dd></div>
            </dl>
            <div class="candidate-profile-grid candidate-history-grid">
                <section>
                    <h3>Related Events</h3>
                    @if(count($relatedEvents))
                        <ul>@foreach($relatedEvents as $event)<li>{{ trim($event) }}</li>@endforeach</ul>
                    @else
                        <p>No related events available.</p>
                    @endif
                </section>
                <section>
                    <h3>Joined Elections</h3>
                    @if(count($joinedElectionData))
                        <ul>
                            @foreach($joinedElectionData as $election)
                                <li>
                                    @if(is_array($election))
                                        {{ trim(implode(' - ', array_filter([$election['year'] ?? '', $election['position'] ?? '']))) ?: 'Election entry' }}
                                    @else
                                        {{ trim($election) }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No election history available.</p>
                    @endif
                </section>
            </div>
        </section>
    </article>
</div>
@endsection

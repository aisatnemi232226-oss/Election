@extends('layouts.voter')

@section('title', ($party->name ?? 'Party') . ' Roster | PCU Online Election')

@section('content')
@php
    $presidentPositionIds = $positions
        ->filter(function ($position) {
            $name = strtolower($position->name ?? '');
            return str_contains($name, 'president') && !str_contains($name, 'vice');
        })
        ->pluck('id')
        ->toArray();
    $presidentCandidates = collect();
    foreach ($presidentPositionIds as $positionId) {
        $presidentCandidates = $presidentCandidates->merge($candidatesByPositionId->get($positionId, collect()));
    }
    $nonPresidentCandidates = collect();
    foreach ($positions as $position) {
        if (!in_array($position->id, $presidentPositionIds, true)) {
            $nonPresidentCandidates = $nonPresidentCandidates->merge($candidatesByPositionId->get($position->id, collect()));
        }
    }
    $layerTwoCandidates = $nonPresidentCandidates->take(3);
    $layerThreeCandidates = $nonPresidentCandidates->slice(3, 3);
    $pyramidCandidateIds = $presidentCandidates->pluck('id')
        ->merge($layerTwoCandidates->pluck('id'))
        ->merge($layerThreeCandidates->pluck('id'))
        ->unique()
        ->values()
        ->toArray();
    $partyLogoUrl = $party->logo
        ? route('storage.image', ['folder' => 'election-parties', 'filename' => basename($party->logo)])
        : null;
@endphp

<div class="page-content party-roster-page">
    <div class="party-roster-heading">
        <div><h1>{{ $party->name }} Roster</h1><p>Candidates registered under this party, grouped by position.</p></div>
        <a href="{{ route('vote') }}" class="button button-primary">Back to Parties</a>
    </div>

    <section class="surface-card party-overview">
        <div class="party-logo-frame">
            @if($partyLogoUrl)
                <img src="{{ $partyLogoUrl }}" alt="{{ $party->name }} logo" onerror="this.onerror=null;this.style.display='none'">
            @else
                <span>No logo</span>
            @endif
        </div>
        <div><h2>{{ $party->name }}</h2><p>{{ $party->description ?: 'No description available.' }}</p></div>
    </section>

    @php($hasPyramidCandidates = $presidentCandidates->isNotEmpty() || $layerTwoCandidates->isNotEmpty() || $layerThreeCandidates->isNotEmpty())
    @if($hasPyramidCandidates)
        <section class="party-pyramid-section">
            <h2>Pyramid View</h2>
            @if($presidentCandidates->isNotEmpty())
                <div class="party-pyramid-row party-pyramid-top">
                    @foreach($presidentCandidates as $c)
                        @include('voter.partials.party-roster-candidate', ['c' => $c])
                    @endforeach
                </div>
            @endif
            @if($layerTwoCandidates->isNotEmpty())
                <div class="party-pyramid-row">
                    @foreach($layerTwoCandidates as $c)
                        @include('voter.partials.party-roster-candidate', ['c' => $c])
                    @endforeach
                </div>
            @endif
            @if($layerThreeCandidates->isNotEmpty())
                <div class="party-pyramid-row">
                    @foreach($layerThreeCandidates as $c)
                        @include('voter.partials.party-roster-candidate', ['c' => $c])
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    @php($remainingCount = 0)
    @foreach($positions as $position)
        @php($group = $candidatesByPositionId->get($position->id, collect())->reject(fn ($candidate) => in_array($candidate->id, $pyramidCandidateIds, true)))
        @if($group->isNotEmpty())
            @php($remainingCount += $group->count())
            <section class="party-position-section">
                <h2>{{ $position->name }} <span class="tag">{{ $group->count() }}</span></h2>
                <div class="surface-grid party-roster-grid">
                    @foreach($group as $c)
                        @include('voter.partials.party-roster-candidate', ['c' => $c])
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach
    @php($otherCandidates = $candidatesByPositionId->get(0, collect())->reject(fn ($candidate) => in_array($candidate->id, $pyramidCandidateIds, true)))
    @if($otherCandidates->isNotEmpty())
        @php($remainingCount += $otherCandidates->count())
        <section class="party-position-section">
            <h2>Other <span class="tag">{{ $otherCandidates->count() }}</span></h2>
            <div class="surface-grid party-roster-grid">
                @foreach($otherCandidates as $c)
                    @include('voter.partials.party-roster-candidate', ['c' => $c])
                @endforeach
            </div>
        </section>
    @endif
    @if(!$hasPyramidCandidates && $remainingCount === 0)
        <div class="notice party-roster-empty">No candidates registered for this party yet.</div>
    @endif
</div>
@endsection

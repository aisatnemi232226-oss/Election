@extends('layouts.voter')

@section('title', 'Election Results')

@section('content')
@php
    $selectedTileUrl = null;
    if (!empty($selectedPosition) && !empty($selectedPosition->tile_img)) {
        $rawSelectedTile = $selectedPosition->tile_img;
        if (\Illuminate\Support\Str::startsWith($rawSelectedTile, 'upload/')) {
            $rawSelectedTile = substr($rawSelectedTile, strlen('upload/'));
        }
        $selectedTileRelative = \Illuminate\Support\Str::contains($rawSelectedTile, '/')
            ? $rawSelectedTile
            : ('position-tiles/' . $rawSelectedTile);
        $selectedTileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $selectedTileRelative);
        $selectedTileUrl = $selectedTileExists
            ? route('storage.image', ['folder' => 'upload', 'filename' => $selectedTileRelative])
            : asset('images/default-banner.png');
    }
    $groups = $candidatesByPositionId ?? collect();
    $selectedId = $selectedPositionId ? (int) $selectedPositionId : null;

    $candidateImageUrl = static function ($candidate) {
        $relative = $candidate->profile_img
            ? (\Illuminate\Support\Str::contains($candidate->profile_img, '/') ? $candidate->profile_img : ('profile-picture/' . $candidate->profile_img))
            : null;
        $exists = $relative
            ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $relative)
            : false;
        return $exists
            ? route('storage.image', ['folder' => 'upload', 'filename' => $relative])
            : asset('images/default-profile.jpg');
    };

    $malePct = null;
    $femalePct = null;
    if (!empty($genderStats) && isset($genderStats['male_pct'], $genderStats['female_pct'])) {
        $malePct = $genderStats['male_pct'];
        $femalePct = $genderStats['female_pct'];
    }
    $topCourses = ($courseStats ?? collect())->take(5);
    $turnoutRows = ($yearLevelTurnout ?? collect())->take(5);
@endphp
<div class="page-content">
    <header class="page-intro">
        @if($selectedTileUrl)
            <img class="tally-banner" src="{{ $selectedTileUrl }}" alt="{{ $selectedPosition->name }}" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
        @endif
        <h1>{{ $selectedPosition ? ($selectedPosition->name . ' Results') : 'Election Results' }}</h1>
        <p>View the detailed results of the election.</p>
    </header>

    <section class="surface-card">
        <h2>Candidate tally</h2>
        @if($selectedId)
            @php
                $positionCandidates = $groups->get($selectedId, collect());
                $positionTotalVotes = (int) ($votesTotalByPositionId[$selectedId] ?? 0);
            @endphp
            <div class="surface-grid result-candidate-grid">
                @forelse($positionCandidates as $candidate)
                    @php
                        $votesCount = (int) ($candidate->votes_count ?? 0);
                        $percentage = $positionTotalVotes > 0 ? round(($votesCount / $positionTotalVotes) * 100, 2) : 0;
                    @endphp
                    <article class="surface-subcard result-card">
                        <div class="result-candidate">
                            <img src="{{ $candidateImageUrl($candidate) }}" alt="{{ $candidate->name }} profile picture" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
                            <div><h3>{{ $candidate->name }}</h3><p>{{ $votesCount }} votes</p></div>
                        </div>
                        <span class="tag">{{ $percentage }}%</span>
                        <div class="progress-track" role="progressbar" aria-label="{{ $candidate->name }} vote share" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $percentage }}">
                            <div class="progress-fill" style="width:{{ $percentage }}%"></div>
                        </div>
                    </article>
                @empty
                    <div class="empty-card">No candidates found for this position.</div>
                @endforelse
            </div>
        @else
            @forelse(($positions ?? collect()) as $position)
                @php
                    $positionCandidates = $groups->get($position->id, collect());
                    $positionTotalVotes = (int) ($votesTotalByPositionId[$position->id] ?? 0);
                    $positionTileUrl = null;
                    if (!empty($position->tile_img)) {
                        $rawPositionTile = $position->tile_img;
                        if (\Illuminate\Support\Str::startsWith($rawPositionTile, 'upload/')) {
                            $rawPositionTile = substr($rawPositionTile, strlen('upload/'));
                        }
                        $positionTileRelative = \Illuminate\Support\Str::contains($rawPositionTile, '/')
                            ? $rawPositionTile
                            : ('position-tiles/' . $rawPositionTile);
                        $positionTileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $positionTileRelative);
                        $positionTileUrl = $positionTileExists
                            ? route('storage.image', ['folder' => 'upload', 'filename' => $positionTileRelative])
                            : null;
                    }
                @endphp
                @if($positionCandidates->isNotEmpty())
                    <section class="position-results">
                        <div class="section-heading">
                            <h3>{{ $position->name }} <span class="tag">{{ $positionCandidates->count() }}</span></h3>
                        </div>
                        @if($positionTileUrl)
                            <img class="position-banner" src="{{ $positionTileUrl }}" alt="{{ $position->name }}" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
                        @endif
                        <div class="surface-grid result-candidate-grid">
                            @foreach($positionCandidates as $candidate)
                                @php
                                    $votesCount = (int) ($candidate->votes_count ?? 0);
                                    $percentage = $positionTotalVotes > 0 ? round(($votesCount / $positionTotalVotes) * 100, 2) : 0;
                                @endphp
                                <article class="surface-subcard result-card">
                                    <div class="result-candidate">
                                        <img src="{{ $candidateImageUrl($candidate) }}" alt="{{ $candidate->name }} profile picture" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
                                        <div><h4>{{ $candidate->name }}</h4><p>{{ $votesCount }} votes</p></div>
                                    </div>
                                    <span class="tag">{{ $percentage }}%</span>
                                    <div class="progress-track" role="progressbar" aria-label="{{ $candidate->name }} vote share" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $percentage }}">
                                        <div class="progress-fill" style="width:{{ $percentage }}%"></div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            @empty
                <p class="muted-text">No candidate results are available.</p>
            @endforelse
        @endif
    </section>

    <section class="surface-card">
        <h2>Election party tally</h2>
        <div class="chart-wrap"><canvas id="partyPieChart" aria-label="Election party vote tally" role="img"></canvas></div>
    </section>

    <section class="surface-card">
        <h2>Course with the largest voter turnout</h2>
        <div class="table-scroll">
            <table class="data-table">
                <thead><tr><th scope="col">Course name</th><th scope="col">Enrolled students</th><th scope="col">Registered voters</th><th scope="col">Voted</th></tr></thead>
                <tbody>
                    @forelse($topCourses as $row)
                        <tr>
                            <td>{{ $row['course_name'] ?? 'N/A' }}</td>
                            <td>{{ (int) ($row['enrolled'] ?? 0) }}</td>
                            <td>{{ (int) ($row['registered'] ?? 0) }}</td>
                            <td>{{ (int) ($row['voted'] ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4">No voter course statistics available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="surface-card">
        <h2>Voter demographics</h2>
        <div class="surface-grid two-column">
            <article class="surface-subcard">
                <h3>Gender distribution</h3>
                <p>Male: {{ $malePct !== null ? ($malePct . '%') : 'N/A' }}</p>
                <p>Female: {{ $femalePct !== null ? ($femalePct . '%') : 'N/A' }}</p>
            </article>
            <article class="surface-subcard">
                <h3>Voter turnout by year</h3>
                @forelse($turnoutRows as $row)
                    <p>{{ $row['year_name'] ?? 'N/A' }}: {{ $row['pct'] ?? 0 }}%</p>
                @empty
                    <p>No turnout statistics available.</p>
                @endforelse
            </article>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const pieCanvas = document.getElementById('partyPieChart');
    if (pieCanvas && window.Chart) {
        const labels = @json($partyTallyLabels ?? []);
        const data = @json($partyTallyData ?? []);
        const safeLabels = (Array.isArray(labels) && labels.length) ? labels : ['No data'];
        const safeData = (Array.isArray(data) && data.length) ? data : [1];
        const palette = ['#1E3A8A', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6', '#14B8A6', '#EC4899', '#64748B'];
        new Chart(pieCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: safeLabels,
                datasets: [{ data: safeData, backgroundColor: safeLabels.map((_, index) => palette[index % palette.length]) }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 11 } } } },
                animation: { animateScale: true, animateRotate: true }
            }
        });
    }
</script>
@endpush

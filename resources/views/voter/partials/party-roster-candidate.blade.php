@php
    $profileRelative = $c->profile_img
        ? (\Illuminate\Support\Str::contains($c->profile_img, '/') ? $c->profile_img : ('profile-picture/' . $c->profile_img))
        : null;
    $bannerRelative = $c->banner_img
        ? (\Illuminate\Support\Str::contains($c->banner_img, '/') ? $c->banner_img : ('banner/' . $c->banner_img))
        : null;
    $profileUrl = $profileRelative && \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $profileRelative)
        ? route('storage.image', ['folder' => 'upload', 'filename' => $profileRelative])
        : asset('images/default-profile.jpg');
    $bannerUrl = $bannerRelative && \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $bannerRelative)
        ? route('storage.image', ['folder' => 'upload', 'filename' => $bannerRelative])
        : asset('images/default-banner.png');
@endphp
<article class="party-roster-candidate">
    <img class="party-roster-banner" src="{{ $bannerUrl }}" alt="{{ $c->name }} banner" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
    <img class="party-roster-avatar" src="{{ $profileUrl }}" alt="{{ $c->name }}" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
    <div class="party-roster-candidate-copy">
        <h3>{{ $c->name }}</h3>
        <p>{{ optional($c->course)->name ?? 'N/A' }}</p>
        <span>{{ optional($c->position)->name ?? 'N/A' }}</span>
    </div>
</article>

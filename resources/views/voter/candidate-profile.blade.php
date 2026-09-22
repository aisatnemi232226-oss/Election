<html>
 <head>
  <title>
   Candidate Profile
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet"/>
  <link rel="icon" href="{{ URL::to('/images/pculogo.png') }}">
  <style>
   body {
            font-family: 'Roboto', sans-serif;
        }

    .platforms-content ul,
    .platforms-content ol {
        margin-left: 1.25rem;
        padding-left: 0.25rem;
    }

    .platforms-content ul { list-style: disc; }
    .platforms-content ol { list-style: decimal; }
    .platforms-content li { margin: 0.25rem 0; }
  </style>
 </head>
 <body class="relative bg-gray-100">
  <div class="pointer-events-none fixed inset-0 flex items-center justify-center opacity-30 z-0" aria-hidden="true">
   <img src="{{ asset('images/pculogo.png') }}" alt="" class="w-[62vw] max-w-[820px] h-auto" />
  </div>

  @php
    $profileRelative = $candidate->profile_img
        ? (\Illuminate\Support\Str::contains($candidate->profile_img, '/') ? $candidate->profile_img : ('profile-picture/' . $candidate->profile_img))
        : null;

    $bannerRelative = $candidate->banner_img
        ? (\Illuminate\Support\Str::contains($candidate->banner_img, '/') ? $candidate->banner_img : ('banner/' . $candidate->banner_img))
        : null;

    $profileExists = $profileRelative
        ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $profileRelative)
        : false;

    $bannerExists = $bannerRelative
        ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $bannerRelative)
        : false;

    $profileUrl = $profileExists
        ? route('storage.image', ['folder' => 'upload', 'filename' => $profileRelative])
        : asset('images/default-profile.jpg');

    $bannerUrl = $bannerExists
        ? route('storage.image', ['folder' => 'upload', 'filename' => $bannerRelative])
        : asset('images/default-banner.png');
  @endphp

  <header class="relative z-50 bg-blue-600 text-white p-4">
   <div class="container mx-auto flex items-center justify-between">
    <a href="{{ route('election.details') }}" class="inline-flex items-center gap-2 hover:underline">
     <i class="fas fa-arrow-left"></i>
     <span>Back</span>
    </a>
    <div class="font-bold">Candidate Profile</div>
    <div></div>
   </div>
  </header>

  <main class="relative z-10 container mx-auto p-4">
   <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="relative">
     <img src="{{ $bannerUrl }}" alt="{{ $candidate->name }} banner" class="w-full h-56 object-cover" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
     <div class="absolute -bottom-12 left-6 flex items-end gap-4">
      <img src="{{ $profileUrl }}" alt="{{ $candidate->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
      <div class="pb-2">
       <div class="text-2xl font-bold text-white drop-shadow">{{ $candidate->name }}</div>
       <div class="text-sm text-blue-50 drop-shadow">{{ optional($candidate->position)->name ?? 'Candidate' }}</div>
      </div>
     </div>
    </div>

    <div class="p-6 pt-16">
     <div class="flex flex-wrap gap-2 mb-5">
      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">{{ $candidate->party ? $candidate->party->name : 'No Party' }}</span>
      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">{{ optional($candidate->course)->name ?? 'N/A' }}</span>
      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">{{ optional($candidate->position)->name ?? 'N/A' }}</span>
      <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ ($candidate->status ?? '') === 'Active' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' }}">{{ $candidate->status ?? '—' }}</span>
     </div>

     <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-1">
       <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
        <div class="text-sm font-bold text-gray-900 mb-2">About</div>
        <div class="text-sm text-gray-700 leading-relaxed">{{ $candidate->description ?? 'No description available.' }}</div>
       </div>
      </div>

      <div class="lg:col-span-2">
       <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
        <div class="text-sm font-bold text-gray-900 mb-2">Platforms</div>
        <div class="text-sm text-gray-700 leading-relaxed platforms-content">{!! $candidate->platforms ?? '' !!}</div>
       </div>
      </div>

      @php
        $relatedEventsRaw = (string) ($candidate->related_events ?? '');
        $relatedEvents = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $relatedEventsRaw) ?: []), fn ($v) => $v !== ''));

        $joinedElectionsRaw = (string) ($candidate->joined_elections ?? '');
        $joinedElections = null;
        if ($joinedElectionsRaw !== '') {
            $decoded = json_decode($joinedElectionsRaw, true);
            if (is_array($decoded)) {
                $joinedElections = $decoded;
            }
        }
      @endphp

      <div class="lg:col-span-3">
       <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
         <div class="text-sm font-bold text-gray-900 mb-2">Related Events</div>
         @if(!empty($relatedEvents))
          <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
           @foreach($relatedEvents as $ev)
            <li>{{ $ev }}</li>
           @endforeach
          </ul>
         @else
          <div class="text-sm text-gray-500">No related events available.</div>
         @endif
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
         <div class="text-sm font-bold text-gray-900 mb-2">Joined Elections</div>
         @if(is_array($joinedElections) && count($joinedElections) > 0)
          <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1">
           @foreach($joinedElections as $je)
            @php
                $year = is_array($je) ? ($je['year'] ?? '') : '';
                $pos = is_array($je) ? ($je['position'] ?? '') : '';
                $winner = is_array($je) ? (($je['winner'] ?? false) ? true : false) : false;
            @endphp
            <li>
             {{ trim($year . ' - ' . $pos) }}{{ $winner ? ' (Winner)' : '' }}
            </li>
           @endforeach
          </ul>
         @elseif(!empty($joinedElectionsRaw))
          <div class="text-sm text-gray-700 whitespace-pre-line">{{ $joinedElectionsRaw }}</div>
         @else
          <div class="text-sm text-gray-500">No election history available.</div>
         @endif
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </main>
 </body>
</html>

<html>
 <head>
  <title>
   Philippine Christian University Online Election System - Party Roster
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet"/>
  <style>
   body {
            font-family: 'Roboto', sans-serif;
        }
  </style>
 </head>
 <body class="relative bg-gray-100">
  <div class="pointer-events-none fixed inset-0 flex items-center justify-center opacity-30 z-0" aria-hidden="true">
   <img src="{{ asset('images/pculogo.png') }}" alt="" class="w-[62vw] max-w-[820px] h-auto" />
  </div>

  <header class="relative z-10 bg-blue-600 text-white p-4">
   <div class="container mx-auto flex flex-col items-center md:flex-row md:justify-between">
    <a href="{{ url('/') }}" class="flex items-center gap-3 mb-4 md:mb-0">
        <img src="{{ asset('images/pculogo.png') }}" alt="School Logo" class="w-16 h-16 md:w-24 md:h-24 -my-4 object-contain flex-shrink-0" onerror="this.onerror=null;this.style.display='none';">
        <h1 class="text-3xl font-bold">Philippine Christian University Online Election System</h1>
    </a>
    <nav class="w-full md:w-auto">
     <div class="flex justify-center md:hidden">
      <button class="text-white focus:outline-none" id="menu-toggle">
       <i class="fas fa-bars">
       </i>
      </button>
     </div>
     <ul class="hidden md:flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 text-center md:text-left" id="menu">
      <li>
       <a class="hover:underline" href="{{ url('/') }}">
        Home
       </a>
      </li>
      <li>
       <a class="hover:underline" href="{{ url('/election') }}">
        Election
       </a>
      </li>
      <li>
       <a class="hover:underline" href="{{ route('election.details') }}">
        Vote
       </a>
      </li>
      <li>
       <a class="hover:underline" href="{{ url('/result') }}">
        Results
       </a>
      </li>
      <li>
       <a class="hover:underline" href="{{ url('/contact') }}">
        Contact
       </a>
      </li>
      @guest
      <li>
       <a class="hover:underline" href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}">
        Log In
       </a>
      </li>
      @endguest
      @auth
      <li class="relative">
       <button type="button" class="flex items-center space-x-2 focus:outline-none" data-profile-toggle>
        <span class="inline-flex items-center justify-center w-8 h-8 bg-white text-blue-600 rounded-full">
         <i class="fas fa-user"></i>
        </span>
        <span class="hidden md:inline text-sm">{{ auth()->user()->name ?? 'Profile' }}</span>
       </button>
       <div class="hidden absolute right-0 mt-2 w-56 bg-white text-gray-800 rounded-md shadow-lg z-50 pointer-events-auto" data-profile-menu>
        <div class="px-4 py-2 border-b">
         <p class="font-semibold text-sm truncate">{{ auth()->user()->name }}</p>
         <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
        </div>
        <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout') }}" class="border-t">
         @csrf
         <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Log out</button>
        </form>
       </div>
      </li>
      @endauth
     </ul>
    </nav>
   </div>
  </header>

  <main class="relative z-10 container mx-auto p-4">
   <div class="flex items-center justify-between flex-wrap gap-3 mt-6">
    <div>
     <h2 class="text-3xl md:text-4xl font-bold text-gray-800">{{ $party->name }} Roster</h2>
     <p class="text-gray-600">Candidates registered under this party, grouped by position.</p>
    </div>
    <a href="{{ route('vote') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Back to Parties</a>
   </div>

   @php
    $partyLogoUrl = null;
    if (!empty($party->logo)) {
        $partyLogoFile = basename($party->logo);
        $partyLogoUrl = route('storage.image', ['folder' => 'election-parties', 'filename' => $partyLogoFile]);
    }
   @endphp

   <div class="bg-white rounded-xl shadow-lg p-6 mt-6">
    <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
     <div class="w-full md:w-56">
      <div class="w-full h-32 rounded-lg bg-gray-100 overflow-hidden flex items-center justify-center">
       @if($partyLogoUrl)
        <img src="{{ $partyLogoUrl }}" alt="{{ $party->name }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.style.display='none';" />
       @else
        <div class="text-gray-400 text-sm">No logo</div>
       @endif
      </div>
     </div>
     <div class="flex-1">
      <div class="text-xl font-bold text-gray-800">{{ $party->name }}</div>
      <div class="text-gray-600 mt-1">{{ $party->description ?: 'No description available.' }}</div>
     </div>
    </div>
   </div>

   <div class="mt-6">
    @php
       $hasAny = false;

       $presidentPositionIds = $positions
           ->filter(function ($p) {
               $name = strtolower($p->name ?? '');
               return str_contains($name, 'president') && !str_contains($name, 'vice');
           })
           ->pluck('id')
           ->toArray();

       $presidentCandidates = collect();
       foreach ($presidentPositionIds as $pid) {
           $presidentCandidates = $presidentCandidates->merge($candidatesByPositionId->get($pid, collect()));
       }

       $orderedNonPresidentCandidates = collect();
       foreach ($positions as $p) {
           if (in_array($p->id, $presidentPositionIds, true)) {
               continue;
           }
           $orderedNonPresidentCandidates = $orderedNonPresidentCandidates->merge($candidatesByPositionId->get($p->id, collect()));
       }

       $layer2 = $orderedNonPresidentCandidates->take(3);
       $layer3 = $orderedNonPresidentCandidates->slice(3, 3);

       $shownCandidateIds = $presidentCandidates->pluck('id')
           ->merge($layer2->pluck('id'))
           ->merge($layer3->pluck('id'))
           ->unique()
           ->values()
           ->toArray();

       $hasAny = $shownCandidateIds ? true : false;
    @endphp

    @if($hasAny)
     <div class="max-w-6xl mx-auto">
      <div class="mb-6">
       <div class="flex items-center justify-between mb-3">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 border border-blue-200">
         <span class="text-sm font-bold text-blue-800">Pyramid View</span>
        </div>
       </div>

      @if($presidentCandidates->isNotEmpty())
       <div class="flex flex-wrap justify-center gap-4 mb-6">
        @foreach($presidentCandidates as $c)
          @php
             $profileRelative = $c->profile_img
                 ? (\Illuminate\Support\Str::contains($c->profile_img, '/') ? $c->profile_img : ('profile-picture/' . $c->profile_img))
                 : null;

             $bannerRelative = $c->banner_img
                 ? (\Illuminate\Support\Str::contains($c->banner_img, '/') ? $c->banner_img : ('banner/' . $c->banner_img))
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

          <div class="w-full max-w-sm bg-white rounded-2xl shadow-lg overflow-hidden border h-full flex flex-col">
           <div class="relative">
            <img src="{{ $bannerUrl }}" alt="{{ $c->name }} banner" class="w-full h-28 object-cover" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
            <div class="absolute -bottom-10 left-0 right-0 flex justify-center">
             <img src="{{ $profileUrl }}" alt="{{ $c->name }}" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
            </div>
           </div>

           <div class="pt-12 px-5 pb-5 text-center flex-1 flex flex-col justify-center">
            <div class="text-lg font-bold text-gray-800">{{ $c->name }}</div>
            <div class="text-sm text-gray-600 mt-1">{{ optional($c->course)->name ?? 'N/A' }}</div>
            <div class="text-xs font-semibold text-blue-700 mt-2">{{ optional($c->position)->name ?? 'President' }}</div>
           </div>
          </div>
         @endforeach
        </div>
       </div>
      @endif

      @if($layer2->isNotEmpty())
       <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 justify-items-center mb-6">
        @foreach($layer2 as $c)
         @php
            $profileRelative = $c->profile_img
                ? (\Illuminate\Support\Str::contains($c->profile_img, '/') ? $c->profile_img : ('profile-picture/' . $c->profile_img))
                : null;

            $bannerRelative = $c->banner_img
                ? (\Illuminate\Support\Str::contains($c->banner_img, '/') ? $c->banner_img : ('banner/' . $c->banner_img))
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

         <div class="w-full max-w-sm bg-white rounded-2xl shadow-lg overflow-hidden border h-full flex flex-col">
          <div class="relative">
           <img src="{{ $bannerUrl }}" alt="{{ $c->name }} banner" class="w-full h-28 object-cover" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
           <div class="absolute -bottom-10 left-0 right-0 flex justify-center">
            <img src="{{ $profileUrl }}" alt="{{ $c->name }}" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
           </div>
          </div>

          <div class="pt-12 px-5 pb-5 text-center flex-1 flex flex-col justify-center">
           <div class="text-lg font-bold text-gray-800">{{ $c->name }}</div>
           <div class="text-sm text-gray-600 mt-1">{{ optional($c->course)->name ?? 'N/A' }}</div>
           <div class="text-xs font-semibold text-blue-700 mt-2">{{ optional($c->position)->name ?? 'N/A' }}</div>
          </div>
         </div>
        @endforeach
       </div>
      @endif

      @if($layer3->isNotEmpty())
       <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 justify-items-center">
        @foreach($layer3 as $c)
         @php
            $profileRelative = $c->profile_img
                ? (\Illuminate\Support\Str::contains($c->profile_img, '/') ? $c->profile_img : ('profile-picture/' . $c->profile_img))
                : null;

            $bannerRelative = $c->banner_img
                ? (\Illuminate\Support\Str::contains($c->banner_img, '/') ? $c->banner_img : ('banner/' . $c->banner_img))
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

         <div class="w-full max-w-sm bg-white rounded-2xl shadow-lg overflow-hidden border h-full flex flex-col">
          <div class="relative">
           <img src="{{ $bannerUrl }}" alt="{{ $c->name }} banner" class="w-full h-28 object-cover" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
           <div class="absolute -bottom-10 left-0 right-0 flex justify-center">
            <img src="{{ $profileUrl }}" alt="{{ $c->name }}" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
           </div>
          </div>

          <div class="pt-12 px-5 pb-5 text-center flex-1 flex flex-col justify-center">
           <div class="text-lg font-bold text-gray-800">{{ $c->name }}</div>
           <div class="text-sm text-gray-600 mt-1">{{ optional($c->course)->name ?? 'N/A' }}</div>
           <div class="text-xs font-semibold text-blue-700 mt-2">{{ optional($c->position)->name ?? 'N/A' }}</div>
          </div>
         </div>
        @endforeach
       </div>
      @endif
     </div>
    </div>
   @endif

   @php
       $remainingCount = 0;
   @endphp

   @foreach($positions as $pos)
    @php
       $group = $candidatesByPositionId->get($pos->id, collect())
           ->reject(function ($cand) use ($shownCandidateIds) {
               return in_array($cand->id, $shownCandidateIds, true);
           });

       if ($group->isNotEmpty()) {
           $remainingCount += $group->count();
       }
    @endphp

    @if($group->isNotEmpty())
     <div class="mb-6">
      <div class="flex items-center justify-between mb-3">
       <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 border border-blue-200">
        <span class="text-sm font-bold text-blue-800">{{ $pos->name }}</span>
        <span class="text-xs font-semibold text-blue-700 bg-white border border-blue-200 rounded-full px-2 py-0.5">{{ $group->count() }}</span>
       </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
       @foreach($group as $c)
        @php
           $profileRelative = $c->profile_img
               ? (\Illuminate\Support\Str::contains($c->profile_img, '/') ? $c->profile_img : ('profile-picture/' . $c->profile_img))
               : null;

           $bannerRelative = $c->banner_img
               ? (\Illuminate\Support\Str::contains($c->banner_img, '/') ? $c->banner_img : ('banner/' . $c->banner_img))
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

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border">
         <div class="relative">
          <img src="{{ $bannerUrl }}" alt="{{ $c->name }} banner" class="w-full h-28 object-cover" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
          <div class="absolute -bottom-10 left-0 right-0 flex justify-center">
           <img src="{{ $profileUrl }}" alt="{{ $c->name }}" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
          </div>
         </div>

         <div class="pt-12 px-5 pb-5 text-center">
          <div class="text-lg font-bold text-gray-800">{{ $c->name }}</div>
          <div class="text-sm text-gray-600 mt-1">{{ optional($c->course)->name ?? 'N/A' }}</div>
         </div>
        </div>
       @endforeach
      </div>
     </div>
    @endif
   @endforeach

   @php
       $other = $candidatesByPositionId->get(0, collect())
           ->reject(function ($cand) use ($shownCandidateIds) {
               return in_array($cand->id, $shownCandidateIds, true);
           });

       if ($other->isNotEmpty()) {
           $remainingCount += $other->count();
       }
   @endphp

   @if($other->isNotEmpty())
    <div class="mb-6">
     <div class="flex items-center justify-between mb-3">
      <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 border border-blue-200">
       <span class="text-sm font-bold text-blue-800">Other</span>
       <span class="text-xs font-semibold text-blue-700 bg-white border border-blue-200 rounded-full px-2 py-0.5">{{ $other->count() }}</span>
      </div>
     </div>

     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($other as $c)
       <div class="bg-white rounded-2xl shadow-lg overflow-hidden border p-5 text-center">
        <div class="text-lg font-bold text-gray-800">{{ $c->name }}</div>
       </div>
      @endforeach
     </div>
    </div>
   @endif

   @if(!$hasAny && $remainingCount === 0)
    <div class="bg-white p-6 rounded-lg shadow-lg text-center text-gray-600 mt-6">No candidates registered for this party yet.</div>
   @endif
   </div>
  </main>

  <script>
   const profileToggle = document.querySelector('[data-profile-toggle]');
   const profileMenu = document.querySelector('[data-profile-menu]');
   if (profileToggle && profileMenu) {
       profileToggle.addEventListener('click', (e) => {
           e.stopPropagation();
           profileMenu.classList.toggle('hidden');
       });
       profileMenu.addEventListener('click', (e) => {
           e.stopPropagation();
       });
       document.addEventListener('click', () => {
           profileMenu.classList.add('hidden');
       });
   }

        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');
        if (menuToggle && menu) {
            menuToggle.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }
  </script>
 </body>
</html>

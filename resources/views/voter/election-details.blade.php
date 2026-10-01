<html>
 <head>
  <title>
   Election Details
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet"/>
  <link rel="icon" href="{{ URL::to('/images/pculogo.png') }}">
  <style>
   body {
            font-family: 'Roboto', sans-serif;
        }

    .flip-card {
        perspective: 1200px;
    }

    .flip-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transform-style: preserve-3d;
        transition: transform 700ms cubic-bezier(0.2, 0.8, 0.2, 1);
    }

    .flip-card.is-flipped .flip-card-inner {
        transform: rotateY(180deg);
    }

    .flip-card-face {
        position: absolute;
        inset: 0;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }

    .flip-card-back {
        transform: rotateY(180deg);
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
  <header class="relative z-50 bg-blue-600 text-white p-4">
   <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
    <a href="{{ url('/') }}" class="flex items-center gap-3 mb-4 md:mb-0">
        <img src="{{ asset('images/pculogo.png') }}" alt="School Logo" class="w-16 h-16 md:w-24 md:h-24 -my-4 object-contain flex-shrink-0" onerror="this.onerror=null;this.style.display='none';">
        <h1 class="text-3xl font-bold">
            University Online Election System
        </h1>
    </a>
    <button class="md:hidden text-white" id="menu-toggle">
     <i class="fas fa-bars text-2xl">
     </i>
    </button>
    <nav class="hidden md:block" id="menu">
     <ul class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 items-center">
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
            @include('voter.partials.account-profile')
            @endauth
     </ul>
    </nav>
   </div>
   <nav class="md:hidden hidden" id="mobile-menu">
    <ul class="flex flex-col space-y-2 mt-4">
     <li>
      <a class="hover:underline block text-center" href="{{ url('/') }}">
       Home
      </a>
     </li>
     <li>
      <a class="hover:underline block text-center" href="{{ url('/election') }}">
       Election
      </a>
     </li>
     <li>
      <a class="hover:underline block text-center" href="{{ route('election.details') }}">
       Vote
      </a>
     </li>
     <li>
      <a class="hover:underline block text-center" href="{{ url('/result') }}">
       Results
      </a>
     </li>
     <li>
      <a class="hover:underline block text-center" href="{{ url('/contact') }}">
       Contact
      </a>
     </li>
    @guest
    <li>
     <a class="hover:underline block text-center" href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}">
      Log In
     </a>
    </li>
    @endguest
     @auth
     <li class="border-t pt-2">
      <div class="flex flex-col items-center space-y-1">
       <span class="inline-flex items-center justify-center w-10 h-10 bg-white text-blue-600 rounded-full">
        <i class="fas fa-user"></i>
       </span>
       <span class="text-sm font-semibold">{{ Auth::user()->name }}</span>
       <span class="text-xs text-gray-200">{{ Auth::user()->email }}</span>
       <form method="POST" action="{{ route('logout') }}" class="w-full mt-1">
        @csrf
        <button type="submit" class="w-full text-center text-sm text-red-200 hover:text-white">Log out</button>
       </form>
        <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout') }}" class="w-full mt-1">
         @csrf
         <button type="submit" class="w-full text-center text-sm text-red-200 hover:text-white">Log out</button>
        </form>
      </div>
     </li>
     @endauth
    </ul>
   </nav>
  </header>
  <main class="relative z-10 container mx-auto p-4">
   @if(session('error'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Unable to complete your vote',
                text: @json(session('error')),
                confirmButtonColor: '#2563eb'
            });
        });
    </script>
   @elseif(session('success'))
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'success',
                title: 'Vote recorded successfully',
                text: @json(session('success')),
                confirmButtonColor: '#2563eb'
            });
        });
    </script>
   @endif
   <section class="my-6">
    <div class="flex items-center justify-between">
     <h2 class="text-3xl font-bold text-gray-800">Election Details</h2>
    </div>
   </section>

   @php
       $selectedId = $selectedPositionId ?? null;

       $pageParties = $parties ?? collect();
       $pageCourses = $courses ?? collect();

       $selectedParty = $selectedPartyId ?? null;
       $selectedCourse = $selectedCourseId ?? null;
       $q = $searchQuery ?? '';
   @endphp
    <!-- Persistent filter form (always visible) -->
    <div class="mb-5 bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
     <form id="filter-form" method="GET" action="{{ route('election.details') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
      <div class="md:col-span-4">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Search candidate</label>
        <input type="text" name="q" value="{{ $q }}" placeholder="Search name..." class="w-full border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Party</label>
        <select name="party" class="w-full border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
         <option value="">All parties</option>
         @foreach($pageParties as $party)
          <option value="{{ $party->id }}" @if((string)$selectedParty === (string)$party->id) selected @endif>{{ $party->name }}</option>
         @endforeach
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Course</label>
        <select name="course" class="w-full border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
         <option value="">All courses</option>
         @foreach($pageCourses as $course)
          <option value="{{ $course->id }}" @if((string)$selectedCourse === (string)$course->id) selected @endif>{{ $course->name }}</option>
         @endforeach
        </select>
      </div>

      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Position</label>
        <select name="position" class="w-full border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
         <option value="">All positions</option>
         @foreach(($positions ?? collect()) as $pos)
          <option value="{{ $pos->id }}" @if((string)($selectedPositionId ?? '') === (string)$pos->id) selected @endif>{{ $pos->name }}</option>
         @endforeach
        </select>
      </div>

      <div class="md:col-span-2 flex gap-2">
        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700">Apply</button>
        <a href="{{ route('election.details') }}" class="flex-1 text-center bg-gray-100 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200">Reset</a>
      </div>
     </form>
    </div>

    @if(empty($selectedId))
    <section class="my-6">
     <h3 class="text-2xl font-bold text-gray-800 mb-4">Available Elections</h3>
     @php
         $pageElections = $elections ?? collect();
     @endphp

    
     @if($pageElections->isEmpty())
      <div class="bg-white p-6 rounded-lg shadow-lg text-gray-600">No elections available.</div>
     @else
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
       @foreach($pageElections as $e)
        <div class="bg-white p-6 rounded-lg shadow-lg">
         <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $e->name }}</h4>
         <p class="text-gray-600">Start: {{ \Carbon\Carbon::parse($e->start_date)->format('Y-m-d') }}</p>
         <p class="text-gray-600">End: {{ \Carbon\Carbon::parse($e->end_date)->format('Y-m-d') }}</p>
         <p class="text-gray-600">Registration: {{ \Carbon\Carbon::parse($e->reg_start)->format('Y-m-d') }} - {{ \Carbon\Carbon::parse($e->reg_end)->format('Y-m-d') }}</p>
        </div>
       @endforeach
      </div>
     @endif
    </section>
   @endif

   <section class="my-6">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Candidates</h3>
    @php
        $pageCandidates = $candidates ?? collect();
        $candidatesByPositionId = $pageCandidates->groupBy(function ($c) {
            return $c->position_id ?? 0;
        });

        $pagePositions = $positions ?? collect();
        $selectedId = $selectedPositionId ?? null;

        $pageParties = $parties ?? collect();
        $pageCourses = $courses ?? collect();

        $selectedParty = $selectedPartyId ?? null;
        $selectedCourse = $selectedCourseId ?? null;
        $q = $searchQuery ?? '';

        $userVotesMap = $userVotesByPosition ?? [];
    @endphp

    <!-- Position filter moved into the top filter form as a dropdown -->

    <div id="candidates-container">
    @if($pageCandidates->isEmpty())
     <div class="bg-white p-6 rounded-lg shadow-lg text-gray-600">No candidates available.</div>
    @else
      @foreach($pagePositions as $p)
       @php
            $positionCandidates = $candidatesByPositionId->get($p->id, collect());
       @endphp
       @if($positionCandidates->isNotEmpty())
        <div class="mb-6">
         <div class="flex items-center justify-between mb-3">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 border border-blue-200">
           <span class="text-sm font-bold text-blue-800">{{ $p->name }}</span>
           <span class="text-xs font-semibold text-blue-700 bg-white border border-blue-200 rounded-full px-2 py-0.5">{{ $positionCandidates->count() }}</span>
          </div>
         </div>

         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach($positionCandidates as $c)
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

       <div class="flip-card w-full text-left rounded-2xl shadow-lg overflow-hidden bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer" data-flip-card role="button" tabindex="0">
        <div class="flip-card-inner min-h-[380px]">
         <div class="flip-card-face flip-card-front">
          <div class="h-full">
           <div class="relative">
            <img src="{{ $bannerUrl }}" alt="{{ $c->name }} banner" class="w-full h-28 object-cover" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
            <div class="absolute -bottom-10 left-0 right-0 flex justify-center">
             <img src="{{ $profileUrl }}" alt="{{ $c->name }}" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
            </div>
           </div>

           <div class="pt-12 px-5 pb-5 text-center">
            <h4 class="text-lg font-bold text-gray-800">{{ $c->name }}</h4>

            <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
             <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-blue-50 text-blue-700 border border-blue-100">{{ $c->party ? $c->party->name : 'No Party' }}</span>
             <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-gray-50 text-gray-700 border border-gray-200">{{ optional($c->position)->name ?? 'N/A' }}</span>
             <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">{{ optional($c->course)->name ?? 'N/A' }}</span>
            </div>

            <div class="mt-3 flex justify-center">
             <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full {{ $c->status === 'Active' ? 'bg-green-100 text-green-800' : ($c->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $c->status }}</span>
            </div>

            @php
                $positionId = $c->position_id;
                $votedCandidateId = $positionId ? ($userVotesMap[$positionId] ?? null) : null;
                $hasVotedForPosition = !empty($votedCandidateId);
                $isUserVotedCandidate = $hasVotedForPosition && ((string)$votedCandidateId === (string)$c->id);
            @endphp

            <div class="mt-4 flex flex-wrap justify-center gap-2" data-no-flip>
             <button
              type="button"
              class="bg-white text-blue-700 border border-blue-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-50 view-profile-btn"
              data-no-flip
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
              data-candidate-joined-elections="{{ e($c->joined_elections ?? '') }}"
             >
              View Profile
             </button>
             @auth
              @php
                  $isVerifiedStudent = !auth()->user()->is_admin && auth()->user()->verifiedVoter()->exists();
              @endphp
              @if(!$positionId)
               <button type="button" class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-not-allowed" disabled>
                Vote Now
               </button>
              @elseif(!$isVerifiedStudent)
               <button type="button" class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-not-allowed" disabled title="Only verified students can vote">
                Verification Required
               </button>
              @elseif($isUserVotedCandidate)
               <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-not-allowed" disabled>
                Voted
               </button>
              @elseif($hasVotedForPosition)
               <button type="button" class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-not-allowed" disabled>
                Voted
               </button>
              @else
               <form method="POST" action="{{ route('vote.cast') }}" data-no-flip data-confirm-vote data-candidate-name="{{ $c->name }}">
                @csrf
                <input type="hidden" name="candidate_id" value="{{ $c->id }}">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700" data-no-flip>
                 Vote Now
                </button>
               </form>
              @endif
             @endauth
             @guest
              <a href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700" data-no-flip>
               Log in to Vote
              </a>
             @endguest
            </div>

            <p class="text-gray-500 text-xs mt-3">Click card to view platforms</p>
           </div>
          </div>
         </div>
         <div class="flip-card-face flip-card-back">
          <div class="p-4 h-full flex flex-col">
           <div class="text-center">
            <div class="text-base font-bold text-gray-800">Platforms</div>
            <div class="text-xs text-gray-500 mt-1">{{ $c->name }}</div>
           </div>

           <div class="mt-4 text-sm text-gray-700 overflow-auto border rounded-xl p-3 bg-gray-50 flex-1 platforms-content">{!! $c->platforms ?? '' !!}</div>

           <div class="mt-4 text-center text-xs text-gray-500">Click again to go back</div>
          </div>
         </div>
        </div>
       </div>
         @endforeach
        </div>
       </div>
       @endif
      @endforeach

      @php
            $otherCandidates = $candidatesByPositionId->get(0, collect());
      @endphp
      @if($otherCandidates->isNotEmpty())
       <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
         <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 border border-blue-200">
          <span class="text-sm font-bold text-blue-800">Other</span>
          <span class="text-xs font-semibold text-blue-700 bg-white border border-blue-200 rounded-full px-2 py-0.5">{{ $otherCandidates->count() }}</span>
         </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
         @foreach($otherCandidates as $c)
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

       <div class="flip-card w-full text-left rounded-2xl shadow-lg overflow-hidden bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer" data-flip-card role="button" tabindex="0">
        <div class="flip-card-inner min-h-[380px]">
         <div class="flip-card-face flip-card-front">
          <div class="h-full">
           <div class="relative">
            <img src="{{ $bannerUrl }}" alt="{{ $c->name }} banner" class="w-full h-28 object-cover" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';">
            <div class="absolute -bottom-10 left-0 right-0 flex justify-center">
             <img src="{{ $profileUrl }}" alt="{{ $c->name }}" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';">
            </div>
           </div>

           <div class="pt-12 px-5 pb-5 text-center">
            <h4 class="text-lg font-bold text-gray-800">{{ $c->name }}</h4>

            <div class="mt-3 flex flex-wrap items-center justify-center gap-2">
             <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-blue-50 text-blue-700 border border-blue-100">{{ $c->party ? $c->party->name : 'No Party' }}</span>
             <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-gray-50 text-gray-700 border border-gray-200">{{ optional($c->position)->name ?? 'N/A' }}</span>
             <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">{{ optional($c->course)->name ?? 'N/A' }}</span>
            </div>

            <div class="mt-3 flex justify-center">
             <span class="inline-flex items-center px-2.5 py-1 text-xs rounded-full {{ $c->status === 'Active' ? 'bg-green-100 text-green-800' : ($c->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ $c->status }}</span>
            </div>

            @php
                $positionId = $c->position_id;
                $votedCandidateId = $positionId ? ($userVotesMap[$positionId] ?? null) : null;
                $hasVotedForPosition = !empty($votedCandidateId);
                $isUserVotedCandidate = $hasVotedForPosition && ((string)$votedCandidateId === (string)$c->id);
            @endphp

            <div class="mt-4 flex flex-wrap justify-center gap-2" data-no-flip>
             <button
              type="button"
              class="bg-white text-blue-700 border border-blue-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-50 view-profile-btn"
              data-no-flip
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
              data-candidate-joined-elections="{{ e($c->joined_elections ?? '') }}"
             >
              View Profile
             </button>
             @auth
              @php
                  $isVerifiedStudent = !auth()->user()->is_admin && auth()->user()->verifiedVoter()->exists();
              @endphp
              @if(!$positionId)
               <button type="button" class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-not-allowed" disabled>
                Vote Now
               </button>
              @elseif(!$isVerifiedStudent)
               <button type="button" class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-not-allowed" disabled title="Only verified students can vote">
                Verification Required
               </button>
              @elseif($isUserVotedCandidate)
               <button type="button" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-semibold cursor-not-allowed" disabled>
                Voted
               </button>
              @elseif($hasVotedForPosition)
               <button type="button" class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold cursor-not-allowed" disabled>
                Voted
               </button>
              @else
               <form method="POST" action="{{ route('vote.cast') }}" data-no-flip data-confirm-vote data-candidate-name="{{ $c->name }}">
                @csrf
                <input type="hidden" name="candidate_id" value="{{ $c->id }}">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700" data-no-flip>
                 Vote Now
                </button>
               </form>
              @endif
             @endauth
             @guest
              <a href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700" data-no-flip>
               Log in to Vote
              </a>
             @endguest
            </div>

            <p class="text-gray-500 text-xs mt-3">Click card to view platforms</p>
           </div>
          </div>
         </div>
         <div class="flip-card-face flip-card-back">
          <div class="p-4 h-full flex flex-col">
           <div class="text-center">
            <div class="text-base font-bold text-gray-800">Platforms</div>
            <div class="text-xs text-gray-500 mt-1">{{ $c->name }}</div>
           </div>

           <div class="mt-4 text-sm text-gray-700 overflow-auto border rounded-xl p-3 bg-gray-50 flex-1 platforms-content">{!! $c->platforms ?? '' !!}</div>

           <div class="mt-4 text-center text-xs text-gray-500">Click again to go back</div>
          </div>
         </div>
        </div>
       </div>
         @endforeach
        </div>
       </div>
      @endif
    @endif
    </div>
   </section>
  </main>

  <div id="candidateProfileModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
   <div class="absolute inset-0 bg-black/60" data-candidate-modal-backdrop></div>
   <div class="relative mx-auto my-8 w-full max-w-5xl px-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
     <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
      <div class="text-lg font-bold text-gray-900" id="candidate-modal-title">Candidate Profile</div>
      <button type="button" class="text-gray-500 hover:text-gray-700" aria-label="Close" id="candidateProfileModalClose">
       <span class="text-2xl leading-none">&times;</span>
      </button>
     </div>

     <div class="max-h-[80vh] overflow-y-auto">
      <div class="relative">
       <img id="candidate-modal-banner" src="" alt="" class="w-full h-56 object-cover" />
       <div class="absolute -bottom-16 left-0 right-0 flex justify-center">
        <img id="candidate-modal-profile" src="" alt="" class="w-32 h-32 rounded-full object-cover border-8 border-white shadow" />
       </div>
      </div>

      <div class="pt-20 px-6 pb-6">
       <div class="text-center">
        <div class="text-2xl font-bold text-gray-900" id="candidate-modal-name"></div>
        <div class="mt-2 flex flex-wrap items-center justify-center gap-2">
         <span class="inline-flex items-center px-3 py-1 text-xs rounded-full bg-blue-50 text-blue-700 border border-blue-100" id="candidate-modal-party"></span>
         <span class="inline-flex items-center px-3 py-1 text-xs rounded-full bg-gray-50 text-gray-700 border border-gray-200" id="candidate-modal-position"></span>
         <span class="inline-flex items-center px-3 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100" id="candidate-modal-course"></span>
         <span class="inline-flex items-center px-3 py-1 text-xs rounded-full bg-green-100 text-green-800" id="candidate-modal-status"></span>
        </div>
       </div>

       <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
         <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
          <div class="text-sm font-bold text-gray-900 mb-2">About</div>
          <div class="text-sm text-gray-700 leading-relaxed" id="candidate-modal-description"></div>
         </div>
        </div>

        <div>
         <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
          <div class="text-sm font-bold text-gray-900 mb-2">Platforms</div>
          <div class="text-sm text-gray-700 leading-relaxed platforms-content" id="candidate-modal-platforms"></div>
         </div>
        </div>

        <div class="lg:col-span-2">
         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
           <div class="text-sm font-bold text-gray-900 mb-2">Related Events</div>
           <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1" id="candidate-modal-related-events"></ul>
          </div>

          <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
           <div class="text-sm font-bold text-gray-900 mb-2">Joined Elections</div>
           <ul class="list-disc pl-5 text-sm text-gray-700 space-y-1" id="candidate-modal-joined-elections"></ul>
          </div>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>

  <script>
        document.addEventListener('submit', (event) => {
            const form = event.target.closest('form[data-confirm-vote]');
            if (!form || form.dataset.confirmed === 'true') return;

            event.preventDefault();

            const candidateName = form.dataset.candidateName || 'this candidate';

            Swal.fire({
                icon: 'question',
                title: 'Confirm your vote',
                text: `Are you sure you want to vote for ${candidateName}?`,
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

        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        if (menuToggle && mobileMenu) {
            menuToggle.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-no-flip]')) return;
            const card = e.target.closest('[data-flip-card]');
            if (!card) return;
            card.classList.toggle('is-flipped');
        });

        document.addEventListener('keydown', (e) => {
            if (e.target.closest && e.target.closest('[data-no-flip]')) return;
            const card = e.target.closest && e.target.closest('[data-flip-card]');
            if (!card) return;
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                card.classList.toggle('is-flipped');
            }
        });

        const candidateModal = document.getElementById('candidateProfileModal');
        const candidateModalBackdrop = candidateModal ? candidateModal.querySelector('[data-candidate-modal-backdrop]') : null;
        const candidateModalClose = document.getElementById('candidateProfileModalClose');

        function setStatusBadge(el, status) {
            if (!el) return;
            const s = String(status || '').trim();
            el.textContent = s;
            el.classList.remove('bg-green-100', 'text-green-800', 'bg-yellow-100', 'text-yellow-800', 'bg-red-100', 'text-red-800', 'bg-gray-100', 'text-gray-800');
            if (s === 'Active') {
                el.classList.add('bg-green-100', 'text-green-800');
            } else if (s === 'Pending') {
                el.classList.add('bg-yellow-100', 'text-yellow-800');
            } else if (s) {
                el.classList.add('bg-red-100', 'text-red-800');
            } else {
                el.classList.add('bg-gray-100', 'text-gray-800');
            }
        }

        function parseList(raw) {
            const str = String(raw || '');
            return Array.from(
                new Set(
                    str
                        .split(/[\n,]+/)
                        .map(s => s.trim())
                        .filter(Boolean)
                )
            );
        }

        function fillList(ulEl, items, emptyText) {
            if (!ulEl) return;
            ulEl.innerHTML = '';
            if (!items || items.length === 0) {
                const li = document.createElement('li');
                li.className = 'list-none text-sm text-gray-500';
                li.textContent = emptyText;
                ulEl.appendChild(li);
                return;
            }
            items.forEach(t => {
                const li = document.createElement('li');
                li.textContent = t;
                ulEl.appendChild(li);
            });
        }

        function openCandidateModal(btn) {
            if (!candidateModal) return;

            const name = btn.getAttribute('data-candidate-name') || '';
            const party = btn.getAttribute('data-candidate-party') || '';
            const position = btn.getAttribute('data-candidate-position') || '';
            const course = btn.getAttribute('data-candidate-course') || '';
            const status = btn.getAttribute('data-candidate-status') || '';
            const profileUrl = btn.getAttribute('data-candidate-profile-url') || '';
            const bannerUrl = btn.getAttribute('data-candidate-banner-url') || '';
            const descriptionRaw = btn.getAttribute('data-candidate-description') || '';
            const platformsRaw = btn.getAttribute('data-candidate-platforms') || '';
            const relatedEventsRaw = btn.getAttribute('data-candidate-related-events') || '';
            const joinedElectionsRaw = btn.getAttribute('data-candidate-joined-elections') || '';

            const titleEl = document.getElementById('candidate-modal-title');
            const nameEl = document.getElementById('candidate-modal-name');
            const partyEl = document.getElementById('candidate-modal-party');
            const positionEl = document.getElementById('candidate-modal-position');
            const courseEl = document.getElementById('candidate-modal-course');
            const statusEl = document.getElementById('candidate-modal-status');
            const bannerEl = document.getElementById('candidate-modal-banner');
            const profileEl = document.getElementById('candidate-modal-profile');
            const descEl = document.getElementById('candidate-modal-description');
            const platformsEl = document.getElementById('candidate-modal-platforms');
            const relatedUl = document.getElementById('candidate-modal-related-events');
            const joinedUl = document.getElementById('candidate-modal-joined-elections');

            if (titleEl) titleEl.textContent = name ? `${name} - Profile` : 'Candidate Profile';
            if (nameEl) nameEl.textContent = name;
            if (partyEl) partyEl.textContent = party;
            if (positionEl) positionEl.textContent = position;
            if (courseEl) courseEl.textContent = course;
            setStatusBadge(statusEl, status);

            if (bannerEl) {
                bannerEl.src = bannerUrl;
                bannerEl.alt = name ? `${name} banner` : 'Banner';
                bannerEl.onerror = function () {
                    this.onerror = null;
                    this.src = '{{ asset('images/default-banner.png') }}';
                };
            }
            if (profileEl) {
                profileEl.src = profileUrl;
                profileEl.alt = name ? name : 'Profile';
                profileEl.onerror = function () {
                    this.onerror = null;
                    this.src = '{{ asset('images/default-profile.jpg') }}';
                };
            }

            let description = '';
            let platforms = '';
            try { description = JSON.parse(descriptionRaw) || ''; } catch (e) { description = descriptionRaw || ''; }
            try { platforms = JSON.parse(platformsRaw) || ''; } catch (e) { platforms = platformsRaw || ''; }

            if (descEl) descEl.innerHTML = description || '<div class="text-sm text-gray-500">No description available.</div>';
            if (platformsEl) platformsEl.innerHTML = platforms || '<div class="text-sm text-gray-500">No platforms available.</div>';

            const related = parseList(relatedEventsRaw);
            fillList(relatedUl, related, 'No related events available.');

            let joinedItems = [];
            const joinedRawStr = String(joinedElectionsRaw || '').trim();
            if (joinedRawStr) {
                try {
                    const parsed = JSON.parse(joinedRawStr);
                    if (Array.isArray(parsed)) {
                        joinedItems = parsed.map(je => {
                            const year = (je && typeof je === 'object') ? (je.year || '') : '';
                            const pos = (je && typeof je === 'object') ? (je.position || '') : '';
                            const win = (je && typeof je === 'object') ? (je.winner ? true : false) : false;
                            const label = String([year, pos].filter(Boolean).join(' - ')).trim();
                            return label ? (win ? `${label} (Winner)` : label) : '';
                        }).filter(Boolean);
                    }
                } catch (e) {
                    joinedItems = parseList(joinedRawStr);
                }
            }
            fillList(joinedUl, joinedItems, 'No election history available.');

            candidateModal.classList.remove('hidden');
            candidateModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
        }

        function closeCandidateModal() {
            if (!candidateModal) return;
            candidateModal.classList.add('hidden');
            candidateModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
        }

        document.querySelectorAll('.view-profile-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                openCandidateModal(btn);
            });
        });

        if (candidateModalBackdrop) candidateModalBackdrop.addEventListener('click', closeCandidateModal);
        if (candidateModalClose) candidateModalClose.addEventListener('click', closeCandidateModal);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && candidateModal && !candidateModal.classList.contains('hidden')) {
                closeCandidateModal();
            }
        });

        // AJAX filter: submit form, fetch updated candidates list and replace container
        (function(){
            const form = document.getElementById('filter-form');
            const container = document.getElementById('candidates-container');
            if (!form || !container) return;

            function refreshCandidates() {
                const formData = new FormData(form);
                const params = new URLSearchParams();
                for (const [k,v] of formData.entries()) {
                    const value = String(v).trim();
                    if (value !== '') params.append(k, value);
                }
                const url = form.action + (params.toString() ? ('?' + params.toString()) : '');

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(r => r.text())
                    .then(html => {
                        try {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newContainer = doc.querySelector('#candidates-container');
                            if (newContainer) {
                                container.innerHTML = newContainer.innerHTML;
                            } else {
                                container.innerHTML = html;
                            }

                            document.querySelectorAll('.view-profile-btn').forEach(btn => {
                                btn.addEventListener('click', function(ev){ ev.preventDefault(); openCandidateModal(this); });
                            });

                        } catch (ex) {
                            console.error('Failed to parse filter response', ex);
                        }
                    })
                    .catch(err => console.error('Filter fetch failed', err));
            }

            form.addEventListener('submit', function(e){
                e.preventDefault();
                refreshCandidates();
            });

            const searchInput = form.querySelector('input[name="q"]');
            let debounceTimer = null;

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    window.clearTimeout(debounceTimer);
                    debounceTimer = window.setTimeout(function () {
                        refreshCandidates();
                    }, 200);
                });
            }

            form.querySelectorAll('select[name]').forEach(function (select) {
                select.addEventListener('change', function () {
                    refreshCandidates();
                });
            });
        })();
  </script>
 </body>
</html>

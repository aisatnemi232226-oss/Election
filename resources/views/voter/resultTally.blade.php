<html>
 <head>
  <title>
   Philippine Christian University Online Election System - Presidential Election Results
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
   body {
            font-family: 'Roboto', sans-serif;
        }
        .tile {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .tile:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .tile:active {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        .no-wrap {
            white-space: nowrap;
        }
  </style>
 </head>
 <body class="relative bg-gray-100">
  <div class="pointer-events-none fixed inset-0 flex items-center justify-center opacity-30 z-0" aria-hidden="true">
   <img src="{{ asset('images/pculogo.png') }}" alt="" class="w-[62vw] max-w-[820px] h-auto" />
  </div>
  <header class="relative z-50 bg-blue-600 text-white p-4">
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
   <section class="text-center my-8">
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
    @endphp

    @if($selectedTileUrl)
     <div class="max-w-md mx-auto mb-4">
      <img src="{{ $selectedTileUrl }}" alt="{{ $selectedPosition->name }}" class="w-full h-40 object-cover rounded-xl shadow" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';" />
     </div>
    @endif

    <h2 class="text-4xl font-bold text-gray-800 mb-4">
     {{ $selectedPosition ? ($selectedPosition->name . ' Results') : 'Election Results' }}
    </h2>
    <p class="text-gray-600 text-lg">
     View the detailed results of the election.
    </p>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-3xl font-bold text-gray-800 mb-4">
      Candidate Tally
     </h3>
     @php
        $groups = $candidatesByPositionId ?? collect();
        $selectedId = $selectedPositionId ? (int) $selectedPositionId : null;
     @endphp

     @if($selectedId)
      @php
        $posCandidates = $groups->get($selectedId, collect());
        $posTotalVotes = (int) ($votesTotalByPositionId[$selectedId] ?? 0);
      @endphp

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
       @forelse($posCandidates as $c)
        @php
            $profileRelative = $c->profile_img
                ? (\Illuminate\Support\Str::contains($c->profile_img, '/') ? $c->profile_img : ('profile-picture/' . $c->profile_img))
                : null;

            $profileExists = $profileRelative
                ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $profileRelative)
                : false;

            $profileUrl = $profileExists
                ? route('storage.image', ['folder' => 'upload', 'filename' => $profileRelative])
                : asset('images/default-profile.jpg');

            $votesCount = (int) ($c->votes_count ?? 0);
            $pct = $posTotalVotes > 0 ? round(($votesCount / $posTotalVotes) * 100, 2) : 0;
        @endphp

        <div class="bg-gray-100 p-4 rounded-lg shadow-md">
         <div class="flex items-center mb-4">
          <img alt="{{ $c->name }} profile picture" class="w-16 h-16 rounded-full mr-4 object-cover" height="100" src="{{ $profileUrl }}" width="100" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';"/>
          <div>
           <h4 class="text-xl font-bold">
            {{ $c->name }}
           </h4>
           <p class="text-gray-600">
            {{ $votesCount }} Votes
           </p>
          </div>
         </div>
         <div class="relative pt-1">
          <div class="flex mb-2 items-center justify-between">
           <div>
            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
             {{ $pct }}%
            </span>
           </div>
          </div>
          <div class="overflow-hidden h-4 mb-4 text-xs flex rounded bg-blue-200">
           <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600" style="width:{{ $pct }}%"></div>
          </div>
         </div>
        </div>
       @empty
        <div class="col-span-full text-center text-gray-600">No candidates found for this position.</div>
       @endforelse
      </div>
     @else
      @forelse(($positions ?? collect()) as $pos)
       @php
        $posCandidates = $groups->get($pos->id, collect());
        $posTotalVotes = (int) ($votesTotalByPositionId[$pos->id] ?? 0);

        $posTileUrl = null;
        if (!empty($pos->tile_img)) {
            $rawPosTile = $pos->tile_img;
            if (\Illuminate\Support\Str::startsWith($rawPosTile, 'upload/')) {
                $rawPosTile = substr($rawPosTile, strlen('upload/'));
            }
            $posTileRelative = \Illuminate\Support\Str::contains($rawPosTile, '/')
                ? $rawPosTile
                : ('position-tiles/' . $rawPosTile);
            $posTileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $posTileRelative);
            $posTileUrl = $posTileExists
                ? route('storage.image', ['folder' => 'upload', 'filename' => $posTileRelative])
                : asset('images/default-banner.png');
        }
       @endphp

       @if($posCandidates->isNotEmpty())
        <div class="mb-6">
         <div class="flex items-center justify-between mb-3">
          <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 border border-blue-200">
           <span class="text-sm font-bold text-blue-800">{{ $pos->name }}</span>
           <span class="text-xs font-semibold text-blue-700 bg-white border border-blue-200 rounded-full px-2 py-0.5">{{ $posCandidates->count() }}</span>
          </div>
         </div>

         @if($posTileUrl)
          <div class="mb-4">
           <img src="{{ $posTileUrl }}" alt="{{ $pos->name }}" class="w-full h-40 object-cover rounded-xl shadow" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';" />
          </div>
         @endif

         <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          @foreach($posCandidates as $c)
           @php
            $profileRelative = $c->profile_img
                ? (\Illuminate\Support\Str::contains($c->profile_img, '/') ? $c->profile_img : ('profile-picture/' . $c->profile_img))
                : null;

            $profileExists = $profileRelative
                ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $profileRelative)
                : false;

            $profileUrl = $profileExists
                ? route('storage.image', ['folder' => 'upload', 'filename' => $profileRelative])
                : asset('images/default-profile.jpg');

            $votesCount = (int) ($c->votes_count ?? 0);
            $pct = $posTotalVotes > 0 ? round(($votesCount / $posTotalVotes) * 100, 2) : 0;
           @endphp

           <div class="bg-gray-100 p-4 rounded-lg shadow-md">
            <div class="flex items-center mb-4">
             <img alt="{{ $c->name }} profile picture" class="w-16 h-16 rounded-full mr-4 object-cover" height="100" src="{{ $profileUrl }}" width="100" onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';"/>
             <div>
              <h4 class="text-xl font-bold">
               {{ $c->name }}
              </h4>
              <p class="text-gray-600">
               {{ $votesCount }} Votes
              </p>
             </div>
            </div>
            <div class="relative pt-1">
             <div class="flex mb-2 items-center justify-between">
              <div>
               <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                {{ $pct }}%
               </span>
              </div>
             </div>
             <div class="overflow-hidden h-4 mb-4 text-xs flex rounded bg-blue-200">
              <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600" style="width:{{ $pct }}%"></div>
             </div>
            </div>
           </div>
          @endforeach
         </div>
        </div>
       @endif
      @empty
      @endforelse
     @endif
    </div>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-3xl font-bold text-gray-800 mb-4">
      Election Party Tally
     </h3>
     <div class="flex justify-center">
      <div class="w-72 h-72 md:w-80 md:h-80">
       <canvas id="partyPieChart"></canvas>
      </div>
     </div>
    </div>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-3xl font-bold text-gray-800 mb-4">
      Course with the Largest Voters
     </h3>
     <div class="overflow-x-auto">
      <table class="min-w-full bg-white">
       <thead>
        <tr>
         <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold w-1/2">
          Course Name
         </th>
         <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">
          Enrolled Students
         </th>
         <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">
          Registered Voters
         </th>
         <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">
          Voted
         </th>
        </tr>
       </thead>
       <tbody>
        @php
         $topCourses = ($courseStats ?? collect())->take(5);
        @endphp

        @forelse($topCourses as $row)
         <tr>
          <td class="py-2 px-4 border-b border-gray-300">
           {{ $row['course_name'] ?? 'N/A' }}
          </td>
          <td class="py-2 px-4 border-b border-gray-300">
           {{ (int) ($row['enrolled'] ?? 0) }}
          </td>
          <td class="py-2 px-4 border-b border-gray-300">
           {{ (int) ($row['registered'] ?? 0) }}
          </td>
          <td class="py-2 px-4 border-b border-gray-300">
           {{ (int) ($row['voted'] ?? 0) }}
          </td>
         </tr>
        @empty
         <tr>
          <td class="py-3 px-4 border-b border-gray-300 text-center text-gray-600" colspan="4">
           No voter course statistics available.
          </td>
         </tr>
        @endforelse
       </tbody>
      </table>
     </div>
    </div>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-3xl font-bold text-gray-800 mb-4">
      Voter Demographics
     </h3>
     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-gray-100 p-4 rounded-lg shadow-md">
       <h4 class="text-xl font-bold mb-2">
        Gender Distribution
       </h4>
       @php
        $malePct = null;
        $femalePct = null;
        if (!empty($genderStats) && isset($genderStats['male_pct'], $genderStats['female_pct'])) {
            $malePct = $genderStats['male_pct'];
            $femalePct = $genderStats['female_pct'];
        }
       @endphp
       <div class="flex items-center mb-4">
        <img alt="Icon representing male gender" class="w-10 h-10 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/hGasLb9WzgqdHdu9Yt96XMEihM6NCLlpe5BZqsKoQNCC2XDKA.jpg" width="100"/>
        <div>
         <p class="text-gray-600">
          Male: {{ $malePct !== null ? ($malePct . '%') : 'N/A' }}
         </p>
        </div>
       </div>
       <div class="flex items-center">
        <img alt="Icon representing female gender" class="w-10 h-10 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/a3Ho08UEFf0jeEklwfr5cuU4SXAEN5gwPMjIogsMltQSYfaQB.jpg" width="100"/>
        <div>
         <p class="text-gray-600">
          Female: {{ $femalePct !== null ? ($femalePct . '%') : 'N/A' }}
         </p>
        </div>
       </div>
      </div>
      <div class="bg-gray-100 p-4 rounded-lg shadow-md">
       <h4 class="text-xl font-bold mb-2">
        Voter Turnout by Year
       </h4>
       @php
        $turnoutRows = ($yearLevelTurnout ?? collect())->take(5);
       @endphp

       @forelse($turnoutRows as $row)
        <div class="flex items-center mb-4">
         <img alt="Icon" class="w-10 h-10 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/AubbQxjf4lVHeU1EEPFIgfE05uX1IRLG7rBZ2ivmilXKYfaQB.jpg" width="100"/>
         <div>
          <p class="text-gray-600">
           {{ $row['year_name'] ?? 'N/A' }}: {{ ($row['pct'] ?? 0) }}%
          </p>
         </div>
        </div>
       @empty
        <div class="text-gray-600">No turnout statistics available.</div>
       @endforelse
      </div>
     </div>
    </div>
   </section>
  </main>
    <footer class="relative z-10 bg-blue-600 text-white p-4 text-center">
     <p>
        &copy; 2026 Philippine Christian University Online Election System. All rights reserved.
     </p>
    </footer>
  <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden" id="login-modal">
   <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">
     Log In
    </h2>
    <form>
     <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="login-email">
       Email
      </label>
      <input class="w-full p-3 border border-gray-300 rounded-lg" id="login-email" name="login-email" required="" type="email"/>
     </div>
     <div class="mb-4">
      <label class="block text-gray-700 font-bold mb-2" for="login-password">
       Password
      </label>
      <input class="w-full p-3 border border-gray-300 rounded-lg" id="login-password" name="login-password" required="" type="password"/>
     </div>
     <div class="text-center">
      <button class="bg-blue-600 text-white p-3 rounded-lg cursor-pointer hover:bg-blue-700" type="submit">
       Log In
      </button>
     </div>
    </form>
   </div>
  </div>
  <script>
   const loginLink = document.querySelector('a[href="login"]');
        if (loginLink) {
            loginLink.addEventListener('click', function(event) {
                event.preventDefault();
                document.getElementById('login-modal').classList.remove('hidden');
            });
        }

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

        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        const pieEl = document.getElementById('partyPieChart');
        if (pieEl) {
            const labels = @json($partyTallyLabels ?? []);
            const data = @json($partyTallyData ?? []);

            const safeLabels = (Array.isArray(labels) && labels.length) ? labels : ['No data'];
            const safeData = (Array.isArray(data) && data.length) ? data : [1];

            const bg = ['#1E3A8A', '#10B981', '#EF4444', '#F59E0B', '#8B5CF6', '#14B8A6', '#EC4899', '#64748B'];

            const ctx = pieEl.getContext('2d');
            const partyPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: safeLabels,
                    datasets: [{
                        data: safeData,
                        backgroundColor: safeLabels.map((_, i) => bg[i % bg.length]),
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 10,
                                font: { size: 11 }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        }
  </script>
 </body>
</html>
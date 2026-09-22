<html>
 <head>
  <title>
   Philippine Christian University Online Election System - Results
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet"/>
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
       <a class="hover:underline" href="{{ url('/vote') }}">
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
    <h2 class="text-4xl font-bold text-gray-800 mb-4">
     Election Results
    </h2>
    <p class="text-gray-600 text-lg">
     View the results of the recent elections.
    </p>
   </section>
   <section class="my-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse(($positions ?? collect()) as $position)
     @php
        $tileRelative = null;
        if (!empty($position->tile_img)) {
            $rawTile = $position->tile_img;
            if (\Illuminate\Support\Str::startsWith($rawTile, 'upload/')) {
                $rawTile = substr($rawTile, strlen('upload/'));
            }
            $tileRelative = \Illuminate\Support\Str::contains($rawTile, '/')
                ? $rawTile
                : ('position-tiles/' . $rawTile);
        }
        $tileExists = $tileRelative ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $tileRelative) : false;
        $tileUrl = $tileExists
            ? route('storage.image', ['folder' => 'upload', 'filename' => $tileRelative])
            : asset('images/default-banner.png');
     @endphp
     <a href="{{ route('resultTally', ['position' => $position->id]) }}" class="block">
      <div class="bg-white p-6 rounded-lg shadow-lg text-center tile flex flex-col h-full">
       <div class="w-full h-40 rounded-lg bg-gray-100 mb-4 overflow-hidden flex items-center justify-center">
        <img alt="Result for {{ $position->name }}" class="w-full h-full object-contain" src="{{ $tileUrl }}" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';"/>
       </div>
       <h4 class="text-2xl font-bold text-gray-800 mb-2 mt-auto">
        {{ $position->name }}
       </h4>
      </div>
     </a>
    @empty
    @endforelse

    <a href="{{ route('resultTally') }}" class="block">
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile flex flex-col h-full">
      <div class="w-full h-40 rounded-lg bg-gray-100 mb-4 overflow-hidden flex items-center justify-center">
       <img alt="Result for all positions" class="w-full h-full object-contain" src="{{ asset('images/default-banner.png') }}" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';"/>
      </div>
      <h4 class="text-2xl font-bold text-gray-800 mb-2 mt-auto">
       All Positions
      </h4>
     </div>
    </a>
    </div>
   </section>
   <section class="my-8">
    <h3 class="text-3xl font-bold text-gray-800 mb-4">
     Poll Results
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile flex flex-col h-full">
      <div class="w-full h-40 rounded-lg bg-gray-100 mb-4 overflow-hidden flex items-center justify-center">
       <img alt="Result for Top President Candidate" class="w-full h-full object-contain" src="https://storage.googleapis.com/a1aa/image/mPeWnsgHfAgSZklazzsc6QqvNiE7X1Vesfe7wxAuCTt1W41gC.jpg" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';" />
      </div>
      <h4 class="text-2xl font-bold text-gray-800 mb-2 mt-auto">
       Top President Candidate
      </h4>
      <p class="text-gray-600">
       Most Voted: {{ data_get($pollCards, 'president.name', '—') }}
      </p>
      <p class="text-gray-600">
       Votes: {{ number_format((int) data_get($pollCards, 'president.votes', 0)) }}
      </p>
     </div>
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile flex flex-col h-full">
      <div class="w-full h-40 rounded-lg bg-gray-100 mb-4 overflow-hidden flex items-center justify-center">
       <img alt="Result for Top Vice President Candidate" class="w-full h-full object-contain" src="https://storage.googleapis.com/a1aa/image/0ifxZa28k00Wf0ODn1oBGrdqGJq8eG7PE4BYtp6Zi7gqFeaQB.jpg" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';" />
      </div>
      <h4 class="text-2xl font-bold text-gray-800 mb-2 mt-auto">
       Top Vice President Candidate
      </h4>
      <p class="text-gray-600">
       Most Voted: {{ data_get($pollCards, 'vice_president.name', '—') }}
      </p>
      <p class="text-gray-600">
       Votes: {{ number_format((int) data_get($pollCards, 'vice_president.votes', 0)) }}
      </p>
     </div>
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile flex flex-col h-full">
      <div class="w-full h-40 rounded-lg bg-gray-100 mb-4 overflow-hidden flex items-center justify-center">
       <img alt="Result for Top Candidate Overall" class="w-full h-full object-contain" src="https://storage.googleapis.com/a1aa/image/vmrKfoRjlKyFYqXU6yTQK9KsY9cnWIxMmIfpMKEO0hfkFeaQB.jpg" onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';" />
      </div>
      <h4 class="text-2xl font-bold text-gray-800 mb-2 mt-auto">
       Top Candidate (All Positions)
      </h4>
      <p class="text-gray-600">
       Most Voted: {{ data_get($pollCards, 'overall.name', '—') }}
      </p>
      <p class="text-gray-600">
       Votes: {{ number_format((int) data_get($pollCards, 'overall.votes', 0)) }}
      </p>
     </div>
    </div>
   </section>
  </main>
    <footer class="relative z-10 bg-blue-600 text-white p-4 text-center">
     <p>
        2026 Philippine Christian University Online Election System. All rights reserved.
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
  </script>
 </body>
</html>
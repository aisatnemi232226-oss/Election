<html>
 <head>
  <title>
   Philippine Christian University Online Election System
  </title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet"/>
  <link rel="icon" href="{{ URL::to('/images/pculogo.png') }}">
  <style>
   body {
            font-family: 'Roboto', sans-serif;
        }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
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
            Philippine Christian University Online Election System
        </h1>
    </a>
    <div class="flex items-center gap-2 md:hidden">
     @auth
      @php
       $studentVerificationUnreadCount = auth()->user()
        ->unreadNotifications()
        ->where('type', \App\Notifications\StudentVoterVerified::class)
        ->count();
      @endphp
      <button
       type="button"
       class="relative inline-flex h-10 w-10 items-center justify-center rounded-full text-white transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white"
       aria-label="Notifications"
       aria-haspopup="true"
       aria-expanded="false"
       data-student-notification-toggle
       data-list-url="{{ route('student.notifications.index') }}"
       data-read-url="{{ route('student.notifications.read') }}"
      >
       <i class="fas fa-bell" aria-hidden="true"></i>
       <span data-student-notification-badge class="{{ $studentVerificationUnreadCount > 0 ? '' : 'hidden' }} absolute -right-1 -top-1 inline-flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-bold text-white">
        {{ $studentVerificationUnreadCount > 99 ? '99+' : $studentVerificationUnreadCount }}
       </span>
      </button>
     @endauth
     <button class="text-white" id="menu-toggle" aria-label="Open navigation menu" aria-expanded="false" aria-controls="menu">
      <i class="fas fa-bars text-2xl" aria-hidden="true"></i>
     </button>
    </div>
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
      @include('voter.partials.verification-notifications')
      @php
       $isVerifiedStudent = !Auth::user()->is_admin && Auth::user()->isVerifiedVoter();
       $verificationPending = Auth::user()->voter_verification_submitted_at !== null;
      @endphp
      <li class="relative flex items-center">
       <button id="profile-toggle" type="button" class="group inline-flex h-12 max-w-full items-center gap-2.5 rounded-full border border-white/30 bg-white/15 py-1 pl-1 pr-3 text-left text-white transition-colors hover:bg-white/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-blue-600" aria-controls="profile-menu" aria-expanded="false" aria-label="Open profile menu">
        <span class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-blue-700 shadow-sm">
         <i class="fas fa-user" aria-hidden="true"></i>
         <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-blue-600 {{ Auth::user()->is_admin ? 'bg-blue-300' : ($isVerifiedStudent ? 'bg-green-400' : 'bg-red-400') }}" aria-hidden="true"></span>
        </span>
        <span class="hidden min-w-0 items-center gap-2 md:flex">
         <span class="max-w-28 truncate text-sm font-semibold">{{ Auth::user()->first_name ?: \Illuminate\Support\Str::before((string) Auth::user()->name, ' ') ?: 'Profile' }}</span>
         @unless(Auth::user()->is_admin)
         <span role="status" aria-label="{{ $isVerifiedStudent ? 'Verified' : 'Not verified' }}" class="inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-1 text-[10px] font-semibold leading-none {{ $isVerifiedStudent ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
          <i class="fas {{ $isVerifiedStudent ? 'fa-check' : 'fa-times' }}" aria-hidden="true"></i>
          Verified
         </span>
         @endunless
        </span>
        <i class="fas fa-chevron-down hidden shrink-0 text-[10px] text-blue-100 transition-transform md:block" aria-hidden="true" data-profile-chevron></i>
       </button>
       <div id="profile-menu" class="pointer-events-auto absolute right-0 top-full z-50 mt-2 hidden w-[300px] max-w-[calc(100vw-24px)] overflow-hidden rounded-2xl bg-white text-gray-800 shadow-[0_12px_32px_rgba(15,23,42,0.18),0_2px_6px_rgba(15,23,42,0.08)]">
        <div class="border-b border-gray-100 px-5 pb-4 pt-5">
         <div class="flex items-center gap-3">
          <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-100">
           <i class="fas fa-user" aria-hidden="true"></i>
          </span>
          <div class="min-w-0">
           <p class="truncate text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
           <p class="mt-0.5 truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
          </div>
         </div>
         @unless(Auth::user()->is_admin)
         <div class="mt-4 flex items-center justify-between gap-3 text-xs">
          <span class="text-gray-500">Student No: <strong class="font-semibold text-gray-700">{{ Auth::user()->idnumber }}</strong></span>
          <span role="status" class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 font-semibold {{ $isVerifiedStudent ? 'bg-green-50 text-green-700 ring-1 ring-green-200' : 'bg-red-50 text-red-700 ring-1 ring-red-200' }}">
           <i class="fas {{ $isVerifiedStudent ? 'fa-check' : 'fa-times' }}" aria-hidden="true"></i>
           {{ $isVerifiedStudent ? 'Verified' : 'Not verified' }}
          </span>
         </div>
         @endunless
        </div>
        @if(!Auth::user()->is_admin && !$isVerifiedStudent)
        <div class="mx-3 my-3 rounded-xl border px-3.5 py-3 {{ $verificationPending ? 'border-amber-200 bg-amber-50' : 'border-blue-100 bg-blue-50' }}">
         <p class="text-xs leading-relaxed {{ $verificationPending ? 'text-amber-900' : 'text-blue-900' }}">
          {{ $verificationPending ? 'Your voter verification application is awaiting review.' : 'Verify your account to vote in elections.' }}
         </p>
         <a href="{{ route('register.verify') }}" class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold {{ $verificationPending ? 'text-amber-800 hover:text-amber-950' : 'text-blue-700 hover:text-blue-900' }} focus:outline-none focus-visible:underline">
          {{ $verificationPending ? 'View application' : 'Verify now' }}
          <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
         </a>
        </div>
        @endif
        <div class="px-2 pb-2">
         <a href="{{ url('/tickets') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
          <i class="fas fa-ticket-alt w-4 text-center text-gray-400" aria-hidden="true"></i>
          Tickets
         </a>
         <a href="{{ route('account.qr-codes') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
          <i class="fas fa-qrcode w-4 text-center text-gray-400" aria-hidden="true"></i>
          Account QR codes
         </a>
        </div>
        <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout') }}" class="border-t border-gray-100 p-2">
         @csrf
         <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-red-600 transition-colors hover:bg-red-50 focus:bg-red-50 focus:outline-none">
          <i class="fas fa-sign-out-alt w-4 text-center" aria-hidden="true"></i>
          Log out
         </button>
        </form>
       </div>
      </li>
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
       @unless(Auth::user()->is_admin)
       <span role="status" class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $isVerifiedStudent ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
        <i class="fas {{ $isVerifiedStudent ? 'fa-check' : 'fa-times' }}" aria-hidden="true"></i>
        {{ $isVerifiedStudent ? 'Verified' : 'Not Verified' }}
       </span>
       @endunless
       <span class="text-xs text-gray-200">{{ Auth::user()->email }}</span>
        <a href="{{ route('account.qr-codes') }}" class="text-sm text-white underline">Account QR codes</a>
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
   <section class="text-center my-8">
    <h2 class="text-4xl font-bold text-gray-800 mb-4">
     Welcome{{ auth()->check() ? ', ' . (auth()->user()->first_name ?: \Illuminate\Support\Str::before((string) auth()->user()->name, ' ')) : '' }} to Philippine Christian University Online Election System
    </h2>
    <p class="text-gray-600 text-lg">
     Participate in the democratic process and vote for your student representatives.
    </p>
   </section>

   <section class="my-8">
    <h3 class="text-3xl font-bold text-gray-800 mb-4">
     How It Works
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-stretch">
    <a href="{{ route('register.options') }}" class="block h-full">
     <div class="bg-white p-6 rounded-lg shadow-lg text-center transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl h-full flex flex-col justify-center">
      <i class="fas fa-user-graduate text-blue-600 text-4xl mb-4">
      </i>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Register
      </h4>
      <p class="text-gray-600">
       Choose candidate registration or voter registration.
      </p>
     </div>
    </a>
    <a href="{{ route('election.details') }}" class="block h-full">
     <div class="bg-white p-6 rounded-lg shadow-lg text-center transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl h-full flex flex-col justify-center">
      <i class="fas fa-vote-yea text-blue-600 text-4xl mb-4">
      </i>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Vote
      </h4>
      <p class="text-gray-600">
       Cast your vote for your preferred candidates online.
      </p>
     </div>
    </a>
    <a href="{{ url('/result') }}" class="block h-full">
     <div class="bg-white p-6 rounded-lg shadow-lg text-center transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl h-full flex flex-col justify-center">
      <i class="fas fa-chart-bar text-blue-600 text-4xl mb-4">
      </i>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Results
      </h4>
      <p class="text-gray-600">
       View the election results once the voting period is over.
      </p>
     </div>
    </a>
    </div>
   </div>
  </section>
   <section class="my-8">
    @php
        $defaultEventsLabel = 'Election 2026 for CSG';
        $eventsLabel = $eventSettings?->label ?? $defaultEventsLabel;

        $labelImageRelative = $eventSettings?->label_image;
        $labelImageExists = $labelImageRelative ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $labelImageRelative) : false;
        $labelImageUrl = $labelImageExists
            ? route('storage.image', ['folder' => 'upload', 'filename' => $labelImageRelative])
            : null;

        $defaultEvents = [
            1 => [
                'title' => 'Election Kickoff',
                'date' => 'March 1, 2023',
                'description' => 'Join us for the official start of the election season with speeches from candidates and more.',
                'image' => 'https://storage.googleapis.com/a1aa/image/dvCf7rv3J9wNaqenfCddb8170ZkT2UD6ivYRPVt79lD3ezaQB.jpg',
            ],
            2 => [
                'title' => 'Candidate Debate',
                'date' => 'March 15, 2023',
                'description' => 'Watch the candidates debate on key issues and present their platforms.',
                'image' => 'https://storage.googleapis.com/a1aa/image/4ankqrk3osKqB9DQfnuXoqcnqPtRvVpXvf2A5JDQWeZ9ezaQB.jpg',
            ],
            3 => [
                'title' => 'Voting Day',
                'date' => 'April 1, 2023',
                'description' => 'Cast your vote online and make your voice heard in the student government elections.',
                'image' => 'https://storage.googleapis.com/a1aa/image/2ZcIUq4TX0axBpyzxe6zBIm7EI5K3w8fnLKi5Py79RXgfZNoA.jpg',
            ],
        ];

        $events = [];
        for ($i = 1; $i <= 3; $i++) {
            $title = $eventSettings?->{"event_{$i}_title"} ?? $defaultEvents[$i]['title'];
            $date = $eventSettings?->{"event_{$i}_date"} ?? $defaultEvents[$i]['date'];
            $description = $eventSettings?->{"event_{$i}_description"} ?? $defaultEvents[$i]['description'];

            $relative = $eventSettings?->{"event_{$i}_image"};
            $exists = $relative ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $relative) : false;
            $image = $exists
                ? route('storage.image', ['folder' => 'upload', 'filename' => $relative])
                : $defaultEvents[$i]['image'];

            $events[$i] = compact('title', 'date', 'description', 'image');
        }
    @endphp
    <div class="flex justify-between items-center mb-4">
     <div>
      <h3 class="text-3xl font-bold text-gray-800">
       Election Events
      </h3>
     </div>
     <div>
      <button class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700" id="prev-btn">
       <i class="fas fa-chevron-left">
       </i>
      </button>
      <button class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700" id="next-btn">
       <i class="fas fa-chevron-right">
       </i>
      </button>
     </div>
    </div>
    <div class="mb-4">
     <a href="{{ route('election.details') }}" class="block w-full bg-white border border-gray-200 rounded-lg shadow px-6 py-6 text-left hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      <div class="flex flex-col items-center justify-center gap-4">
       @if($labelImageUrl)
       <div class="w-full h-24 md:h-72 rounded-2xl overflow-hidden 
            bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 
            flex items-center px-6 md:px-16">

  <!-- USG Logo Text -->
  <div class="flex items-center gap-4 md:gap-8">
    <h1 class="text-white font-extrabold tracking-widest 
               text-4xl md:text-8xl drop-shadow-lg">
      USG
    </h1>

    <!-- Text Content -->
    <div class="text-white leading-tight">
      <p class="text-sm md:text-3xl font-semibold tracking-wide">
        University Student Government PCU 2026
      </p>
      <p class="text-xs md:text-xl opacity-90">
        2025 – 2026
      </p>
    </div>
  </div>

</div>
       @endif
       <div class="text-center w-full">
        <div class="text-xs font-semibold text-gray-700">Election Details</div>
        <div class="mt-1 text-gray-800 font-bold text-base md:text-lg">{{ $eventsLabel }}</div>
       </div>
      </div>
     </a>
    </div>
    <div class="relative">
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="events-container">
      <button type="button" class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl text-left" data-event-card data-event-title="{{ $events[1]['title'] }}" data-event-date="{{ $events[1]['date'] }}" data-event-image="{{ $events[1]['image'] }}" data-event-description="{{ $events[1]['description'] }}">
       <img alt="Event 1: {{ $events[1]['title'] }}" class="rounded-lg mb-4" height="400" src="{{ $events[1]['image'] }}" width="600"/>
       <h4 class="text-2xl font-bold text-gray-800 mb-2">
        {{ $events[1]['title'] }}
       </h4>
       <p class="text-gray-600">
        Date: {{ $events[1]['date'] }}
       </p>
       <p class="text-gray-600">
        {{ $events[1]['description'] }}
       </p>
      </button>
      <button type="button" class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl text-left" data-event-card data-event-title="{{ $events[2]['title'] }}" data-event-date="{{ $events[2]['date'] }}" data-event-image="{{ $events[2]['image'] }}" data-event-description="{{ $events[2]['description'] }}">
       <img alt="Event 2: {{ $events[2]['title'] }}" class="rounded-lg mb-4" height="400" src="{{ $events[2]['image'] }}" width="600"/>
       <h4 class="text-2xl font-bold text-gray-800 mb-2">
        {{ $events[2]['title'] }}
       </h4>
       <p class="text-gray-600">
        Date: {{ $events[2]['date'] }}
       </p>
       <p class="text-gray-600">
        {{ $events[2]['description'] }}
       </p>
      </button>
      <button type="button" class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl text-left" data-event-card data-event-title="{{ $events[3]['title'] }}" data-event-date="{{ $events[3]['date'] }}" data-event-image="{{ $events[3]['image'] }}" data-event-description="{{ $events[3]['description'] }}">
       <img alt="Event 3: {{ $events[3]['title'] }}" class="rounded-lg mb-4" height="400" src="{{ $events[3]['image'] }}" width="600"/>
       <h4 class="text-2xl font-bold text-gray-800 mb-2">
        {{ $events[3]['title'] }}
       </h4>
       <p class="text-gray-600">
        Date: {{ $events[3]['date'] }}
       </p>
       <p class="text-gray-600">
        {{ $events[3]['description'] }}
       </p>
      </button>
     </div>
    </div>
   </div>
  </section>
   <section class="my-8">
    <h3 class="text-3xl font-bold text-gray-800 mb-4">
     Important Dates
    </h3>
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <ul class="list-disc pl-5 text-gray-600">
      <li>
       Registration Deadline: February 25, 2023
      </li>
      <li>
       Campaign Period: March 1 - March 31, 2023
      </li>
      <li>
       Voting Period: April 1 - April 7, 2023
      </li>
      <li>
       Results Announcement: April 10, 2023
      </li>
     </ul>
    </div>
   </section>
    <section class="my-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-3xl font-bold text-gray-800">Latest Articles</h3>
            <div class="flex gap-2">
                <button type="button" class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700" id="latest-articles-prev" aria-label="Previous article">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700" id="latest-articles-next" aria-label="Next article">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        @php
            $query = \App\Models\ElectionArticle::query()->with(['articles']);
            if (\Illuminate\Support\Facades\Schema::hasColumn('election_articles', 'show_status')) {
                $query->where('show_status', 1);
            }

            $latestArticles = $query
                ->orderByDesc('created_at')
                ->get()
                ->map(fn ($ea) => $ea->articles)
                ->filter();
        @endphp

        @if($latestArticles->isEmpty())
            <div class="bg-white p-6 rounded-lg shadow-lg text-gray-600">No articles found.</div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4" id="latest-articles-container">
                @foreach($latestArticles as $a)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden latest-article-card hover:shadow-2xl transition-shadow duration-300">
                        <button type="button" class="block w-full text-left" data-article-card data-article-id="{{ $a->id }}">
                            <div class="w-full h-48 md:h-56 bg-gray-100">
                                <img src="{{ $a->featured_image_url ?? asset('images/logo.png') }}" alt="{{ $a->title }}" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}'">
                            </div>
                            <div class="p-4">
                                <h4 class="text-lg font-bold text-gray-800 line-clamp-2">{{ $a->title }}</h4>
                                <p class="text-gray-500 text-xs mt-1">{{ optional($a->created_at)->format('F j, Y') }}</p>
                                <p class="text-gray-600 text-sm mt-2 line-clamp-3">{{ \Illuminate\Support\Str::limit(strip_tags($a->content), 160) }}</p>
                                <div class="mt-3">
                                    <span class="inline-flex items-center gap-2 text-blue-600 font-semibold text-sm">Read More <span aria-hidden="true">→</span></span>
                                </div>
                            </div>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
     </section>

   <section class="my-8">
    <h3 class="text-3xl font-bold text-gray-800 mb-4">
     Election Guidelines
    </h3>
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <p class="text-gray-600 mb-4">
      Ensure you follow the university's election guidelines to make your vote count. Read the rules and regulations carefully.
     </p>
     <ul class="list-disc pl-5 text-gray-600">
      <li>
       Only registered students are eligible to vote.
      </li>
      <li>
       Each student can vote only once.
      </li>
      <li>
       Any form of election tampering will result in disqualification.
      </li>
      <li>
       Respect all candidates and their campaigns.
      </li>
      <li>
       Follow the university's code of conduct during the election period.
      </li>
     </ul>
    </div>
   </section>

  </main>
    <footer class="relative z-10 bg-blue-600 text-white p-4 text-center">
     <p>
        &copy; 2026 Philippine Christian University Online Election System. All rights reserved.
     </p>
    </footer>
  @guest
  <div class="fixed bottom-12 right-4 z-50" id="chatbot-root">
   <div class="flex flex-col items-end">
    <div class="relative mb-2">
     <div class="bg-white border border-gray-200 shadow-md text-gray-800 text-sm font-semibold px-4 py-2 rounded-full">
      Need help? Click here
     </div>
     <div class="absolute right-5 -bottom-1 w-3 h-3 bg-white border-r border-b border-gray-200 rotate-45"></div>
    </div>
    <button class="shadow-lg rounded-full bg-blue-600 text-white w-16 h-16 flex items-center justify-center hover:bg-blue-700 focus:outline-none" id="chatbot-toggle" type="button" aria-haspopup="dialog" aria-expanded="false" title="Help">
     <i class="fas fa-comment-dots text-2xl"></i>
    </button>
   </div>

   <div id="chatbot-panel" class="hidden mt-3 w-[92vw] max-w-md bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden" role="dialog" aria-label="Chatbot">
    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">
     <div>
      <div class="text-sm font-bold text-gray-900">PCU Assistant</div>
      <div class="text-xs text-gray-500">Ask how to register, vote, or view results</div>
     </div>
     <button id="chatbot-close" type="button" class="text-gray-500 hover:text-gray-700" aria-label="Close chatbot">
      <span class="text-xl leading-none">&times;</span>
     </button>
    </div>

    <div class="px-4 py-3 h-96 overflow-auto space-y-3" id="chatbot-messages"></div>
    <div class="px-4 pb-2" id="chatbot-quick-replies"></div>

    <form id="chatbot-form" class="px-4 py-3 border-t flex gap-2">
     <input id="chatbot-input" type="text" class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Type a question..." autocomplete="off" />
     <button id="chatbot-send" type="submit" class="rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700">Send</button>
    </form>
   </div>
  </div>
  @endguest
  <div class="fixed inset-0 hidden items-center justify-center bg-gray-800 bg-opacity-75 z-50" id="article-modal">
   <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden">
    <div class="flex justify-between items-center p-4 border-b">
     <h2 class="text-xl font-bold text-gray-800" id="article-modal-title">
     </h2>
     <button class="text-2xl leading-none" onclick="closeArticleModal()" type="button">
      &times;
     </button>
    </div>
    <div class="p-4 space-y-3 max-h-[80vh] overflow-auto">
     <div class="text-sm text-gray-600">
      <span id="article-modal-author">
      </span>
      <span id="article-modal-date">
      </span>
     </div>
     <img alt="Article image" class="w-full max-h-[55vh] object-contain bg-gray-100 rounded hidden" id="article-modal-image"/>
     <div class="prose max-w-none" id="article-modal-content">
     </div>
    </div>
   </div>
  </div>
  <div class="fixed inset-0 hidden items-center justify-center bg-gray-800 bg-opacity-75 z-50" id="event-modal">
   <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl mx-4 overflow-hidden">
    <div class="flex justify-between items-center p-4 border-b">
     <h2 class="text-xl font-bold text-gray-800" id="event-modal-title">
     </h2>
     <button class="text-2xl leading-none" onclick="closeEventModal()" type="button">
      &times;
     </button>
    </div>
    <div class="p-4 space-y-3 max-h-[80vh] overflow-auto">
     <div class="text-sm text-gray-600" id="event-modal-date">
     </div>
     <img alt="Event image" class="w-full max-h-[55vh] object-contain bg-gray-100 rounded hidden" id="event-modal-image"/>
     <div class="text-gray-700" id="event-modal-content">
     </div>
    </div>
   </div>
  </div>
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
        // Profile dropdown toggle (desktop)
        const profileToggle = document.getElementById('profile-toggle');
        const profileMenu = document.getElementById('profile-menu');

        if (profileToggle && profileMenu) {
            profileToggle.addEventListener('click', (event) => {
                event.stopPropagation();
                const isExpanded = profileToggle.getAttribute('aria-expanded') === 'true';
                profileToggle.setAttribute('aria-expanded', String(!isExpanded));
                profileToggle.setAttribute('aria-label', isExpanded ? 'Open profile menu' : 'Close profile menu');
                profileMenu.classList.toggle('hidden', isExpanded);
                document.querySelector('[data-profile-chevron]')?.classList.toggle('rotate-180', !isExpanded);
            });

            profileMenu.addEventListener('click', (event) => {
                event.stopPropagation();
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !profileMenu.classList.contains('hidden')) {
                    profileMenu.classList.add('hidden');
                    profileToggle.setAttribute('aria-expanded', 'false');
                    profileToggle.setAttribute('aria-label', 'Open profile menu');
                    document.querySelector('[data-profile-chevron]')?.classList.remove('rotate-180');
                    profileToggle.focus();
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (event) => {
                if (!profileMenu.classList.contains('hidden') && !profileMenu.contains(event.target) && event.target !== profileToggle && !profileToggle.contains(event.target)) {
                    profileMenu.classList.add('hidden');
                    profileToggle.setAttribute('aria-expanded', 'false');
                    profileToggle.setAttribute('aria-label', 'Open profile menu');
                    document.querySelector('[data-profile-chevron]')?.classList.remove('rotate-180');
                }
            });
        }

        // Events carousel
        const eventsContainer = document.getElementById('events-container');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');

        let currentIndex = 0;
        const eventsPerPage = 3;

        function showEvents(index) {
            const events = eventsContainer.children;
            for (let i = 0; i < events.length; i++) {
                events[i].classList.add('hidden');
            }
            for (let i = index; i < index + eventsPerPage && i < events.length; i++) {
                events[i].classList.remove('hidden');
            }
        }

        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex > 0) ? currentIndex - eventsPerPage : eventsContainer.children.length - eventsPerPage;
            if (currentIndex < 0) currentIndex = 0;
            showEvents(currentIndex);
        });

        nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex < eventsContainer.children.length - eventsPerPage) ? currentIndex + eventsPerPage : 0;
            showEvents(currentIndex);
        });

        showEvents(currentIndex);

        // Mobile menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Latest Articles carousel (3 items at a time)
        const latestArticlesContainer = document.getElementById('latest-articles-container');
        const latestPrevBtn = document.getElementById('latest-articles-prev');
        const latestNextBtn = document.getElementById('latest-articles-next');

        let latestIndex = 0;
        const latestPerPage = 3;

        function showLatestArticle(index) {
            if (!latestArticlesContainer) return;
            const cards = latestArticlesContainer.querySelectorAll('.latest-article-card');
            if (!cards.length) return;

            cards.forEach((c) => c.classList.add('hidden'));
            const maxStart = Math.max(0, cards.length - latestPerPage);
            if (index < 0) index = maxStart;
            if (index > maxStart) index = 0;

            for (let i = index; i < index + latestPerPage && i < cards.length; i++) {
                cards[i].classList.remove('hidden');
            }

            latestIndex = index;
        }

        if (latestPrevBtn && latestNextBtn && latestArticlesContainer) {
            latestPrevBtn.addEventListener('click', () => showLatestArticle(latestIndex - latestPerPage));
            latestNextBtn.addEventListener('click', () => showLatestArticle(latestIndex + latestPerPage));
            showLatestArticle(0);
        }

        async function openArticleModal(el) {
            const id = el.getAttribute('data-article-id');
            const modal = document.getElementById('article-modal');

            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.getElementById('article-modal-title').textContent = 'Loading...';
            document.getElementById('article-modal-author').textContent = '';
            document.getElementById('article-modal-date').textContent = '';
            document.getElementById('article-modal-content').innerHTML = '';

            const img = document.getElementById('article-modal-image');
            img.classList.add('hidden');
            img.removeAttribute('src');

            try {
                const res = await fetch(`/article/${id}/json`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) {
                    throw new Error(`Request failed: ${res.status}`);
                }

                const article = await res.json();

                document.getElementById('article-modal-title').textContent = article.title || '';
                document.getElementById('article-modal-author').textContent = article.author ? `By ${article.author}` : '';
                document.getElementById('article-modal-date').textContent = article.created_at ? ` • ${new Date(article.created_at).toLocaleString()}` : '';
                document.getElementById('article-modal-content').innerHTML = article.content || '';

                const imageUrl = article.featured_image_url || article.featured_image;
                if (imageUrl) {
                    img.src = imageUrl;
                    img.classList.remove('hidden');
                    img.onerror = () => {
                        img.classList.add('hidden');
                        img.removeAttribute('src');
                    };
                }
            } catch (e) {
                document.getElementById('article-modal-title').textContent = 'Error';
                document.getElementById('article-modal-content').textContent = 'Failed to load article details.';
            }
        }

        function closeArticleModal() {
            const modal = document.getElementById('article-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function openEventModalFromCard(card) {
            const title = card.getAttribute('data-event-title') || '';
            const date = card.getAttribute('data-event-date') || '';
            const description = card.getAttribute('data-event-description') || '';
            const image = card.getAttribute('data-event-image') || '';

            const modal = document.getElementById('event-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            document.getElementById('event-modal-title').textContent = title;
            document.getElementById('event-modal-date').textContent = date ? `Date: ${date}` : '';
            document.getElementById('event-modal-content').textContent = description;

            const img = document.getElementById('event-modal-image');
            img.classList.add('hidden');
            img.removeAttribute('src');
            if (image) {
                img.src = image;
                img.classList.remove('hidden');
                img.onerror = () => {
                    img.classList.add('hidden');
                    img.removeAttribute('src');
                };
            }
        }

        function closeEventModal() {
            const modal = document.getElementById('event-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('click', (e) => {
            const modal = document.getElementById('article-modal');
            if (modal && !modal.classList.contains('hidden') && e.target === modal) {
                closeArticleModal();
            }
        });

        document.addEventListener('click', (e) => {
            const modal = document.getElementById('event-modal');
            if (modal && !modal.classList.contains('hidden') && e.target === modal) {
                closeEventModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeArticleModal();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeEventModal();
            }
        });

        document.addEventListener('click', (e) => {
            const card = e.target.closest('[data-event-card]');
            if (!card) return;
            openEventModalFromCard(card);
        });

        document.addEventListener('click', (e) => {
            const card = e.target.closest('[data-article-card]');
            if (!card) return;
            openArticleModal(card);
        });

        // Chatbot (guest-only)
        (function () {
            const chatbotToggle = document.getElementById('chatbot-toggle');
            const chatbotPanel = document.getElementById('chatbot-panel');
            const chatbotClose = document.getElementById('chatbot-close');
            const chatbotForm = document.getElementById('chatbot-form');
            const chatbotInput = document.getElementById('chatbot-input');
            const chatbotMessages = document.getElementById('chatbot-messages');
            const chatbotQuickReplies = document.getElementById('chatbot-quick-replies');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if (!chatbotToggle || !chatbotPanel || !chatbotForm || !chatbotInput || !chatbotMessages || !chatbotQuickReplies) return;

            function appendMessage(role, text) {
                const wrap = document.createElement('div');
                const isUser = role === 'user';
                wrap.className = isUser ? 'flex justify-end' : 'flex justify-start';
                const bubble = document.createElement('div');
                bubble.className = (isUser
                    ? 'max-w-[80%] bg-blue-600 text-white'
                    : 'max-w-[80%] bg-gray-100 text-gray-900') + ' px-3 py-2 rounded-lg text-sm whitespace-pre-line';
                bubble.textContent = text;
                wrap.appendChild(bubble);
                chatbotMessages.appendChild(wrap);
                chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
            }

            function renderQuickReplies(replies) {
                if (!replies || replies.length === 0) {
                    chatbotQuickReplies.innerHTML = '';
                    return;
                }

                chatbotQuickReplies.innerHTML = `
                    <div class="flex flex-wrap gap-2">
                        ${replies.map((r) => `<button type="button" class="chatbot-quick px-3 py-1.5 text-xs font-semibold rounded-full bg-gray-100 hover:bg-gray-200" data-text="${String(r).replace(/"/g, '&quot;')}">${r}</button>`).join('')}
                    </div>
                `;
            }

            async function sendToBot(text) {
                const res = await fetch("{{ route('chatbot.respond') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf || '',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ message: text })
                });
                if (!res.ok) throw new Error('Chatbot request failed');
                return await res.json();
            }

            function isGibberish(text) {
                const t = String(text || '').trim();
                if (t.length < 2) return true;

                const stripped = t.replace(/\s+/g, '');
                const lettersOnly = /^[a-zA-Z]+$/.test(stripped);

                if (lettersOnly && stripped.length >= 8) {
                    const lower = stripped.toLowerCase();
                    const uniqueChars = new Set(lower.split('')).size;
                    if (uniqueChars <= 3) return true;
                    if (/^(.{1,3})\1+$/.test(lower)) return true;
                }

                const meaningfulChars = (t.match(/[a-zA-Z0-9]/g) || []).length;
                if (t.length >= 6 && meaningfulChars <= 2) return true;

                return false;
            }

            function guidedFallback() {
                appendMessage('bot', "I couldn't understand that. Please try a clear question or tap one of these:");
                renderQuickReplies(defaultQuickReplies);
            }

            let defaultQuickReplies = [
                'How do I log in?',
                'How do I vote?',
                'How do I register as candidate?',
                'Where can I see results?'
            ];

            async function openChatbot() {
                chatbotPanel.classList.remove('hidden');
                chatbotToggle.setAttribute('aria-expanded', 'true');
                if (chatbotMessages.childElementCount === 0) {
                    try {
                        const data = await sendToBot('hi');
                        const greet = data?.reply || "Hi! I'm PCU Assistant.";
                        appendMessage('bot', greet);
                        if (Array.isArray(data?.quick_replies) && data.quick_replies.length > 0) {
                            defaultQuickReplies = data.quick_replies;
                        }
                        renderQuickReplies(defaultQuickReplies);
                    } catch (err) {
                        appendMessage('bot', "Hi! I'm PCU Assistant. You can ask me how to log in, register as candidate, vote, or view results.");
                        renderQuickReplies(defaultQuickReplies);
                    }
                }
                setTimeout(() => chatbotInput.focus(), 50);
            }

            function closeChatbot() {
                chatbotPanel.classList.add('hidden');
                chatbotToggle.setAttribute('aria-expanded', 'false');
            }

            chatbotToggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const isOpen = !chatbotPanel.classList.contains('hidden');
                if (isOpen) closeChatbot();
                else openChatbot();
            });

            chatbotClose.addEventListener('click', (e) => {
                e.preventDefault();
                closeChatbot();
            });

            chatbotPanel.addEventListener('click', (e) => {
                e.stopPropagation();
                const btn = e.target.closest('.chatbot-quick');
                if (!btn) return;
                const txt = btn.getAttribute('data-text');
                if (!txt) return;
                chatbotInput.value = txt;
                chatbotForm.dispatchEvent(new Event('submit', { cancelable: true }));
            });

            chatbotForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const text = (chatbotInput.value || '').trim();
                if (!text) return;
                if (isGibberish(text)) {
                    chatbotInput.value = '';
                    guidedFallback();
                    return;
                }
                chatbotInput.value = '';
                appendMessage('user', text);
                try {
                    const data = await sendToBot(text);
                    appendMessage('bot', data?.reply || 'Sorry, I could not respond.');
                    renderQuickReplies(data?.quick_replies || []);
                } catch (err) {
                    appendMessage('bot', 'Sorry, something went wrong. Please try again.');
                }
            });

            document.addEventListener('click', () => {
                closeChatbot();
            });
        })();
  </script>
 </body>
</html>
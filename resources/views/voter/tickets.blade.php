<html>
 <head>
  <title>
   University Online Election System - Support Tickets
  </title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
  <header class="relative z-50 bg-blue-600 text-white p-4">
   <div class="container mx-auto flex flex-col items-center md:flex-row md:justify-between">
    <a href="{{ url('/') }}" class="flex items-center gap-3 mb-4 md:mb-0">
        <img src="{{ asset('images/pculogo.png') }}" alt="School Logo" class="w-16 h-16 md:w-24 md:h-24 -my-4 object-contain flex-shrink-0" onerror="this.onerror=null;this.style.display='none';">
        <h1 class="text-3xl font-bold">University Online Election System</h1>
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
      <li>
       <a class="hover:underline" href="{{ route('tickets.index') }}">
        Tickets
       </a>
      </li>
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
        <form method="POST" action="{{ route('logout') }}" class="border-t">
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
     Support Tickets
    </h2>
    <p class="text-gray-600 text-lg">
     Submit a vote concern ticket (vote reset request / wrong vote) and wait for admin response.
    </p>
   </section>

   @if (session('success'))
    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-800">
     {{ session('success') }}
    </div>
   @endif

   @if ($errors->any())
    <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-800">
     <ul class="list-disc pl-5">
      @foreach ($errors->all() as $error)
       <li>{{ $error }}</li>
      @endforeach
     </ul>
    </div>
   @endif

   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-2xl font-bold text-gray-800 mb-4">Submit Ticket</h3>

     <form method="POST" action="{{ route('tickets.store') }}" class="space-y-4">
      @csrf

      <div>
       <label class="block text-sm font-medium text-gray-700">Concern Type</label>
       <select name="type" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2" required>
        @php($type = old('type', 'Vote Concern'))
        <option value="Vote Concern" {{ $type === 'Vote Concern' ? 'selected' : '' }}>Vote Concern</option>
        <option value="Reset Vote Request" {{ $type === 'Reset Vote Request' ? 'selected' : '' }}>Reset Vote Request</option>
        <option value="Wrong Vote" {{ $type === 'Wrong Vote' ? 'selected' : '' }}>Wrong Vote</option>
       </select>
      </div>

      <div>
       <label class="block text-sm font-medium text-gray-700">Subject</label>
       <input name="subject" value="{{ old('subject') }}" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2" placeholder="e.g. Request reset for President" required />
      </div>

      <div>
       <label class="block text-sm font-medium text-gray-700">Message</label>
       <textarea name="message" rows="5" class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2" placeholder="Explain what happened and what you need." required>{{ old('message') }}</textarea>
      </div>

      <div class="flex justify-end">
       <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Submit</button>
      </div>
     </form>
    </div>
   </section>

   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-2xl font-bold text-gray-800 mb-4">My Tickets</h3>

     <div class="overflow-x-auto">
      <table class="min-w-full bg-white">
       <thead>
        <tr>
         <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Date</th>
         <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Type</th>
         <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Subject</th>
         <th class="py-2 px-4 border-b-2 border-gray-300 text-left text-gray-600 font-bold">Status</th>
        </tr>
       </thead>
       <tbody>
        @forelse(($tickets ?? collect()) as $t)
         <tr class="align-top">
          <td class="py-2 px-4 border-b border-gray-300">{{ optional($t->created_at)->format('Y-m-d H:i') }}</td>
          <td class="py-2 px-4 border-b border-gray-300">{{ $t->type }}</td>
          <td class="py-2 px-4 border-b border-gray-300">
           <div class="font-semibold">{{ $t->subject }}</div>
           <div class="text-sm text-gray-600 whitespace-pre-line mt-1">{{ $t->message }}</div>
           @if ($t->admin_reply)
            <div class="mt-2 text-sm">
             <div class="font-bold text-gray-800">Admin Reply:</div>
             <div class="text-gray-700 whitespace-pre-line">{{ $t->admin_reply }}</div>
            </div>
           @endif
          </td>
          <td class="py-2 px-4 border-b border-gray-300">
           <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold {{ $t->status === 'Resolved' ? 'bg-green-100 text-green-800' : ($t->status === 'In Progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
            {{ $t->status }}
           </span>
          </td>
         </tr>
        @empty
         <tr>
          <td class="py-4 px-4 text-gray-500" colspan="4">No tickets yet.</td>
         </tr>
        @endforelse
       </tbody>
      </table>
     </div>
    </div>
   </section>
  </main>

    <footer class="relative z-10 bg-blue-600 text-white p-4 text-center">
     <p>
        2026 University Online Election System. All rights reserved.
     </p>
    </footer>

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

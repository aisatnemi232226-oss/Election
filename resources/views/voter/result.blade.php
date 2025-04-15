<html>
 <head>
  <title>
   University Online Election System - Results
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
 <body class="bg-gray-100">
  <header class="bg-blue-600 text-white p-4">
   <div class="container mx-auto flex flex-col items-center md:flex-row md:justify-between">
    <h1 class="text-3xl font-bold mb-4 md:mb-0">
        <a href="{{ url('/') }}" class="text-inherit">University Online Election System</a>
    </h1>
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
       <a class="hover:underline" href="{{ url('/login ') }}">
        Log In
       </a>
      </li>
     </ul>
    </nav>
   </div>
  </header>
  <main class="container mx-auto p-4">
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
    <a href="{{ url('/resultTally') }}" class="block">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
            <img alt="Result for Presidential Election showing the winning candidate" class="rounded-lg mx-auto mb-4" height="200" src="https://storage.googleapis.com/a1aa/image/Fxij7q9yDiZMLRqOKK1E9eF9D8jv8mS1NNInJlyLUgfzCvGUA.jpg" width="300"/>
            <h4 class="text-2xl font-bold text-gray-800 mb-2">
                Presidential Election
            </h4>
            <p class="text-gray-600">
                Total Votes: 1200
            </p>
            <p class="text-gray-600">
                Total Registered: 1500
            </p>
        </div>
    </a>
    <a href="{{ url('/resultTally') }}" class="block">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
            <img alt="Result for Vice Presidential Election showing the winning candidate" class="rounded-lg mx-auto mb-4" height="200" src="https://storage.googleapis.com/a1aa/image/ehlyeLQdiOnekpoFelpHRbK67Sw9wWdnk3HsCXjoZCUeW41gC.jpg" width="300"/>
            <h4 class="text-2xl font-bold text-gray-800 mb-2">
                Vice Presidential Election
            </h4>
            <p class="text-gray-600">
                Total Votes: 1100
            </p>
            <p class="text-gray-600">
                Total Registered: 1500
            </p>
        </div>
    </a>
    <a href="{{ url('/resultTally') }}" class="block">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
            <img alt="Result for Election 2025 showing the winning candidates for all positions" class="rounded-lg mx-auto mb-4" height="200" src="https://storage.googleapis.com/a1aa/image/e3zfovjnPqhB60pmwjFfrmvZPqKu9uLuh79qVuvSJLFpFeaQB.jpg" width="300"/>
            <h4 class="text-2xl font-bold text-gray-800 mb-2">
                Election 2025 (All Positions)
            </h4>
            <p class="text-gray-600">
                Total Votes: 1050
            </p>
            <p class="text-gray-600">
                Total Registered: 1500
            </p>
        </div>
    </a>
    </div>
   </section>
   <section class="my-8">
    <h3 class="text-3xl font-bold text-gray-800 mb-4">
     Poll Results
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
      <img alt="Result for Favorite Presidential Candidate poll" class="rounded-lg mx-auto mb-4" height="200" src="https://storage.googleapis.com/a1aa/image/mPeWnsgHfAgSZklazzsc6QqvNiE7X1Vesfe7wxAuCTt1W41gC.jpg" width="300"/>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Favorite Presidential Candidate
      </h4>
      <p class="text-gray-600">
       Most Voted: John Doe
      </p>
      <p class="text-gray-600">
       Votes: 800
      </p>
     </div>
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
      <img alt="Result for Most Important Election Issue poll" class="rounded-lg mx-auto mb-4" height="200" src="https://storage.googleapis.com/a1aa/image/0ifxZa28k00Wf0ODn1oBGrdqGJq8eG7PE4BYtp6Zi7gqFeaQB.jpg" width="300"/>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Most Important Election Issue
      </h4>
      <p class="text-gray-600">
       Most Voted: Campus Safety
      </p>
      <p class="text-gray-600">
       Votes: 900
      </p>
     </div>
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
      <img alt="Result for Preferred Voting Method poll" class="rounded-lg mx-auto mb-4" height="200" src="https://storage.googleapis.com/a1aa/image/vmrKfoRjlKyFYqXU6yTQK9KsY9cnWIxMmIfpMKEO0hfkFeaQB.jpg" width="300"/>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Preferred Voting Method
      </h4>
      <p class="text-gray-600">
       Most Voted: Online Voting
      </p>
      <p class="text-gray-600">
       Votes: 950
      </p>
     </div>
    </div>
   </section>
  </main>
  <footer class="bg-blue-600 text-white p-4 text-center">
   <p>
    © 2023 University Online Election System. All rights reserved.
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
   document.querySelector('a[href="login"]').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('login-modal').classList.remove('hidden');
        });

        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');

        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });
  </script>
 </body>
</html>
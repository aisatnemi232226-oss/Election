<html>
 <head>
  <title>
   University Online Election System - Vote
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
     Choose an Election Event to Vote
    </h2>
    <p class="text-gray-600 text-lg">
     Select the election event you want to participate in and cast your vote.
    </p>
   </section>
   <section class="my-8">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
      <img alt="Election Event 1: Presidential Election" class="rounded-lg mx-auto mb-4" height="200" src="https://storage.googleapis.com/a1aa/image/lhFfvIk4Y8wBXyF0s6f7lFgIlrmPYRftwJHFFDDFv3naqMNoA.jpg" width="300"/>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Presidential Election
      </h4>
      <p class="text-gray-600">
       Vote for the next student body president.
      </p>
     </div>
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
      <img alt="Election Event 2: Vice Presidential Election" class="rounded-lg mx-auto mb-4" height="200" src="https://storage.googleapis.com/a1aa/image/Qxcl5e8XJ7ThBC4DmUe30TM93oX1e5Rn453sqkiafWdrUZaQB.jpg" width="300"/>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Vice Presidential Election
      </h4>
      <p class="text-gray-600">
       Vote for the next student body vice president.
      </p>
     </div>
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
      <img alt="Election Event 3: Secretary Election" class="rounded-lg mx-auto mb-4" height="200" src="https://storage.googleapis.com/a1aa/image/7ePWeky3U5qsXECimTkVewb9UDfufN1xXmzhEZ5yyTfDTlpBF.jpg" width="300"/>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Secretary Election
      </h4>
      <p class="text-gray-600">
       Vote for the next student body secretary.
      </p>
     </div>
    </div>
   </section>
   <section class="my-8">
    <h3 class="text-3xl font-bold text-gray-800 mb-4">
     Polls
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
      <img alt="Poll 1: Favorite Presidential Candidate" class="rounded-lg mx-auto mb-4" height="400" src="https://storage.googleapis.com/a1aa/image/wJkVcZJ1sxqCM11HKBqB8zuXmgu8eew9UW9XhnxnMZNS6uGUA.jpg" width="600"/>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Favorite Presidential Candidate
      </h4>
      <p class="text-gray-600">
       Vote for your favorite presidential candidate.
      </p>
     </div>
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
      <img alt="Poll 2: Most Important Election Issue" class="rounded-lg mx-auto mb-4" height="400" src="https://storage.googleapis.com/a1aa/image/beKzm39DdG1EVqTnlI6O61nZVPu1vTidW2xhJYCFLJRKdXDKA.jpg" width="600"/>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Most Important Election Issue
      </h4>
      <p class="text-gray-600">
       Vote on the most important issue in this election.
      </p>
     </div>
     <div class="bg-white p-6 rounded-lg shadow-lg text-center tile">
      <img alt="Poll 3: Preferred Voting Method" class="rounded-lg mx-auto mb-4" height="400" src="https://storage.googleapis.com/a1aa/image/sS9cRez5QAXbdilAZOwC3D4wP6OSfIUARcT3oNeWys4m0dNoA.jpg" width="600"/>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Preferred Voting Method
      </h4>
      <p class="text-gray-600">
       Vote for your preferred method of voting.
      </p>
     </div>
    </div>
   </section>
   <section class="my-8">
    <h3 class="text-3xl font-bold text-gray-800 mb-4">
     Vote History
    </h3>
    <div class="hidden md:block md:w-1/4">
    </div>
    <div class="bg-white p-6 rounded-lg shadow-lg max-h-64 overflow-y-auto md:max-w-2xl mx-auto md:mx-0 md:w-1/2">
     <ul class="divide-y divide-gray-200">
      <li class="py-4 flex flex-col md:flex-row justify-between items-start md:items-center">
       <div class="text-left w-full md:w-1/3 no-wrap md:mr-4">
        <span class="text-gray-800 font-bold">
         Presidential Election
        </span>
        <span class="block md:hidden text-gray-600">
         Election Vote
        </span>
        <span class="block md:hidden text-gray-600">
         March 1, 2023
        </span>
       </div>
       <div class="hidden md:flex flex-col md:flex-row items-center w-full md:w-1/3 no-wrap md:mr-4">
        <span class="text-gray-600 mr-4">
         Election Vote
        </span>
        <span class="text-gray-600">
         March 1, 2023
        </span>
       </div>
      </li>
      <li class="py-4 flex flex-col md:flex-row justify-between items-start md:items-center">
       <div class="text-left w-full md:w-1/3 no-wrap md:mr-4">
        <span class="text-gray-800 font-bold">
         Vice Presidential Election
        </span>
        <span class="block md:hidden text-gray-600">
         Election Vote
        </span>
        <span class="block md:hidden text-gray-600">
         March 2, 2023
        </span>
       </div>
       <div class="hidden md:flex flex-col md:flex-row items-center w-full md:w-1/3 no-wrap md:mr-4">
        <span class="text-gray-600 mr-4">
         Election Vote
        </span>
        <span class="text-gray-600">
         March 2, 2023
        </span>
       </div>
      </li>
      <li class="py-4 flex flex-col md:flex-row justify-between items-start md:items-center">
       <div class="text-left w-full md:w-1/3 no-wrap md:mr-4">
        <span class="text-gray-800 font-bold">
         Secretary Election
        </span>
        <span class="block md:hidden text-gray-600">
         Election Vote
        </span>
        <span class="block md:hidden text-gray-600">
         March 3, 2023
        </span>
       </div>
       <div class="hidden md:flex flex-col md:flex-row items-center w-full md:w-1/3 no-wrap md:mr-4">
        <span class="text-gray-600 mr-4">
         Election Vote
        </span>
        <span class="text-gray-600">
         March 3, 2023
        </span>
       </div>
      </li>
      <li class="py-4 flex flex-col md:flex-row justify-between items-start md:items-center">
       <div class="text-left w-full md:w-1/3 no-wrap md:mr-4">
        <span class="text-gray-800 font-bold">
         Campus Facilities Survey
        </span>
        <span class="block md:hidden text-gray-600">
         Poll Vote
        </span>
        <span class="block md:hidden text-gray-600">
         March 4, 2023
        </span>
       </div>
       <div class="hidden md:flex flex-col md:flex-row items-center w-full md:w-1/3 no-wrap md:mr-4">
        <span class="text-gray-600 mr-4">
         Poll Vote
        </span>
        <span class="text-gray-600">
         March 4, 2023
        </span>
       </div>
      </li>
      <li class="py-4 flex flex-col md:flex-row justify-between items-start md:items-center">
       <div class="text-left w-full md:w-1/3 no-wrap md:mr-4">
        <span class="text-gray-800 font-bold">
         Cafeteria Food Quality Survey
        </span>
        <span class="block md:hidden text-gray-600">
         Poll Vote
        </span>
        <span class="block md:hidden text-gray-600">
         March 5, 2023
        </span>
       </div>
       <div class="hidden md:flex flex-col md:flex-row items-center w-full md:w-1/3 no-wrap md:mr-4">
        <span class="text-gray-600 mr-4">
         Poll Vote
        </span>
        <span class="text-gray-600">
         March 5, 2023
        </span>
       </div>
      </li>
     </ul>
    </div>
    <div class="hidden md:block md:w-1/4">
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

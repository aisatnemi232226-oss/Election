<html>
 <head>
  <title>
   University Online Election System - Registration
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
 <body class="bg-gray-100">
  <header class="bg-blue-600 text-white p-4">
   <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
    <h1 class="text-3xl font-bold mb-4 md:mb-0">
        <a href="{{ url('/') }}" class="text-inherit">
            University Online Election System
        </a>
    </h1>
    <button class="md:hidden text-white" id="menu-toggle">
     <i class="fas fa-bars text-2xl">
     </i>
    </button>
    <nav class="hidden md:block" id="menu">
     <ul class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
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
   <nav class="md:hidden hidden" id="mobile-menu">
    <ul class="flex flex-col space-y-2 mt-4">
     <li>
      <a class="hover:underline block text-center" href="#">
       Home
      </a>
     </li>
     <li>
      <a class="hover:underline block text-center" href="#">
       Election
      </a>
     </li>
     <li>
      <a class="hover:underline block text-center" href="#">
       Vote
      </a>
     </li>
     <li>
      <a class="hover:underline block text-center" href="#">
       Results
      </a>
     </li>
     <li>
      <a class="hover:underline block text-center" href="#">
       Contact
      </a>
     </li>
     <li>
      <a class="hover:underline block text-center" href="login">
       Log In
      </a>
     </li>
    </ul>
   </nav>
  </header>
  <main class="container mx-auto p-4">
   <section class="text-center my-8">
    <h2 class="text-4xl font-bold text-gray-800 mb-4">
     Register for University Online Election System
    </h2>
    <p class="text-gray-600 text-lg">
     Sign up with your student credentials to participate in the election.
    </p>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <form>
      <div class="mb-4">
       <label class="block text-gray-700 font-bold mb-2" for="election-list">
        Election List
       </label>
       <select class="w-full p-3 border border-gray-300 rounded-lg" id="election-list" name="election-list" required="">
        <option value="">
         Select an Election
        </option>
        <option value="presidential-2023">
         Presidential Election 2023
        </option>
        <option value="student-council-2023">
         Student Council Election 2023
        </option>
        <option value="department-head-2023">
         Department Head Election 2023
        </option>
       </select>
      </div>
      <div class="text-center">
       <button class="bg-blue-600 text-white p-3 rounded-lg cursor-pointer hover:bg-blue-700" type="submit">
        Register
       </button>
      </div>
     </form>
    </div>
   </section>
  </main>
  <footer class="bg-blue-600 text-white p-4 text-center">
   <p>
    © 2023 University Online Election System. All rights reserved.
   </p>
  </footer>
  <script>
   const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
  </script>
 </body>
</html>
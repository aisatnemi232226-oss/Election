<html>
 <head>
  <title>
   University Online Election System - Contact Us
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
     Contact Us
    </h2>
    <p class="text-gray-600 text-lg">
     We would love to hear from you! Please use the contact information below to get in touch with us.
    </p>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-3xl font-bold text-gray-800 mb-4">
      Our Location
     </h3>
     <div class="flex justify-center">
      <iframe class="w-full h-64" height="300" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.086509486048!2d144.9630579153167!3d-37.81410797975195!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf577d1f9f1b1a1b1!2sUniversity%20of%20Melbourne!5e0!3m2!1sen!2sau!4v1633071234567!5m2!1sen!2sau" style="border:0;" allowfullscreen="" loading="lazy" width="600"></iframe>
     </div>
    </div>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-3xl font-bold text-gray-800 mb-4">
      Contact Information
     </h3>
     <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-gray-100 p-4 rounded-lg shadow-md">
       <h4 class="text-xl font-bold mb-2">
        Address
       </h4>
       <p class="text-gray-600">
        123 University Ave, City, Country
       </p>
      </div>
      <div class="bg-gray-100 p-4 rounded-lg shadow-md">
       <h4 class="text-xl font-bold mb-2">
        Phone
       </h4>
       <p class="text-gray-600">
        +123 456 7890
       </p>
      </div>
      <div class="bg-gray-100 p-4 rounded-lg shadow-md">
       <h4 class="text-xl font-bold mb-2">
        Email
       </h4>
       <p class="text-gray-600">
        contact@university.edu
       </p>
      </div>
      <div class="bg-gray-100 p-4 rounded-lg shadow-md">
       <h4 class="text-xl font-bold mb-2">
        Office Hours
       </h4>
       <p class="text-gray-600">
        Mon - Fri: 9:00 AM - 5:00 PM
       </p>
      </div>
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
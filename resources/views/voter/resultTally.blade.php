<html>
 <head>
  <title>
   University Online Election System - Presidential Election Results
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
 <body class="bg-gray-100">
  <header class="bg-blue-600 text-white p-4">
   <div class="container mx-auto flex flex-col items-center md:flex-row md:justify-between">
    <h1 class="text-3xl font-bold mb-4 md:mb-0">
        <a href="{{ url('/') }}" class="text-inherit">
            University Online Election System
        </a>
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
     Presidential Election Results
    </h2>
    <p class="text-gray-600 text-lg">
     View the detailed results of the Presidential Election.
    </p>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-3xl font-bold text-gray-800 mb-4">
      Candidate Tally
     </h3>
     <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-gray-100 p-4 rounded-lg shadow-md">
       <div class="flex items-center mb-4">
        <img alt="John Doe's profile picture" class="w-16 h-16 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/PI7EVpmdGUbuMpM9ZfJyqx1tnfZPlCF3wb7nicINthUGsvGUA.jpg" width="100"/>
        <div>
         <h4 class="text-xl font-bold">
          John Doe
         </h4>
         <p class="text-gray-600">
          600 Votes
         </p>
        </div>
       </div>
       <div class="relative pt-1">
        <div class="flex mb-2 items-center justify-between">
         <div>
          <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
           50%
          </span>
         </div>
        </div>
        <div class="overflow-hidden h-4 mb-4 text-xs flex rounded bg-blue-200">
         <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600" style="width:50%">
         </div>
        </div>
       </div>
      </div>
      <div class="bg-gray-100 p-4 rounded-lg shadow-md">
       <div class="flex items-center mb-4">
        <img alt="Jane Smith's profile picture" class="w-16 h-16 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/ICq5xrb2kdq2I5mSrEsI6ljg98u91665tIkePL3Y3q6D2XDKA.jpg" width="100"/>
        <div>
         <h4 class="text-xl font-bold">
          Jane Smith
         </h4>
         <p class="text-gray-600">
          400 Votes
         </p>
        </div>
       </div>
       <div class="relative pt-1">
        <div class="flex mb-2 items-center justify-between">
         <div>
          <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">
           33.33%
          </span>
         </div>
        </div>
        <div class="overflow-hidden h-4 mb-4 text-xs flex rounded bg-green-200">
         <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-600" style="width:33.33%">
         </div>
        </div>
       </div>
      </div>
      <div class="bg-gray-100 p-4 rounded-lg shadow-md">
       <div class="flex items-center mb-4">
        <img alt="Alice Johnson's profile picture" class="w-16 h-16 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/5MKTRCNSj64CNdmffuZaMfG5ffwpyKuEMC3sLmkWAp3cg91gC.jpg" width="100"/>
        <div>
         <h4 class="text-xl font-bold">
          Alice Johnson
         </h4>
         <p class="text-gray-600">
          200 Votes
         </p>
        </div>
       </div>
       <div class="relative pt-1">
        <div class="flex mb-2 items-center justify-between">
         <div>
          <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-red-600 bg-red-200">
           16.67%
          </span>
         </div>
        </div>
        <div class="overflow-hidden h-4 mb-4 text-xs flex rounded bg-red-200">
         <div class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-red-600" style="width:16.67%">
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
   </section>
   <section class="my-8">
    <div class="bg-white p-6 rounded-lg shadow-lg">
     <h3 class="text-3xl font-bold text-gray-800 mb-4">
      Election Party Tally
     </h3>
     <div class="flex justify-center">
      <canvas id="partyPieChart" class="w-64 h-64"></canvas>
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
        <tr>
         <td class="py-2 px-4 border-b border-gray-300">
          Bachelor of Science in Computer Science
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          1000
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          900
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          800
         </td>
        </tr>
        <tr>
         <td class="py-2 px-4 border-b border-gray-300">
          Bachelor of Science in Business Administration Major in Operations Management
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          950
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          850
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          750
         </td>
        </tr>
        <tr>
         <td class="py-2 px-4 border-b border-gray-300">
          Bachelor of Science in Engineering
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          900
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          800
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          700
         </td>
        </tr>
        <tr>
         <td class="py-2 px-4 border-b border-gray-300">
          Bachelor of Arts
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          850
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          750
         </td>
         <td class="py-2 px-4 border-b border-gray-300">
          650
         </td>
        </tr>
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
       <div class="flex items-center mb-4">
        <img alt="Icon representing male gender" class="w-10 h-10 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/hGasLb9WzgqdHdu9Yt96XMEihM6NCLlpe5BZqsKoQNCC2XDKA.jpg" width="100"/>
        <div>
         <p class="text-gray-600">
          Male: 55%
         </p>
        </div>
       </div>
       <div class="flex items-center">
        <img alt="Icon representing female gender" class="w-10 h-10 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/a3Ho08UEFf0jeEklwfr5cuU4SXAEN5gwPMjIogsMltQSYfaQB.jpg" width="100"/>
        <div>
         <p class="text-gray-600">
          Female: 45%
         </p>
        </div>
       </div>
      </div>
      <div class="bg-gray-100 p-4 rounded-lg shadow-md">
       <h4 class="text-xl font-bold mb-2">
        Voter Turnout by Year
       </h4>
       <div class="flex items-center mb-4">
        <img alt="Icon representing the year 2021" class="w-10 h-10 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/AubbQxjf4lVHeU1EEPFIgfE05uX1IRLG7rBZ2ivmilXKYfaQB.jpg" width="100"/>
        <div>
         <p class="text-gray-600">
          2021: 70%
         </p>
        </div>
       </div>
       <div class="flex items-center mb-4">
        <img alt="Icon representing the year 2022" class="w-10 h-10 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/j23hJ9pyM14HDVo5Qtb9zf53PxPHQTXhmlS9Yxzxnt1A2XDKA.jpg" width="100"/>
        <div>
         <p class="text-gray-600">
          2022: 75%
         </p>
        </div>
       </div>
       <div class="flex items-center">
        <img alt="Icon representing the year 2023" class="w-10 h-10 rounded-full mr-4" height="100" src="https://storage.googleapis.com/a1aa/image/dDzSW0rDwZKdGRlXYJLX3NjZLkYWq6NZveeJNvufpVPFYfaQB.jpg" width="100"/>
        <div>
         <p class="text-gray-600">
          2023: 80%
         </p>
        </div>
       </div>
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

        const ctx = document.getElementById('partyPieChart').getContext('2d');
        const partyPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Party A', 'Party B', 'Party C'],
                datasets: [{
                    data: [50, 30, 20],
                    backgroundColor: ['#1E3A8A', '#10B981', '#EF4444'],
                }]
            },
            options: {
                responsive: true,
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
  </script>
 </body>
</html>
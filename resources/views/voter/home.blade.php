<html>
 <head>
  <title>
   University Online Election System
  </title>
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap" rel="stylesheet"/>
  <link rel="icon" href="{{ URL::to('/images/logo.png') }}">
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
      <a class="hover:underline block text-center" href="{{ url('/login ') }}">
       Log In
      </a>
     </li>
    </ul>
   </nav>
  </header>
  <main class="container mx-auto p-4">
   <section class="text-center my-8">
    <h2 class="text-4xl font-bold text-gray-800 mb-4">
     Welcome to the University Online Election System
    </h2>
    <p class="text-gray-600 text-lg">
     Participate in the democratic process and vote for your student representatives.
    </p>
   </section>
   <section class="my-8">
    <h3 class="text-3xl font-bold text-gray-800 mb-4">
     How It Works
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ url('/registerElection') }}" class="block">
     <div class="bg-white p-6 rounded-lg shadow-lg text-center transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
      <i class="fas fa-user-graduate text-blue-600 text-4xl mb-4">
      </i>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Register
      </h4>
      <p class="text-gray-600">
       Sign up with your student credentials to participate in the election.
      </p>
     </div>
    </a>
     <div class="bg-white p-6 rounded-lg shadow-lg text-center transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
      <i class="fas fa-vote-yea text-blue-600 text-4xl mb-4">
      </i>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Vote
      </h4>
      <p class="text-gray-600">
       Cast your vote for your preferred candidates online.
      </p>
     </div>
     <div class="bg-white p-6 rounded-lg shadow-lg text-center transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
      <i class="fas fa-chart-bar text-blue-600 text-4xl mb-4">
      </i>
      <h4 class="text-2xl font-bold text-gray-800 mb-2">
       Results
      </h4>
      <p class="text-gray-600">
       View the election results once the voting period is over.
      </p>
     </div>
    </div>
   </section>
   <section class="my-8">
    <div class="flex justify-between items-center mb-4">
     <h3 class="text-3xl font-bold text-gray-800">
      Election Events
     </h3>
     <div>
      <button class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 mr-2" id="prev-btn">
       <i class="fas fa-chevron-left">
       </i>
      </button>
      <button class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700" id="next-btn">
       <i class="fas fa-chevron-right">
       </i>
      </button>
     </div>
    </div>
    <div class="relative">
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="events-container">
      <div class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
       <img alt="Event 1: Election Kickoff" class="rounded-lg mb-4" height="400" src="https://storage.googleapis.com/a1aa/image/dvCf7rv3J9wNaqenfCddb8170ZkT2UD6ivYRPVt79lD3ezaQB.jpg" width="600"/>
       <h4 class="text-2xl font-bold text-gray-800 mb-2">
        Election Kickoff
       </h4>
       <p class="text-gray-600">
        Date: March 1, 2023
       </p>
       <p class="text-gray-600">
        Join us for the official start of the election season with speeches from candidates and more.
       </p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
       <img alt="Event 2: Candidate Debate" class="rounded-lg mb-4" height="400" src="https://storage.googleapis.com/a1aa/image/4ankqrk3osKqB9DQfnuXoqcnqPtRvVpXvf2A5JDQWeZ9ezaQB.jpg" width="600"/>
       <h4 class="text-2xl font-bold text-gray-800 mb-2">
        Candidate Debate
       </h4>
       <p class="text-gray-600">
        Date: March 15, 2023
       </p>
       <p class="text-gray-600">
        Watch the candidates debate on key issues and present their platforms.
       </p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
       <img alt="Event 3: Voting Day" class="rounded-lg mb-4" height="400" src="https://storage.googleapis.com/a1aa/image/2ZcIUq4TX0axBpyzxe6zBIm7EI5K3w8fnLKi5Py79RXgfZNoA.jpg" width="600"/>
       <h4 class="text-2xl font-bold text-gray-800 mb-2">
        Voting Day
       </h4>
       <p class="text-gray-600">
        Date: April 1, 2023
       </p>
       <p class="text-gray-600">
        Cast your vote online and make your voice heard in the student government elections.
       </p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-lg hidden transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
       <img alt="Event 4: Results Announcement" class="rounded-lg mb-4" height="400" src="https://storage.googleapis.com/a1aa/image/rGfyfufKf1B2PRTnCNcMoEBgOv9fqctS7Sc6TiNClkuN7n1gC.jpg" width="600"/>
       <h4 class="text-2xl font-bold text-gray-800 mb-2">
        Results Announcement
       </h4>
       <p class="text-gray-600">
        Date: April 10, 2023
       </p>
       <p class="text-gray-600">
        Join us for the announcement of the election results.
       </p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-lg hidden transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
       <img alt="Event 5: Post-Election Discussion" class="rounded-lg mb-4" height="400" src="https://storage.googleapis.com/a1aa/image/Med8ye4CFfabKIyj55aasuh3VfRoahXnoxpzN5nRQeup7n1gC.jpg" width="600"/>
       <h4 class="text-2xl font-bold text-gray-800 mb-2">
        Post-Election Discussion
       </h4>
       <p class="text-gray-600">
        Date: April 15, 2023
       </p>
       <p class="text-gray-600">
        Participate in a discussion about the election process and results.
       </p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-lg hidden transform transition-transform duration-300 hover:scale-105 hover:shadow-2xl">
       <img alt="Event 6: Future Plans" class="rounded-lg mb-4" height="400" src="https://storage.googleapis.com/a1aa/image/Ls8elaZHCAX9dCX8fO4wlpIHmcfk07A5QAPUibOLNc8xezaQB.jpg" width="600"/>
       <h4 class="text-2xl font-bold text-gray-800 mb-2">
        Future Plans
       </h4>
       <p class="text-gray-600">
        Date: April 20, 2023
       </p>
       <p class="text-gray-600">
        Discuss future plans and initiatives with the newly elected representatives.
       </p>
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

        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
  </script>
 </body>
</html>
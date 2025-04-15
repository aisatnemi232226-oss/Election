<html>
<head>
    <title>University Online Election System - Elections</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .tile:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .tile:active {
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
                <h1 class="text-3xl font-bold mb-4 md:mb-0">
                    <a href="{{ url('/') }}" class="text-inherit">University Online Election System</a>
                </h1>
            <nav>
                <button id="menu-toggle" class="md:hidden text-white focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
                <ul id="menu" class="hidden md:flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 text-center md:text-left">
                    <li><a class="hover:underline" href="{{ url('/') }}">Home</a></li>
                    <li><a class="hover:underline" href="{{ url('/election') }}">Election</a></li>
                    <li><a class="hover:underline" href="{{ url('/vote') }}">Vote</a></li>
                    <li><a class="hover:underline" href="{{ url('/result') }}">Results</a></li>
                    <li><a class="hover:underline" href="{{ url('/contact') }}">Contact</a></li>
                    <li><a class="hover:underline" href="{{ url('/login ') }}">Log In</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mx-auto p-4">
        <section class="my-8 text-center">
            <p class="text-gray-600 text-lg mb-4">Explore the different elections happening at our university.</p>
            <div class="flex justify-between items-center">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Current Elections</h2>
                <div>
                    <button class="bg-blue-600 text-white p-2 rounded-lg cursor-pointer hover:bg-blue-700 mr-2" id="prev-btn-current">&lt;</button>
                    <button class="bg-blue-600 text-white p-2 rounded-lg cursor-pointer hover:bg-blue-700" id="next-btn-current">&gt;</button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4" id="current-elections">
                <!-- Current Elections Tiles will be rendered here -->
            </div>
        </section>
        <section class="my-8">
            <div class="flex justify-between items-center">
                <h3 class="text-3xl font-bold text-gray-800 mb-4">Previous Elections</h3>
                <div>
                    <button class="bg-blue-600 text-white p-2 rounded-lg cursor-pointer hover:bg-blue-700 mr-2" id="prev-btn-previous">&lt;</button>
                    <button class="bg-blue-600 text-white p-2 rounded-lg cursor-pointer hover:bg-blue-700" id="next-btn-previous">&gt;</button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4" id="previous-elections">
                <!-- Previous Elections Tiles will be rendered here -->
            </div>
        </section>
        <section class="my-8">
            <h3 class="text-3xl font-bold text-gray-800 mb-4">Registration History</h3>
            <div class="bg-white p-6 rounded-lg shadow-lg max-h-64 overflow-y-auto">
                <ul class="divide-y divide-gray-200">
                    <li class="py-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <span class="text-gray-800 font-bold">Election 2023</span>
                            <span class="text-gray-600 mt-2 md:mt-0">Registered on Jan 15, 2023</span>
                        </div>
                    </li>
                    <li class="py-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <span class="text-gray-800 font-bold">Election 2022</span>
                            <span class="text-gray-600 mt-2 md:mt-0">Registered on Jan 15, 2022</span>
                        </div>
                    </li>
                    <li class="py-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <span class="text-gray-800 font-bold">Election 2021</span>
                            <span class="text-gray-600 mt-2 md:mt-0">Registered on Jan 15, 2021</span>
                        </div>
                    </li>
                    <li class="py-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <span class="text-gray-800 font-bold">Election 2020</span>
                            <span class="text-gray-600 mt-2 md:mt-0">Registered on Jan 15, 2020</span>
                        </div>
                    </li>
                    <li class="py-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <span class="text-gray-800 font-bold">Election 2019</span>
                            <span class="text-gray-600 mt-2 md:mt-0">Registered on Jan 15, 2019</span>
                        </div>
                    </li>
                    <li class="py-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <span class="text-gray-800 font-bold">Election 2018</span>
                            <span class="text-gray-600 mt-2 md:mt-0">Registered on Jan 15, 2018</span>
                        </div>
                    </li>
                    <li class="py-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <span class="text-gray-800 font-bold">Election 2017</span>
                            <span class="text-gray-600 mt-2 md:mt-0">Registered on Jan 15, 2017</span>
                        </div>
                    </li>
                    <li class="py-4">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                            <span class="text-gray-800 font-bold">Election 2016</span>
                            <span class="text-gray-600 mt-2 md:mt-0">Registered on Jan 15, 2016</span>
                        </div>
                    </li>
                </ul>
            </div>
        </section>
    </main>
    <footer class="bg-blue-600 text-white p-4 text-center">
        <p>© 2023 University Online Election System. All rights reserved.</p>
    </footer>
    <div class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-75 hidden" id="login-modal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Log In</h2>
            <form>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="login-email">Email</label>
                    <input class="w-full p-3 border border-gray-300 rounded-lg" id="login-email" name="login-email" required="" type="email"/>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="login-password">Password</label>
                    <input class="w-full p-3 border border-gray-300 rounded-lg" id="login-password" name="login-password" required="" type="password"/>
                </div>
                <div class="text-center">
                    <button class="bg-blue-600 text-white p-3 rounded-lg cursor-pointer hover:bg-blue-700" type="submit">Log In</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.querySelector('a[href="#"]').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('login-modal').classList.remove('hidden');
        });

        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');

        menuToggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        const currentElections = [
            {
                title: "Election 2025",
                description: "Vote for various positions including President, Vice President, Secretary, Treasurer, and Class Representatives.",
                imgSrc: "https://placehold.co/600x400?text=Election+2025",
                altText: "Image representing the Election 2025"
            },
            {
                title: "Presidential Election 2026",
                description: "Vote for the next student body president. Learn more about the candidates and their platforms.",
                imgSrc: "https://placehold.co/600x400?text=Presidential+Election+2026",
                altText: "Image representing the Presidential Election 2026"
            },
            {
                title: "Vice Presidential Election 2026",
                description: "Vote for the next student body vice president. Learn more about the candidates and their platforms.",
                imgSrc: "https://placehold.co/600x400?text=Vice+Presidential+Election+2026",
                altText: "Image representing the Vice Presidential Election 2026"
            },
            {
                title: "Secretary Election 2026",
                description: "Vote for the next student body secretary. Learn more about the candidates and their platforms.",
                imgSrc: "https://placehold.co/600x400?text=Secretary+Election+2026",
                altText: "Image representing the Secretary Election 2026"
            },
            {
                title: "Treasurer Election 2026",
                description: "Vote for the next student body treasurer. Learn more about the candidates and their platforms.",
                imgSrc: "https://placehold.co/600x400?text=Treasurer+Election+2026",
                altText: "Image representing the Treasurer Election 2026"
            },
            {
                title: "Class Representative Election 2026",
                description: "Vote for the next class representatives. Learn more about the candidates and their platforms.",
                imgSrc: "https://placehold.co/600x400?text=Class+Representative+Election+2026",
                altText: "Image representing the Class Representative Election 2026"
            }
        ];

        const previousElections = [
            {
                title: "Election 2024",
                description: "Review the results and highlights from the 2024 student government elections.",
                imgSrc: "https://placehold.co/600x400?text=Election+2024",
                altText: "Image representing the 2024 Election"
            },
            {
                title: "Election 2023",
                description: "Review the results and highlights from the 2023 student government elections.",
                imgSrc: "https://placehold.co/600x400?text=Election+2023",
                altText: "Image representing the 2023 Election"
            },
            {
                title: "Election 2022",
                description: "Review the results and highlights from the 2022 student government elections.",
                imgSrc: "https://placehold.co/600x400?text=Election+2022",
                altText: "Image representing the 2022 Election"
            },
            {
                title: "Election 2021",
                description: "Review the results and highlights from the 2021 student government elections.",
                imgSrc: "https://placehold.co/600x400?text=Election+2021",
                altText: "Image representing the 2021 Election"
            },
            {
                title: "Election 2020",
                description: "Review the results and highlights from the 2020 student government elections.",
                imgSrc: "https://placehold.co/600x400?text=Election+2020",
                altText: "Image representing the 2020 Election"
            },
            {
                title: "Election 2019",
                description: "Review the results and highlights from the 2019 student government elections.",
                imgSrc: "https://placehold.co/600x400?text=Election+2019",
                altText: "Image representing the 2019 Election"
            }
        ];

        let currentIndexCurrent = 0;
        let currentIndexPrevious = 0;

        function renderCurrentElections() {
            const container = document.getElementById('current-elections');
            container.innerHTML = '';
            for (let i = currentIndexCurrent; i < currentIndexCurrent + 3; i++) {
                const election = currentElections[i % currentElections.length];
                const tile = document.createElement('div');
                tile.className = 'bg-white p-6 rounded-lg shadow-lg transition-transform duration-300 tile';
                tile.innerHTML = `
                    <img alt="${election.altText}" class="rounded-lg mb-4 w-full h-48 object-cover" src="${election.imgSrc}"/>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">${election.title}</h3>
                    <p class="text-gray-600 mb-4">${election.description}</p>
                `;
                container.appendChild(tile);
            }
        }

        function renderPreviousElections() {
            const container = document.getElementById('previous-elections');
            container.innerHTML = '';
            for (let i = currentIndexPrevious; i < currentIndexPrevious + 3; i++) {
                const election = previousElections[i % previousElections.length];
                const tile = document.createElement('div');
                tile.className = 'bg-white p-6 rounded-lg shadow-lg transition-transform duration-300 tile';
                tile.innerHTML = `
                    <img alt="${election.altText}" class="rounded-lg mb-4 w-full h-48 object-cover" src="${election.imgSrc}"/>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">${election.title}</h3>
                    <p class="text-gray-600 mb-4">${election.description}</p>
                `;
                container.appendChild(tile);
            }
        }

        document.getElementById('next-btn-current').addEventListener('click', () => {
            currentIndexCurrent = (currentIndexCurrent + 3) % currentElections.length;
            renderCurrentElections();
        });

        document.getElementById('prev-btn-current').addEventListener('click', () => {
            currentIndexCurrent = (currentIndexCurrent - 3 + currentElections.length) % currentElections.length;
            renderCurrentElections();
        });

        document.getElementById('next-btn-previous').addEventListener('click', () => {
            currentIndexPrevious = (currentIndexPrevious + 3) % previousElections.length;
            renderPreviousElections();
        });

        document.getElementById('prev-btn-previous').addEventListener('click', () => {
            currentIndexPrevious = (currentIndexPrevious - 3 + previousElections.length) % previousElections.length;
            renderPreviousElections();
        });

        renderCurrentElections();
        renderPreviousElections();
    </script>
</body>
</html>
<header class="bg-blue-600 text-white p-4 fixed top-0 left-0 w-full shadow-md transition-transform duration-300 z-50">
    <div class="container mx-auto flex flex-col items-center md:flex-row md:justify-between">
        <h1 class="text-3xl font-bold mb-4 md:mb-0">
            <a href="{{ route('dashboard') }}" class="text-inherit">
                University Online Election System - 
                <span id="dashboard-section-title">
                    @yield('dashboard-title', 'Admin Dashboard')
                </span>
            </a>
        </h1>
        <nav class="w-full md:w-auto">
            <div class="flex justify-center md:hidden">
                <button class="text-white focus:outline-none" id="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <ul class="hidden md:flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-0 text-center md:text-left -mx-8 px-4 py-2" id="menu">
                <li class="px-4"><a class="hover:underline" href="{{ route('dashboard') }}">Dashboard</a></li>

                <li class="relative group">
                    <a id="election-management-toggle" class="hover:underline cursor-pointer block">Election Management</a>
                    <ul id="election-management-menu" class="absolute left-0 mt-1 hidden bg-white text-gray-800 shadow-lg rounded-lg w-56 z-50 p-2 group-hover:block">
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.elections.index') }}">Manage Elections</a></li>
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.candidates.index') }}">Manage Candidates</a></li>
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.positions.index') }}">Manage Positions</a></li>
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.voters.index') }}">Manage Voters</a></li>
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.election-articles.index') }}">Manage Election Articles</a></li>
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.results.index') }}">View Results</a></li>
                    </ul>
                </li>

                <li class="px-4"><a class="hover:underline" href="{{ route('admin.reports.index') }}">Reports</a></li>

                <li class="relative group">
                    <a id="settings-toggle" class="hover:underline cursor-pointer block">Settings</a>
                    <ul id="settings-menu" class="absolute left-0 mt-1 hidden bg-white text-gray-800 shadow-lg rounded-lg w-56 z-50 p-2 group-hover:block">
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.admin-roles.index') }}">Admin Roles</a></li>
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.profile-settings.index') }}">Profile Settings</a></li>
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="#">User Management</a></li>
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.audit-logs.index') }}">Audit Logs</a></li>
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.verified-voters.index') }}">Verify Voters</a></li>
                        <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.academic-year.index') }}">Set Current Academic Year</a></li>
                    </ul>
                </li>

                <li class="px-4">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>

                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="hover:underline">
                        Log Out
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let lastScrollTop = 0;
        const navbar = document.querySelector("header");
        let scrollTimeout;

        window.addEventListener("scroll", function () {
            clearTimeout(scrollTimeout);
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop === 0) {
                navbar.style.transform = "translateY(0)";
            } else if (scrollTop > lastScrollTop) {
                navbar.style.transform = "translateY(-100%)";
            } else {
                navbar.style.transform = "translateY(0)";
            }

            lastScrollTop = scrollTop;

            // Keep navbar visible for a short period before hiding it
            scrollTimeout = setTimeout(() => {
                if (window.pageYOffset > 100) {
                    navbar.style.transform = "translateY(-100%)";
                }
            }, 1500); // 1.5 seconds before hiding again
        });
    });
</script>

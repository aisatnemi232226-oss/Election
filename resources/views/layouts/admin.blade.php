<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>University Online Election System - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <link rel="icon" href="{{ URL::to('/images/logo.png') }}">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .tile { transition: transform 0.3s, box-shadow 0.3s; }
        .tile:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); }
        .tile:active { transform: translateY(-2px); box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); }
        .no-wrap { white-space: nowrap; }
    </style>
</head>
<body class="flex flex-col min-h-screen bg-gray-100 pt-16">

    @include('layouts.admin-header')

    <main class="flex-1 container mx-auto p-4">
        @yield('content')
    </main>

    @include('layouts.admin-footer')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function setupDropdown(toggleId, menuId) {
                const toggle = document.getElementById(toggleId);
                const menu = document.getElementById(menuId);
                if (!toggle || !menu) return;

                let hoverTimeout;
                toggle.addEventListener("mouseenter", function () {
                    clearTimeout(hoverTimeout);
                    menu.classList.remove("hidden");
                });
                menu.addEventListener("mouseenter", function () {
                    clearTimeout(hoverTimeout);
                    menu.classList.remove("hidden");
                });
                toggle.addEventListener("mouseleave", function () {
                    hoverTimeout = setTimeout(() => {
                        if (!menu.matches(":hover")) {
                            menu.classList.add("hidden");
                        }
                    }, 300);
                });
                menu.addEventListener("mouseleave", function () {
                    menu.classList.add("hidden");
                });
            }

            setupDropdown("election-management-toggle", "election-management-menu");
            setupDropdown("settings-toggle", "settings-menu");

            const menuToggle = document.getElementById("menu-toggle");
            const menu = document.getElementById("menu");
            if (menuToggle && menu) {
                menuToggle.addEventListener("click", () => {
                    menu.classList.toggle("hidden");
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        (function () {
            var theme = 'light';
            try {
                theme = localStorage.getItem('pcu-theme') || 'light';
            } catch (error) {
                theme = 'light';
            }
            document.documentElement.setAttribute('data-theme', theme === 'dark' ? 'dark' : 'light');
        })();
    </script>
    <title>@yield('title', 'Philippine Christian University Online Election System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/pculogo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/pcu-theme.css') }}">
    <script src="{{ asset('js/pcu-theme.js') }}" defer></script>
    @stack('styles')
</head>
<body class="@yield('body-class')">
    <a class="skip-link" href="#main-content">Skip to content</a>
    @include('layouts.partials.header')
    <main id="main-content">
        @yield('content')
    </main>
    @include('layouts.partials.footer')
    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Philippine Christian University Online Election System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="relative min-h-screen bg-gray-100 text-gray-800">
    <div class="pointer-events-none fixed inset-0 flex items-center justify-center opacity-20" aria-hidden="true">
        <img src="{{ asset('images/pculogo.png') }}" alt="" class="w-[62vw] max-w-[820px] h-auto">
    </div>

    <header class="relative bg-blue-600 p-4 text-white">
        <div class="container mx-auto">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <img src="{{ asset('images/pculogo.png') }}" alt="School Logo" class="h-16 w-16 object-contain">
                <span class="text-xl font-bold md:text-2xl">Philippine Christian University Online Election System</span>
            </a>
        </div>
    </header>

    <main class="relative container mx-auto px-4 py-12">
        <section class="mx-auto max-w-4xl text-center">
            <h1 class="mb-3 text-3xl font-bold md:text-4xl">Register</h1>
            <p class="mb-10 text-lg text-gray-600">Choose the type of registration you need.</p>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <a href="{{ route('candidate.register') }}" class="group rounded-xl bg-white p-8 text-center shadow-lg transition hover:-translate-y-1 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-300">
                    <i class="fas fa-user-graduate mb-5 text-5xl text-blue-600" aria-hidden="true"></i>
                    <h2 class="mb-3 text-2xl font-bold">Candidate Registration</h2>
                    <p class="mb-6 text-gray-600">Apply to run for a student representative position.</p>
                    <span class="inline-block rounded-lg bg-blue-600 px-6 py-3 font-semibold text-white group-hover:bg-blue-700">Continue as Candidate</span>
                </a>

                <a href="{{ auth()->check() ? route('register.verify') : route('login') }}" class="group rounded-xl bg-white p-8 text-center shadow-lg transition hover:-translate-y-1 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-green-300">
                    <i class="fas fa-vote-yea mb-5 text-5xl text-green-600" aria-hidden="true"></i>
                    <h2 class="mb-3 text-2xl font-bold">Voter Registration</h2>
                    <p class="mb-6 text-gray-600">Submit your existing student account for administrator verification.</p>
                    <span class="inline-block rounded-lg bg-green-600 px-6 py-3 font-semibold text-white group-hover:bg-green-700">{{ auth()->check() ? 'Continue to Verification' : 'Log In to Verify' }}</span>
                </a>
            </div>

            <a href="{{ url('/') }}" class="mt-8 inline-block text-blue-700 hover:underline">Back to Home</a>
        </section>
    </main>
</body>
</html>

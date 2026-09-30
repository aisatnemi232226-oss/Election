<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - Philippine Christian University Online Election System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="relative min-h-screen bg-blue-900 text-center text-white">
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden" aria-hidden="true">
        <img src="{{ asset('images/background1.jpg') }}" alt="" class="h-full w-full object-cover object-bottom">
    </div>
    <div class="pointer-events-none fixed inset-0 flex items-center justify-center opacity-30" aria-hidden="true">
        <img src="{{ asset('images/pculogo.png') }}" alt="" class="w-[62vw] max-w-[820px] h-auto">
    </div>
    <a href="{{ route('home') }}" class="fixed left-4 top-4 z-20 inline-flex items-center gap-2 rounded-lg bg-white/95 px-4 py-2 text-sm font-bold text-blue-700 shadow-md hover:bg-white focus:outline-none focus:ring-2 focus:ring-white" aria-label="Back to home">
        <i class="fas fa-arrow-left" aria-hidden="true"></i>
        <i class="fas fa-home" aria-hidden="true"></i>
        <span>Home</span>
    </a>

    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-16">
        <section class="w-full max-w-md rounded-lg bg-white p-8 text-left text-gray-800 shadow-lg">
            <h1 class="mb-2 text-center text-3xl font-bold">Create Account</h1>
            <p class="mb-6 text-center text-sm text-gray-600">Create your account to access the election system.</p>

            <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-900" role="note">
                Creating an account does not verify voter eligibility. You can apply for voter verification separately after signing in.
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert" aria-live="assertive">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="mb-1 block text-sm font-bold" for="idnumber">Student Number</label>
                    <input class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" id="idnumber" name="idnumber" type="text" inputmode="numeric" autocomplete="off" value="{{ old('idnumber', $prefillIdNumber) }}" required>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-bold" for="first_name">First Name</label>
                        <input class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" id="first_name" name="first_name" type="text" autocomplete="given-name" value="{{ old('first_name', $prefillFirstName) }}" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-bold" for="middle_initial">Middle Initial</label>
                        <input class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" id="middle_initial" name="middle_initial" type="text" maxlength="10" autocomplete="additional-name" value="{{ old('middle_initial', $prefillMiddleInitial) }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-sm font-bold" for="last_name">Last Name</label>
                        <input class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" id="last_name" name="last_name" type="text" autocomplete="family-name" value="{{ old('last_name', $prefillLastName) }}" required>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-bold" for="email">Email</label>
                    <input class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" id="email" name="email" type="email" autocomplete="email" value="{{ old('email', $prefillEmail) }}" required>
                    <p class="mt-1 text-xs text-gray-500">Use your PCU email address ending in @pcu.edu.ph.</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-bold" for="password">Password</label>
                    <div class="relative">
                        <input class="w-full rounded border border-gray-300 px-3 py-2 pr-12 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
                        <button class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-gray-600 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" type="button" data-password-toggle="password" aria-controls="password" aria-label="Show password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Use at least 8 characters.</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-bold" for="password_confirmation">Confirm Password</label>
                    <div class="relative">
                        <input class="w-full rounded border border-gray-300 px-3 py-2 pr-12 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
                        <button class="absolute inset-y-0 right-0 inline-flex items-center px-3 text-gray-600 hover:text-blue-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" type="button" data-password-toggle="password_confirmation" aria-controls="password_confirmation" aria-label="Show confirmation password" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button class="w-full rounded-lg bg-blue-600 px-4 py-3 font-bold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" type="submit">Create Account</button>
            </form>

            <p class="mt-5 text-center text-sm text-gray-700">
                Already have an account?
                <a class="font-bold text-blue-600 hover:text-blue-800" href="{{ route('login') }}">Log in</a>
            </p>
        </section>
    </main>
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const passwordInput = document.getElementById(toggle.dataset.passwordToggle);
                const isVisible = passwordInput.type === 'text';

                passwordInput.type = isVisible ? 'password' : 'text';
                toggle.setAttribute('aria-pressed', String(!isVisible));
                toggle.setAttribute(
                    'aria-label',
                    `${isVisible ? 'Show' : 'Hide'} ${passwordInput.id === 'password' ? 'password' : 'confirmation password'}`
                );
                toggle.querySelector('i').classList.toggle('fa-eye', isVisible);
                toggle.querySelector('i').classList.toggle('fa-eye-slash', !isVisible);
            });
        });
    </script>
    @if (session('status'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Account Created!',
                text: @json(session('status')),
                confirmButtonText: 'Continue to Login',
                confirmButtonColor: '#2563eb',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = @json(route('login'));
                }
            });
        </script>
    @endif
</body>
</html>

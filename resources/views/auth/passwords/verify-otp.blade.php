<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify code</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 px-4 py-12 text-gray-800">
    <main class="mx-auto w-full max-w-md rounded-lg bg-white p-8 shadow">
        <h1 class="text-2xl font-bold">Check your email</h1>
        <p class="mt-2 text-sm text-gray-600">If an account exists for that address, a six-digit code has been sent. It expires in 10 minutes.</p>

        @if (session('mail_error'))
            <p class="mt-5 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700" role="alert">{{ session('mail_error') }}</p>
        @endif

        @error('otp')
            <p class="mt-5 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $message }}</p>
        @enderror

        <form class="mt-6 space-y-4" method="POST" action="{{ route('password.otp.verify') }}">
            @csrf
            <div>
                <label for="otp" class="mb-1 block text-sm font-medium">Verification code</label>
                <input id="otp" name="otp" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required autofocus
                    class="w-full rounded border border-gray-300 px-3 py-2 text-center text-xl tracking-[0.4em] focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <button type="submit" class="w-full rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">Verify code</button>
        </form>

        <form class="mt-4 text-center" method="POST" action="{{ route('password.email') }}">
            @csrf
            <input type="hidden" name="email" value="{{ session('password_otp_email') }}">
            <button type="submit" class="text-sm font-semibold text-blue-700 hover:text-blue-900">Send a new code</button>
        </form>
    </main>
</body>
</html>
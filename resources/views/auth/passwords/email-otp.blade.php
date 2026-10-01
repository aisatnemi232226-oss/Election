<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="min-h-screen bg-gray-100 px-4 py-12 text-gray-800">
    <main class="mx-auto w-full max-w-md rounded-lg bg-white p-8 shadow">
        <a href="{{ route('login') }}" class="text-sm font-semibold text-blue-700">&larr; Back to login</a>
        <h1 class="mt-6 text-2xl font-bold">Forgot password?</h1>
        <p class="mt-2 text-sm text-gray-600">Enter the email address on your account and we will send a verification code.</p>

        @if (session('status') || session('mail_error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    Swal.fire({
                        icon: @json(session('mail_error') ? 'error' : 'success'),
                        title: @json(session('mail_error') ? 'Email not sent' : 'Check your email'),
                        text: @json(session('mail_error') ?? session('status')),
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#1d4ed8'
                    });
                });
            </script>
        @endif
        @error('email')
            <p class="mt-5 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $message }}</p>
        @enderror

        <form class="mt-6 space-y-4" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div>
                <label for="email" class="mb-1 block text-sm font-medium">Email address</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <button type="submit" class="w-full rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">Send verification code</button>
        </form>
        <a href="{{ route('password.recovery-qr') }}" class="mt-5 block text-center text-sm font-semibold text-blue-700 hover:text-blue-900">Use my recovery QR instead</a>
    </main>
</body>
</html>
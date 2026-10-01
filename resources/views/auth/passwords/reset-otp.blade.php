<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose a new password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 px-4 py-12 text-gray-800">
    <main class="mx-auto w-full max-w-md rounded-lg bg-white p-8 shadow">
        <a href="{{ route('login') }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">&larr; Back to login</a>
        <h1 class="mt-6 text-2xl font-bold">Choose a new password</h1>
        <p class="mt-2 text-sm text-gray-600">Your new password must be at least 8 characters long.</p>

        <form class="mt-6 space-y-4" method="POST" action="{{ route('password.update') }}">
            @csrf
            <div>
                <label for="password" class="mb-1 block text-sm font-medium">New password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                        class="w-full rounded border border-gray-300 px-3 py-2 pr-20 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <button type="button" data-password-toggle="password" aria-controls="password" aria-pressed="false"
                        class="absolute inset-y-0 right-3 text-sm font-semibold text-blue-700 hover:text-blue-900">Show</button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium">Confirm new password</label>
                <div class="relative">
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                        class="w-full rounded border border-gray-300 px-3 py-2 pr-20 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <button type="button" data-password-toggle="password_confirmation" aria-controls="password_confirmation" aria-pressed="false"
                        class="absolute inset-y-0 right-3 text-sm font-semibold text-blue-700 hover:text-blue-900">Show</button>
                </div>
            </div>
            <button type="submit" class="w-full rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">Update password</button>
        </form>
    </main>
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.passwordToggle);
                const isVisible = input.type === 'password';

                input.type = isVisible ? 'text' : 'password';
                button.textContent = isVisible ? 'Hide' : 'Show';
                button.setAttribute('aria-pressed', String(isVisible));
            });
        });
    </script>
</body>
</html>
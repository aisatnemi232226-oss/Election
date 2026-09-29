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
        <h1 class="text-2xl font-bold">Choose a new password</h1>
        <p class="mt-2 text-sm text-gray-600">Your new password must be at least 8 characters long.</p>

        <form class="mt-6 space-y-4" method="POST" action="{{ route('password.update') }}">
            @csrf
            <div>
                <label for="password" class="mb-1 block text-sm font-medium">New password</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">
                @error('password')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                    class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <button type="submit" class="w-full rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">Update password</button>
        </form>
    </main>
</body>
</html>
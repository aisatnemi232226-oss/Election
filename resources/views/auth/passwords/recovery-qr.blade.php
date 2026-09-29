<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Account QR codes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 px-4 py-12 text-gray-800">
    <main class="mx-auto w-full max-w-4xl rounded-lg bg-white p-8 text-center shadow">
        <a href="{{ $continueTo }}" class="text-left text-sm font-semibold text-blue-700">&larr; Continue</a>
        <h1 class="mt-6 text-2xl font-bold">Account QR codes</h1>
        <p class="mt-2 text-sm text-gray-600">Keep both codes private. Anyone with your login QR can sign in; your recovery QR can reset your password once.</p>

        @if (session('status'))
            <p class="mt-5 rounded border border-blue-200 bg-blue-50 p-3 text-left text-sm text-blue-900" role="status">{{ session('status') }}</p>
        @endif

        <div class="mt-7 grid grid-cols-1 items-stretch gap-6 md:grid-cols-2">
        <section class="flex flex-col items-center rounded border border-gray-200 p-5">
            <h2 class="text-lg font-semibold">Login QR</h2>
            @if ($loginSecret)
                <p class="mt-2 text-sm text-gray-600">Download and save this reusable sign-in code. Generating a replacement invalidates this one.</p>
                <div id="login-qr" class="mx-auto mt-4 flex min-h-52 w-52 items-center justify-center border border-gray-200 p-2" aria-label="Your private login QR"></div>
                <button id="download-login-qr" type="button" class="mt-3 rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">Download login QR</button>
            @else
                <p class="mt-2 text-sm text-gray-600">No login QR is being displayed. Generate a new one to download it.</p>
            @endif
            <form class="mt-4" method="POST" action="{{ route('account.login-qr.generate') }}">
                @csrf
                <button type="submit" class="w-full rounded bg-gray-800 px-4 py-2 font-semibold text-white hover:bg-black">
                    {{ $loginSecret ? 'Generate replacement login QR' : 'Generate login QR' }}
                </button>
            </form>
        </section>

        <section class="flex flex-col items-center rounded border border-gray-200 p-5">
            <h2 class="text-lg font-semibold">Password recovery QR</h2>
            @if ($recoverySecret)
                <p class="mt-2 text-sm text-gray-600">Save this one-time recovery code. Scanning it to reset your password invalidates it.</p>
                <div id="recovery-qr" class="mx-auto mt-4 flex min-h-52 w-52 items-center justify-center border border-gray-200 p-2" aria-label="Your private password recovery QR"></div>
                <button id="download-recovery-qr" type="button" class="mt-3 rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">Download recovery QR</button>
            @else
                <p class="mt-2 text-sm text-gray-600">No recovery QR is being displayed. Generate a new one to download it.</p>
            @endif
            <form class="mt-4" method="POST" action="{{ route('account.recovery-qr.generate') }}">
                @csrf
                <button type="submit" class="w-full rounded bg-gray-800 px-4 py-2 font-semibold text-white hover:bg-black">
                    {{ $recoverySecret ? 'Generate replacement recovery QR' : 'Generate recovery QR' }}
                </button>
            </form>
        </section>
        </div>
    </main>

    @if ($loginSecret || $recoverySecret)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            function renderDownloadableQr(containerId, buttonId, filename, payload) {
                const container = document.getElementById(containerId);
                new QRCode(container, { text: JSON.stringify(payload), width: 192, height: 192 });
                document.getElementById(buttonId).addEventListener('click', function () {
                    const image = container.querySelector('img');
                    const canvas = container.querySelector('canvas');
                    const link = document.createElement('a');
                    link.download = filename;
                    link.href = image?.src || canvas?.toDataURL('image/png') || '';
                    if (link.href) link.click();
                });
            }

            @if ($loginSecret)
                renderDownloadableQr('login-qr', 'download-login-qr', 'account-login-qr.png', {
                    purpose: 'login',
                    user_id: @json($userId),
                    secret: @json($loginSecret),
                });
            @endif

            @if ($recoverySecret)
                renderDownloadableQr('recovery-qr', 'download-recovery-qr', 'password-recovery-qr.png', {
                    purpose: 'password-recovery',
                    user_id: @json($userId),
                    secret: @json($recoverySecret),
                });
            @endif
        </script>
    @endif
</body>
</html>
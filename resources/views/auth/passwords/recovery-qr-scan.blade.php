<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recover with QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        window.recoveryScannerReady = new Promise(function (resolve, reject) {
            const sources = [
                'https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js',
                'https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js',
            ];

            function loadSource(index) {
                if (index >= sources.length) {
                    reject(new Error('QR scanner library failed to load.'));
                    return;
                }

                const script = document.createElement('script');
                script.src = sources[index];
                script.async = true;
                script.onload = function () {
                    if (window.Html5Qrcode) {
                        resolve();
                    } else {
                        loadSource(index + 1);
                    }
                };
                script.onerror = function () {
                    loadSource(index + 1);
                };
                document.head.appendChild(script);
            }

            loadSource(0);
        });
    </script>
</head>
<body class="min-h-screen bg-gray-100 px-4 py-12 text-gray-800">
    <main class="mx-auto w-full max-w-md rounded-lg bg-white p-8 shadow">
        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-blue-700">&larr; Use email recovery</a>
        <h1 class="mt-6 text-2xl font-bold">Scan recovery QR</h1>
        <p class="mt-2 text-sm text-gray-600">Scan the private password recovery QR you saved. It can be used once and expires after a successful scan.</p>
        <div id="qr-reader" class="mt-6 w-full overflow-hidden"></div>
        <p id="scan-status" class="mt-4 min-h-6 text-sm text-gray-700" role="status" aria-live="polite"></p>
        <button id="start-scan" type="button" class="mt-3 w-full rounded bg-blue-700 px-4 py-2 font-semibold text-white hover:bg-blue-800">Start camera</button>
    </main>

    <script>
        const scanStatus = document.getElementById('scan-status');
        const startButton = document.getElementById('start-scan');
        let scanner = null;
        let scanning = false;
        let submitting = false;

        async function stopScanner() {
            if (scanner && scanning) {
                await scanner.stop().catch(() => {});
                scanning = false;
            }
        }

        async function submitRecoveryQr(rawQr) {
            if (submitting) return;
            submitting = true;
            scanStatus.textContent = 'Checking recovery QR…';

            try {
                await stopScanner();
                const response = await fetch("{{ route('password.recovery-qr.verify') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ qr: rawQr }),
                });
                const result = await response.json();

                if (!response.ok || !result.ok || !result.redirect) {
                    throw new Error(result.message || 'That recovery QR is invalid or expired.');
                }

                window.location.assign(result.redirect);
            } catch (error) {
                scanStatus.textContent = error.message || 'Could not verify the QR. Try again.';
                submitting = false;
            }
        }

        startButton.addEventListener('click', async function () {
            startButton.disabled = true;

            try {
                scanStatus.textContent = 'Loading QR scanner…';
                await window.recoveryScannerReady;
                scanStatus.textContent = 'Starting camera…';
                scanner = new window.Html5Qrcode('qr-reader');
                await scanner.start(
                    { facingMode: 'environment' },
                    { fps: 10, qrbox: 240 },
                    submitRecoveryQr,
                    () => {}
                );
                scanning = true;
                scanStatus.textContent = 'Point the camera at your saved recovery QR.';
            } catch (error) {
                scanStatus.textContent = error.message === 'QR scanner library failed to load.'
                    ? 'QR scanner could not load from either CDN. Check your internet connection or browser network settings.'
                    : 'Camera access failed. Allow camera permission and try again.';
                startButton.disabled = false;
            }
        });
    </script>
</body>
</html>
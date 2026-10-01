<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Philippine Christian University Online Election System - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <script>
        (function () {
            function loadScript(src, onload, onerror) {
                var s = document.createElement('script');
                s.src = src;
                s.async = true;
                s.onload = onload;
                s.onerror = onerror;
                document.head.appendChild(s);
            }

            loadScript(
                'https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js',
                function () {},
                function () {
                    loadScript('https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js');
                }
            );
        })();
    </script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        @keyframes qrScanLine {
            0% { transform: translateY(0%); opacity: 0.9; }
            50% { opacity: 1; }
            100% { transform: translateY(320px); opacity: 0.9; }
        }

        .qr-scan-overlay {
            pointer-events: none;
        }

        .qr-scan-line {
            position: absolute;
            left: 12px;
            right: 12px;
            top: 12px;
            height: 3px;
            background: linear-gradient(90deg, rgba(59,130,246,0), rgba(59,130,246,1), rgba(59,130,246,0));
            box-shadow: 0 0 12px rgba(59,130,246,0.9);
            animation: qrScanLine 1.6s linear infinite;
        }

        .qr-scan-glow {
            position: absolute;
            inset: 12px;
            border-radius: 12px;
            border: 2px solid rgba(59,130,246,0.55);
            box-shadow: 0 0 0 1px rgba(59,130,246,0.2), 0 0 24px rgba(59,130,246,0.25);
        }
    </style>
</head>
<body class="relative bg-blue-900 text-center text-white flex items-center justify-center h-screen">
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden" aria-hidden="true">
        <img src="{{ asset('images/background1.jpg') }}" alt="" class="h-full w-full object-cover object-bottom">
    </div>
    <div class="pointer-events-none fixed inset-0 flex items-center justify-center opacity-30 z-0" aria-hidden="true">
        <img src="{{ asset('images/pculogo.png') }}" alt="" class="w-[62vw] max-w-[820px] h-auto" />
    </div>
    <a href="{{ route('home') }}"
        class="fixed top-4 left-4 z-20 inline-flex items-center gap-2 rounded-lg bg-white/95 px-4 py-2 text-sm font-bold text-blue-700 shadow-md transition hover:bg-white hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-blue-900"
        aria-label="Back to home">
        <i class="fas fa-arrow-left" aria-hidden="true"></i>
        <i class="fas fa-home" aria-hidden="true"></i>
        <span>Home</span>
    </a>
    <div class="relative z-10 bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Philippine Christian University Online Election System</h2>
        <form action="{{route('login')}}" method="POST">
            @csrf
            @if ($errors->any())
                <div id="login-error-notification" class="fixed top-4 right-4 z-50 w-[calc(100%-2rem)] max-w-md rounded-lg border border-red-200 bg-white p-4 text-left text-red-700 shadow-xl" role="alert" aria-live="assertive">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle mt-0.5 text-red-500" aria-hidden="true"></i>
                        <ul class="flex-1 space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="text-red-400 hover:text-red-700" aria-label="Dismiss notification" onclick="document.getElementById('login-error-notification').remove()">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <script>
                    setTimeout(function () {
                        const notification = document.getElementById('login-error-notification');
                        if (notification) {
                            notification.remove();
                        }
                    }, 6000);
                </script>
            @endif<div class="mb-4 flex items-center">
                <div class="flex-grow">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                    <div class="relative">
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 pr-12 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" name="email" type="text" placeholder="Username" value="{{ old('email') }}" required>
                        <button class="absolute inset-y-0 right-0 z-10 flex items-center bg-green-600 text-white p-2 rounded-r-lg hover:bg-green-700" type="button" id="scan-qr-button">
                            <i class="fas fa-qrcode"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" placeholder="Password" required>
            </div>
            <div class="flex items-center justify-between mb-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700" type="submit">Login</button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800" href="{{route('password.request') }}">Forgot Password?</a>
            </div>
            <div class="text-center">
                <a class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800" href="{{ route('register') }}">Create Account</a>
            </div>
        </form>
    </div>

    <div id="qr-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div id="qr-modal-backdrop" class="absolute inset-0 bg-gray-900/70"></div>
        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-md rounded-lg bg-white shadow-lg">
                <div class="flex items-center justify-between border-b px-4 py-3">
                    <span class="text-gray-800 text-sm font-bold">Scan QR Code</span>
                    <button class="text-gray-600 hover:text-gray-800" type="button" id="close-qr-button" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <div class="relative overflow-hidden rounded-lg bg-black/5">
                        <div id="qr-reader"></div>
                        <div class="qr-scan-overlay absolute inset-0">
                            <div class="qr-scan-glow"></div>
                            <div class="qr-scan-line"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Password reset',
                    text: @json(session('status')),
                    confirmButtonText: 'Continue to login',
                    confirmButtonColor: '#2563eb'
                });
            });
        </script>
    @endif
    <script>
        let html5QrCode = null;
        let isScanning = false;

        function openQrModal() {
            const modal = document.getElementById('qr-modal');
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeQrModal() {
            const modal = document.getElementById('qr-modal');
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
        }

        async function loginViaQr(qrValue) {
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const csrf = tokenEl ? tokenEl.getAttribute('content') : '';

            const res = await fetch("{{ route('login.qr') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ qr: (qrValue || '').trim() }),
            });

            let data = null;
            try {
                data = await res.json();
            } catch (e) {
                data = null;
            }

            if (data && data.ok === false && data.action === 'register' && data.field && data.value) {
                const url = new URL("{{ route('register') }}", window.location.origin);
                if (data.idnumber) {
                    url.searchParams.set('idnumber', data.idnumber);
                }

                if (data.email) {
                    url.searchParams.set('email', data.email);
                }

                if (!data.idnumber && !data.email) {
                    url.searchParams.set(data.field, data.value);
                }

                if (data.name) {
                    url.searchParams.set('name', data.name);
                }
                window.location.href = url.toString();
                return;
            }

            if (!res.ok || !data || data.ok !== true || !data.redirect) {
                throw new Error((data && data.message) ? data.message : 'QR login failed');
            }

            window.location.href = data.redirect;
        }

        async function stopScanner() {
            if (!html5QrCode || !isScanning) {
                closeQrModal();
                return;
            }

            try {
                await html5QrCode.stop();
            } catch (err) {
                console.error(err);
            } finally {
                closeQrModal();
                isScanning = false;
                html5QrCode = null;
                document.getElementById('qr-reader').innerHTML = '';
            }
        }

        document.getElementById('close-qr-button').addEventListener('click', function() {
            stopScanner();
        });

        document.getElementById('qr-modal-backdrop').addEventListener('click', function() {
            stopScanner();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                stopScanner();
            }
        });

        document.getElementById('scan-qr-button').addEventListener('click', async function() {
            if (typeof Html5Qrcode === 'undefined') {
                alert('QR scanner failed to load. Please refresh the page or check your internet connection.');
                return;
            }

            if (isScanning) {
                await stopScanner();
                return;
            }

            openQrModal();
            html5QrCode = new Html5Qrcode("qr-reader");
            isScanning = true;

            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: 250
                },
                async qrCodeMessage => {
                    try {
                        await stopScanner();
                        await loginViaQr(qrCodeMessage);
                    } catch (e) {
                        console.error(e);
                        alert(e && e.message ? e.message : 'Invalid QR code');
                    }
                },
                errorMessage => {
                    console.warn(`QR Code no longer in front of camera.`);
                }
            ).catch(async err => {
                console.error(err);
                await stopScanner();
            });
        });
    </script>
</body>
</html>
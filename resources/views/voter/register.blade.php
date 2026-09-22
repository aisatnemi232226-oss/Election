<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Philippine Christian University Online Election System - Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<body class="relative bg-[url('{{ asset('images/background1.jpg') }}')] bg-bottom bg-cover text-center text-white flex items-center justify-center h-screen">
    <div class="pointer-events-none fixed inset-0 flex items-center justify-center opacity-30 z-0" aria-hidden="true">
        <img src="{{ asset('images/pculogo.png') }}" alt="" class="w-[62vw] max-w-[820px] h-auto" />
    </div>
    <div class="relative z-10 bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">Philippine Christian University Online Election System - Registration</h2>
        <form action="{{route('register')}}" method="POST">
            @csrf
            @if (!empty($redirectTo ?? ''))
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            @endif
            @if ($errors->any())
                <div class="mb-4 text-red-600 text-sm text-left">
                    <ul class="list-disc list-inside">

                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="idnumber">Student Number</label>
                <div class="relative">
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline pr-12" id="idnumber" name="idnumber" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="Student Number" value="{{ old('idnumber', $prefillIdNumber ?? '') }}" oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
                    <button class="absolute inset-y-0 right-0 z-10 flex items-center bg-green-600 text-white p-2 rounded-r-lg hover:bg-green-700" type="button" id="scan-qr-button">
                        <i class="fas fa-qrcode"></i>
                    </button>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="fullname">Full Name</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="fullname" name="name" type="text" placeholder="Full Name" value="{{ old('name', $prefillName ?? '') }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" name="email" type="email" placeholder="Email" value="{{ old('email', $prefillEmail ?? '') }}" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" name="password" type="password" placeholder="Password" required>
            </div>

            <div class="flex items-center justify-between mb-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 w-full" type="submit">Register</button>
            </div>
            <div class="text-center">
                <p class="text-gray-700 text-sm">Already have an account? <a class="font-bold text-blue-600 hover:text-blue-800" href="{{ url('/login') }}">Login</a></p>
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
</body>

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

        if (data && data.ok === false && data.action === 'register') {
            return { action: 'register', data };
        }

        if (!res.ok || !data || data.ok !== true || !data.redirect) {
            throw new Error((data && data.message) ? data.message : 'QR login failed');
        }

        window.location.href = data.redirect;
        return { action: 'redirect' };
    }

    function parseQrPayload(raw) {
        const text = (raw || '').trim();
        if (!text) return null;

        if (text.includes(';')) {
            const segs = text.split(';').map(s => s.trim()).filter(Boolean);
            if (segs.length >= 2) {
                return {
                    idnumber: segs[0] || '',
                    name: segs[1] || '',
                    email: segs[2] || '',
                };
            }
        }

        if (text[0] === '{') {
            try {
                const obj = JSON.parse(text);
                if (obj && typeof obj === 'object') {
                    return obj;
                }
            } catch (e) {}
        }

        if (text.startsWith('http://') || text.startsWith('https://')) {
            try {
                const u = new URL(text);
                return {
                    idnumber: u.searchParams.get('idnumber') || '',
                    name: u.searchParams.get('name') || '',
                    email: u.searchParams.get('email') || '',
                };
            } catch (e) {}
        }

        const normalized = text.replace(/\r/g, '\n');
        const parts = normalized.split(/[\n,;|]+/).map(p => p.trim()).filter(Boolean);
        const out = {};
        for (const p of parts) {
            const m = p.match(/^\s*(idnumber|student\s*number|studentno|student\s*no|id|email|name|fullname|full\s*name)\s*[:=]\s*(.+)\s*$/i);
            if (!m) continue;
            const key = m[1].toLowerCase().replace(/\s+/g, '');
            const val = (m[2] || '').trim().replace(/^['"]|['"]$/g, '');

            if (key === 'idnumber' || key === 'studentnumber' || key === 'studentno' || key === 'studentno' || key === 'id') {
                out.idnumber = val;
            } else if (key === 'fullname' || key === 'fullname' || key === 'name') {
                out.name = val;
            } else if (key === 'email') {
                out.email = val;
            }
        }

        if (Object.keys(out).length > 0) {
            return out;
        }

        return null;
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
                const raw = (qrCodeMessage || '').trim();
                try {
                    await stopScanner();
                    const result = await loginViaQr(raw);
                    if (result && result.action === 'redirect') {
                        return;
                    }
                } catch (e) {
                    console.error(e);
                }

                const parsed = parseQrPayload(raw);

                const idValue = parsed && parsed.idnumber ? String(parsed.idnumber) : raw;
                const nameValue = parsed && parsed.name ? String(parsed.name) : '';
                const emailValue = parsed && parsed.email ? String(parsed.email) : '';

                document.getElementById('idnumber').value = (idValue || '').replace(/[^0-9]/g, '');
                if (nameValue) {
                    document.getElementById('fullname').value = nameValue;
                }
                if (emailValue) {
                    document.getElementById('email').value = emailValue;
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

</html>
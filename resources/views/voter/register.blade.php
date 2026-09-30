<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Voter Registration - Philippine Christian University Online Election System</title>
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

        @keyframes verification-success-enter {
            from { opacity: 0; transform: translateY(0.75rem) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @media (prefers-reduced-motion: no-preference) {
            #verification-success-modal:not(.hidden) .verification-success-card {
                animation: verification-success-enter 0.24s ease-out both;
            }
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
<body class="relative min-h-screen bg-gray-100">
    <div class="pointer-events-none fixed inset-0 flex items-center justify-center opacity-30 z-0" aria-hidden="true">
        <img src="{{ asset('images/pculogo.png') }}" alt="" class="w-[62vw] max-w-[820px] h-auto" />
    </div>
    <header class="relative z-50 bg-blue-600 text-white p-4">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
            <a href="{{ url('/') }}" class="flex items-center gap-3 mb-4 md:mb-0">
                <img src="{{ asset('images/pculogo.png') }}" alt="School Logo" class="w-16 h-16 md:w-24 md:h-24 -my-4 object-contain flex-shrink-0" onerror="this.onerror=null;this.style.display='none';">
                <h1 class="text-3xl font-bold">Philippine Christian University Online Election System</h1>
            </a>
            <nav class="hidden md:block" id="menu">
                <ul class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    <li><a class="hover:underline" href="{{ url('/') }}">Home</a></li>
                    <li><a class="hover:underline" href="{{ url('/election') }}">Election</a></li>
                    <li><a class="hover:underline" href="{{ route('election.details') }}">Vote</a></li>
                    <li><a class="hover:underline" href="{{ url('/result') }}">Results</a></li>
                    <li><a class="hover:underline" href="{{ url('/contact') }}">Contact</a></li>
                    @guest
                        <li><a class="hover:underline" href="{{ route('login') }}">Log In</a></li>
                    @endguest
                    @auth
                        <li class="border-t md:border-t-0 md:pt-0 pt-2">
                            <span class="text-sm font-semibold">{{ auth()->user()->name }}</span>
                        </li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>

    <main class="relative z-10 container mx-auto p-4">
        <a href="{{ url('/') }}"
            class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-bold text-blue-700 shadow-md transition hover:bg-blue-50 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            aria-label="Back to home">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
            <i class="fas fa-home" aria-hidden="true"></i>
            <span>Home</span>
        </a>
        <section class="text-center my-8">
            <h2 class="text-4xl font-bold text-gray-800 mb-2">Voter Registration</h2>
            <p class="text-gray-600 text-lg">Submit your existing account for administrator verification so you can vote.</p>
        </section>

        <section class="my-8">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl mx-auto">
                @if (!$alreadyVerified && !$verificationPending)
                    <div class="mb-4 rounded-lg bg-blue-50 px-4 py-3 text-blue-900 border border-blue-200">
                        Your account details below come from your signed-in account. An administrator will review them; this will not create a new account.
                    </div>
                @endif
                @if ($alreadyVerified)
                    <div class="rounded-lg bg-green-50 px-4 py-3 text-green-800" role="status">
                        Your account is already verified to vote.
                    </div>
                @elseif ($verificationPending)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-4 text-amber-900" role="status">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-clock mt-1 text-amber-600" aria-hidden="true"></i>
                            <div>
                                <h3 class="font-bold">Pending Voter Application</h3>
                                <p class="mt-1 text-sm">Your verification request has been submitted and is awaiting administrator review. You can vote after your account is verified.</p>
                            </div>
                        </div>
                    </div>
                @else
                <form action="{{ route('register.verify.store') }}" method="POST">
            @csrf
            @if ($errors->any())
                <div id="register-error-notification" class="fixed top-4 right-4 z-50 w-[calc(100%-2rem)] max-w-md rounded-lg border border-red-200 bg-white p-4 text-left text-red-700 shadow-xl" role="alert" aria-live="assertive">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle mt-0.5 text-red-500" aria-hidden="true"></i>
                        <ul class="flex-1 space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="text-red-400 hover:text-red-700" aria-label="Dismiss notification" onclick="document.getElementById('register-error-notification').remove()">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <script>
                    setTimeout(function () {
                        const notification = document.getElementById('register-error-notification');
                        if (notification) {
                            notification.remove();
                        }
                    }, 6000);
                </script>
            @endif
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2" for="idnumber">Student Number</label>
                <div class="relative">
                    <input class="w-full p-3 border border-gray-300 rounded-lg pr-12 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" id="idnumber" name="idnumber" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="Student Number" value="{{ $prefillIdNumber }}" readonly required>
                    <button class="absolute inset-y-0 right-0 z-10 hidden items-center bg-green-600 text-white px-3 rounded-r-lg hover:bg-green-700" type="button" id="scan-qr-button" aria-label="Scan student QR code">
                        <i class="fas fa-qrcode"></i>
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="first_name">First Name</label>
                    <input class="w-full p-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200" id="first_name" name="first_name" type="text" placeholder="First Name" value="{{ $prefillFirstName }}" readonly required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="middle_initial">Middle Initial</label>
                    <input class="w-full p-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200" id="middle_initial" name="middle_initial" type="text" maxlength="2" pattern="[A-Za-z]\.?" placeholder="M.I. (optional)" value="{{ $prefillMiddleInitial }}" readonly>
                    <p class="mt-1 text-xs text-gray-500">Enter one letter, with or without a period.</p>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="last_name">Last Name</label>
                    <input class="w-full p-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200" id="last_name" name="last_name" type="text" placeholder="Last Name" value="{{ $prefillLastName }}" readonly required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2" for="email">Email</label>
                    <input class="w-full p-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200" id="email" name="email" type="email" placeholder="Email" value="{{ $prefillEmail }}" readonly required>
                </div>
            </div>

            <div class="mb-4">
                @auth
                    <label class="block text-gray-700 font-bold mb-2" for="current_password">Re-enter Your Account Password</label>
                    <div class="relative">
                        <input class="w-full p-3 pr-20 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200" id="current_password" name="current_password" type="password" autocomplete="current-password" placeholder="Enter your current password" required>
                        <button class="absolute inset-y-0 right-2 my-auto h-9 rounded px-2 text-sm font-semibold text-blue-700 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500" type="button" id="toggle-current-password" aria-label="Show password" aria-controls="current_password" aria-pressed="false">
                            Show
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Confirm your identity so your existing account can be reviewed for voter eligibility.</p>
                @else
                    <label class="block text-gray-700 font-bold mb-2" for="password">Password</label>
                    <input class="w-full p-3 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200" id="password" name="password" type="password" autocomplete="new-password" placeholder="Password" required>
                @endauth
            </div>

            <div class="mt-6 text-center">
                <button class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700" type="submit">Submit for Verification</button>
            </div>
        </form>
                @endif
            </div>
        </section>
    </main>

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
    <div id="verification-success-modal" class="fixed inset-0 z-[9999] hidden" role="dialog" aria-modal="true" aria-labelledby="verification-success-title" aria-describedby="verification-success-description">
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" id="verification-success-backdrop"></div>
        <div class="relative flex min-h-full items-center justify-center p-4 sm:p-6">
            <section class="verification-success-card relative w-full max-w-md overflow-hidden rounded-3xl border border-white/70 bg-white shadow-2xl shadow-slate-950/25">
                <div class="absolute inset-x-0 top-0 h-2 bg-gradient-to-r from-emerald-400 via-teal-500 to-blue-600"></div>
                <button type="button" id="close-verification-success" class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" aria-label="Close confirmation">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>

                <div class="px-7 pb-7 pt-10 text-center sm:px-9 sm:pb-9">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-8 ring-emerald-50/60">
                        <i class="fas fa-check text-2xl" aria-hidden="true"></i>
                    </div>
                    <p class="mt-6 text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">Submission received</p>
                    <h2 id="verification-success-title" class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Verification submitted</h2>
                    <p id="verification-success-description" class="mx-auto mt-3 max-w-sm text-sm leading-6 text-slate-600 sm:text-base">
                        {{ session('status') }}
                    </p>
                    <div class="mt-6 flex items-start gap-3 rounded-2xl border border-blue-100 bg-blue-50/80 p-4 text-left">
                        <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white text-blue-700 shadow-sm">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                        </span>
                        <p class="text-sm leading-5 text-blue-950">
                            <span class="font-semibold">What happens next?</span><br>
                            An administrator will review your account. You can vote once your student details are verified.
                        </p>
                    </div>
                </div>

                <div class="border-t border-slate-100 bg-slate-50/80 px-7 py-5 sm:px-9">
                    <button type="button" id="ok-verification-success" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-900/15 transition hover:-translate-y-0.5 hover:bg-blue-800 hover:shadow-blue-900/25 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <span>Got it</span>
                        <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i>
                    </button>
                </div>
            </section>
        </div>
    </div>
@endif
<script>
    (function () {
        const modal = document.getElementById('verification-success-modal');
        if (!modal) return;

        function closeModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            document.removeEventListener('keydown', handleEscape);
        }

        function handleEscape(event) {
            if (event.key === 'Escape') closeModal();
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('ok-verification-success')?.focus();
        document.getElementById('close-verification-success')?.addEventListener('click', closeModal);
        document.getElementById('ok-verification-success')?.addEventListener('click', closeModal);
        document.getElementById('verification-success-backdrop')?.addEventListener('click', closeModal);
        document.addEventListener('keydown', handleEscape);
    })();

    const currentPasswordInput = document.getElementById('current_password');
    const toggleCurrentPasswordButton = document.getElementById('toggle-current-password');

    if (currentPasswordInput && toggleCurrentPasswordButton) {
        toggleCurrentPasswordButton.addEventListener('click', function () {
            const shouldShowPassword = currentPasswordInput.type === 'password';
            currentPasswordInput.type = shouldShowPassword ? 'text' : 'password';
            toggleCurrentPasswordButton.textContent = shouldShowPassword ? 'Hide' : 'Show';
            toggleCurrentPasswordButton.setAttribute('aria-pressed', String(shouldShowPassword));
            toggleCurrentPasswordButton.setAttribute(
                'aria-label',
                shouldShowPassword ? 'Hide password' : 'Show password'
            );
        });
    }

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
            } else if (key === 'fullname' || key === 'name') {
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
                const nameValue = parsed && (parsed.name || parsed.full_name) ? String(parsed.name || parsed.full_name) : '';
                const emailValue = parsed && parsed.email ? String(parsed.email) : '';

                document.getElementById('idnumber').value = (idValue || '').replace(/[^0-9]/g, '');
                if (nameValue) {
                    const nameParts = nameValue.trim().split(/\s+/);
                    document.getElementById('first_name').value = nameParts[0] || '';
                    document.getElementById('middle_initial').value = nameParts.length > 2
                        ? (nameParts.slice(1, -1).join(' ').replace(/\.$/, '').charAt(0) + '.')
                        : '';
                    document.getElementById('last_name').value = nameParts.length > 1
                        ? nameParts[nameParts.length - 1]
                        : '';
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

</body>
</html>
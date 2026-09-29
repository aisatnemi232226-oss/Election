<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PCU Online Election System - Candidate Registration</title>
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
        body { font-family: 'Roboto', sans-serif; }
    </style>
</head>
<body class="relative bg-gray-100">
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
                        <li><a class="hover:underline" href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}">Log In</a></li>
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
        <section class="text-center my-8">
            <h2 class="text-4xl font-bold text-gray-800 mb-2">Candidate Registration</h2>
            <p class="text-gray-600 text-lg">Apply as a candidate by selecting your position and submitting your details.</p>
        </section>

        <section class="my-8">
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl mx-auto">
                <div class="mb-4 rounded-lg bg-blue-50 px-4 py-3 text-blue-900 border border-blue-200">
                    After submitting your application, it will be reviewed by the admin within 1-3 working days. Please wait for approval before appearing as an official candidate.
                </div>

                @if (session('success'))
                    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-800">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!session('success'))
                    @if (($existingApplicationStatus ?? null) === 'Active')
                        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-800">
                            Your candidate application has been approved. You can now visit Candidates to see your name.
                        </div>
                    @elseif (($existingApplicationStatus ?? null) === 'Rejected')
                        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-red-800">
                            Your candidate application has been rejected by the admin.
                        </div>
                    @elseif (($hasExistingApplication ?? false) && ($existingApplicationStatus ?? null) === 'Pending')
                        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-green-800">
                            You have already submitted your application. Please wait for admin approval (1-3 working days).
                        </div>
                    @endif
                @endif

                @if (!session('success') && !($hasExistingApplication ?? false))
                    <form method="POST" action="{{ route('candidate.register.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="position_id">Position</label>
                                <select class="w-full p-3 border border-gray-300 rounded-lg" id="position_id" name="position_id" required>
                                    <option value="">Select Position</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}" {{ (string) old('position_id', $prefillPositionId ?? '') === (string) $position->id ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="course_id">Course</label>
                                <select class="w-full p-3 border border-gray-300 rounded-lg bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200" id="course_id" name="course_id" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ (string) old('course_id') === (string) $course->id ? 'selected' : '' }}>
                                            {{ $course->name }}{{ $course->coursecode ? ' (' . $course->coursecode . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Select the course shown on your official student record.</p>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="party_id">Party (Optional)</label>
                                <select class="w-full p-3 border border-gray-300 rounded-lg" id="party_id" name="party_id">
                                    <option value="">No Party</option>
                                    @foreach($parties as $party)
                                        <option value="{{ $party->id }}" {{ (string) old('party_id') === (string) $party->id ? 'selected' : '' }}>
                                            {{ $party->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block text-gray-700 font-bold mb-2" for="credential">Student QR / Credential</label>
                            <div class="relative">
                                <input class="w-full p-3 border border-gray-300 rounded-lg pr-12" id="credential" type="text" placeholder="Scan QR or type student number/email" value="{{ $credentialValue }}" {{ auth()->check() ? 'readonly' : '' }}>
                                <button class="absolute inset-y-0 right-0 z-10 flex items-center bg-green-600 text-white px-3 rounded-r-lg hover:bg-green-700 {{ auth()->check() ? 'hidden' : '' }}" type="button" id="scan-qr-button" aria-label="Scan QR">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Guest: scan QR to auto-fill. Logged in: auto-filled.</p>
                        </div>

                        <input type="hidden" id="idnumber" name="idnumber" value="{{ old('idnumber', auth()->check() ? (auth()->user()->idnumber ?? '') : ($prefillIdNumber ?? '')) }}" />
                        <input type="hidden" id="fullname" name="name" value="{{ old('name', auth()->check() ? (auth()->user()->name ?? '') : ($prefillName ?? '')) }}" />
                        <input type="hidden" id="email" name="email" value="{{ old('email', auth()->check() ? (auth()->user()->email ?? '') : ($prefillEmail ?? '')) }}" />

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="display-idnumber">Student Number</label>
                                <input class="w-full p-3 border border-gray-300 rounded-lg {{ auth()->check() ? 'bg-gray-50' : '' }}" id="display-idnumber" type="text" value="{{ old('idnumber', auth()->check() ? (auth()->user()->idnumber ?? '') : ($prefillIdNumber ?? '')) }}" {{ auth()->check() ? 'readonly' : '' }} />
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="display-fullname">Full Name</label>
                                <input class="w-full p-3 border border-gray-300 rounded-lg {{ auth()->check() ? 'bg-gray-50' : '' }}" id="display-fullname" type="text" value="{{ old('name', auth()->check() ? (auth()->user()->name ?? '') : ($prefillName ?? '')) }}" {{ auth()->check() ? 'readonly' : '' }} />
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="display-email">Email</label>
                                <input class="w-full p-3 border border-gray-300 rounded-lg {{ auth()->check() ? 'bg-gray-50' : '' }}" id="display-email" type="text" value="{{ old('email', auth()->check() ? (auth()->user()->email ?? '') : ($prefillEmail ?? '')) }}" {{ auth()->check() ? 'readonly' : '' }} />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Profile Image</label>
                                <input class="w-full p-2 border border-gray-300 rounded" type="file" name="profile_img" accept="image/*" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">Banner Image</label>
                                <input class="w-full p-2 border border-gray-300 rounded" type="file" name="banner_img" accept="image/*" required>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-gray-700 font-bold mb-2" for="description">Description</label>
                            <textarea class="w-full p-3 border border-gray-300 rounded-lg" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="mt-4">
                            <label class="block text-gray-700 font-bold mb-2" for="platforms">Platforms</label>
                            <textarea class="w-full p-3 border border-gray-300 rounded-lg" id="platforms" name="platforms" rows="4" required>{{ old('platforms') }}</textarea>
                        </div>

                        <div class="mt-6 text-center">
                            <button class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700" type="submit">Submit Application</button>
                        </div>
                    </form>

                    <div id="qr-reader-container" class="mt-4" style="display: none;">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-700 text-sm font-bold">Scan QR Code</span>
                            <button class="text-gray-600 hover:text-gray-800" type="button" id="close-qr-button" aria-label="Close scanner">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div id="qr-reader"></div>
                    </div>
                @endif
            </div>
        </section>
    </main>

    @if (session('success'))
        <div id="success-modal" class="fixed inset-0 hidden" style="z-index: 9999;">
            <div class="absolute inset-0 bg-gray-900/50" id="success-modal-backdrop"></div>
            <div class="relative h-full w-full flex items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Application Submitted</h3>
                                <p class="mt-2 text-sm text-gray-700">We will review your application. Please wait for the admin approval (1-3 working days).</p>
                            </div>
                            <button type="button" id="close-success-modal" class="text-gray-500 hover:text-gray-700" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="px-5 py-4 bg-gray-50 flex justify-end">
                        <button type="button" id="ok-success-modal" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <footer class="relative z-10 bg-blue-600 text-white p-4 text-center">
        <p>2026 Philippine Christian University Online Election System. All rights reserved.</p>
    </footer>

<script>
    let html5QrCode = null;
    let isScanning = false;

    function syncSubmittedFields() {
        const idInput = document.getElementById('display-idnumber');
        const nameInput = document.getElementById('display-fullname');
        const emailInput = document.getElementById('display-email');

        if (idInput) {
            const idClean = (idInput.value || '').replace(/[^0-9]/g, '');
            document.getElementById('idnumber').value = idClean;
            idInput.value = idClean;
        }

        if (nameInput) {
            document.getElementById('fullname').value = (nameInput.value || '').trim();
        }

        if (emailInput) {
            document.getElementById('email').value = (emailInput.value || '').trim();
        }
    }

    function parseQrPayload(raw) {
        const text = (raw || '').trim();
        if (!text) return null;

        if (text.includes(';')) {
            const segs = text.split(';').map(s => s.trim()).filter(Boolean);
            if (segs.length >= 2) {
                return { idnumber: segs[0] || '', name: segs[1] || '', email: segs[2] || '' };
            }
        }

        if (text[0] === '{') {
            try {
                const obj = JSON.parse(text);
                if (obj && typeof obj === 'object') return obj;
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

        return null;
    }

    async function stopScanner() {
        const container = document.getElementById('qr-reader-container');
        if (!html5QrCode || !isScanning) {
            container.style.display = 'none';
            return;
        }

        try {
            await html5QrCode.stop();
        } catch (err) {
            console.error(err);
        } finally {
            container.style.display = 'none';
            isScanning = false;
            html5QrCode = null;
            document.getElementById('qr-reader').innerHTML = '';
        }
    }

    document.getElementById('close-qr-button')?.addEventListener('click', function() {
        stopScanner();
    });

    document.getElementById('display-idnumber')?.addEventListener('input', function () {
        syncSubmittedFields();
    });
    document.getElementById('display-fullname')?.addEventListener('input', function () {
        syncSubmittedFields();
    });
    document.getElementById('display-email')?.addEventListener('input', function () {
        syncSubmittedFields();
    });

    document.getElementById('credential')?.addEventListener('input', function () {
        const raw = (this.value || '').trim();
        if (!raw) return;

        if (raw.includes('@') && !document.getElementById('display-email').value) {
            document.getElementById('display-email').value = raw;
            syncSubmittedFields();
            return;
        }

        const idClean = raw.replace(/[^0-9]/g, '');
        if (idClean && !document.getElementById('display-idnumber').value) {
            document.getElementById('display-idnumber').value = idClean;
            syncSubmittedFields();
        }
    });

    document.getElementById('scan-qr-button')?.addEventListener('click', async function() {
        const container = document.getElementById('qr-reader-container');

        if (typeof Html5Qrcode === 'undefined') {
            alert('QR scanner failed to load. Please refresh the page or check your internet connection.');
            return;
        }

        if (isScanning) {
            await stopScanner();
            return;
        }

        container.style.display = 'block';
        html5QrCode = new Html5Qrcode('qr-reader');
        isScanning = true;

        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 250 },
            async qrCodeMessage => {
                try {
                    await stopScanner();
                    const raw = (qrCodeMessage || '').trim();
                    const parsed = parseQrPayload(raw);

                    const idValue = parsed && parsed.idnumber ? String(parsed.idnumber) : '';
                    const nameValue = parsed && parsed.name ? String(parsed.name) : '';
                    const emailValue = parsed && parsed.email ? String(parsed.email) : '';

                    const idClean = (idValue || '').replace(/[^0-9]/g, '');

                    document.getElementById('credential').value = emailValue || idClean || raw;

                    if (idClean && !document.getElementById('display-idnumber').value) {
                        document.getElementById('display-idnumber').value = idClean;
                    }
                    if (nameValue && !document.getElementById('display-fullname').value) {
                        document.getElementById('display-fullname').value = nameValue;
                    }
                    if (emailValue && !document.getElementById('display-email').value) {
                        document.getElementById('display-email').value = emailValue;
                    }

                    syncSubmittedFields();
                } catch (e) {
                    console.error(e);
                    alert(e && e.message ? e.message : 'Invalid QR code');
                }
            },
            () => {}
        ).catch(async err => {
            console.error(err);
            await stopScanner();
        });
    });

    (function () {
        const modal = document.getElementById('success-modal');
        if (!modal) return;

        function close() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        document.getElementById('close-success-modal')?.addEventListener('click', close);
        document.getElementById('ok-success-modal')?.addEventListener('click', close);
        document.getElementById('success-modal-backdrop')?.addEventListener('click', close);
    })();
</script>

</body>
</html>

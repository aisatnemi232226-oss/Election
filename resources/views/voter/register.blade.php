@extends('layouts.voter')

@section('title', 'Voter Registration - Philippine Christian University Online Election System')
@section('body-class', 'auth-page auth-register-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pcu-auth.css') }}">
@endpush

@section('content')
<div class="page-content auth-wrap">
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
    <section class="auth-card wide" aria-labelledby="verification-title">
        <header class="auth-brand">
            <img src="{{ asset('images/pculogo.png') }}" alt="" width="64" height="64">
            <h1 id="verification-title">Voter Registration</h1>
            <p>Submit your existing account for administrator verification so you can vote.</p>
        </header>

        @if (!$alreadyVerified && !$verificationPending)
            <div class="auth-note">
                Your account details below come from your signed-in account. An administrator will review them; this will not create a new account.
            </div>
        @endif
        @if ($alreadyVerified)
            <div class="auth-note success" role="status">Your account is already verified to vote.</div>
        @elseif ($verificationPending)
            <div class="auth-note warning" role="status">
                <h2>Pending Voter Application</h2>
                <p>Your verification request has been submitted and is awaiting administrator review. You can vote after your account is verified.</p>
            </div>
        @else
            <form action="{{ route('register.verify.store') }}" method="POST">
            @csrf
            @if ($errors->any())
                <div id="register-error-notification" class="auth-alert" role="alert" aria-live="assertive">
                    <div>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" aria-label="Dismiss notification" onclick="document.getElementById('register-error-notification').remove()">
                            <svg viewBox="0 0 24 24" aria-hidden="true" width="20" height="20"><path d="m6 6 12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
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
            <div class="auth-field">
                <label for="idnumber">Student Number</label>
                <div class="auth-scan-control">
                    <input id="idnumber" name="idnumber" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="Student Number" value="{{ $prefillIdNumber }}" readonly required>
                    <button class="auth-scan-btn" type="button" id="scan-qr-button" aria-label="Scan student QR code">
                        Scan QR
                    </button>
                </div>
            </div>
            <div class="auth-grid two">
                <div class="auth-field">
                    <label for="first_name">First Name</label>
                    <input id="first_name" name="first_name" type="text" placeholder="First Name" value="{{ $prefillFirstName }}" readonly required>
                </div>
                <div class="auth-field">
                    <label for="middle_initial">Middle Initial</label>
                    <input id="middle_initial" name="middle_initial" type="text" maxlength="2" pattern="[A-Za-z]\.?" placeholder="M.I. (optional)" value="{{ $prefillMiddleInitial }}" readonly>
                    <p class="hint">Enter one letter, with or without a period.</p>
                </div>
                <div class="auth-field">
                    <label for="last_name">Last Name</label>
                    <input id="last_name" name="last_name" type="text" placeholder="Last Name" value="{{ $prefillLastName }}" readonly required>
                </div>
                <div class="auth-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" placeholder="Email" value="{{ $prefillEmail }}" readonly required>
                </div>
            </div>

            <div class="auth-field">
                @auth
                    <label for="current_password">Re-enter Your Account Password</label>
                    <div class="auth-input">
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password" placeholder="Enter your current password" required>
                        <button class="auth-toggle" type="button" id="toggle-current-password" aria-label="Show password" aria-controls="current_password" aria-pressed="false">
                            Show
                        </button>
                    </div>
                    <p class="hint">Confirm your identity so your existing account can be reviewed for voter eligibility.</p>
                @else
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" placeholder="Password" required>
                @endauth
            </div>

            <button class="auth-btn" type="submit">Submit for Verification</button>
        </form>
        @endif

        <p class="auth-foot"><a class="auth-link" href="{{ url('/') }}" aria-label="Back to home">Home</a></p>
    </section>

    <div id="qr-modal" class="auth-dialog hidden" aria-hidden="true">
        <div id="qr-modal-backdrop" class="auth-dialog-backdrop"></div>
        <div class="auth-dialog-card">
            <div class="auth-dialog-head">
                <h2>Scan QR Code</h2>
                <button class="auth-dialog-close" type="button" id="close-qr-button" aria-label="Close">
                        Close
                </button>
            </div>
            <div class="auth-dialog-body">
                <div style="position:relative;overflow:hidden">
                    <div id="qr-reader"></div>
                    <div class="qr-scan-overlay"><div class="qr-scan-glow"></div><div class="qr-scan-line"></div></div>
                </div>
            </div>
        </div>
    </div>
@if (session('status'))
    <div id="verification-success-modal" class="auth-dialog hidden" role="dialog" aria-modal="true" aria-labelledby="verification-success-title" aria-describedby="verification-success-description">
        <div class="auth-dialog-backdrop" id="verification-success-backdrop"></div>
        <section class="auth-dialog-card">
            <button type="button" id="close-verification-success" class="auth-dialog-close" aria-label="Close confirmation">
                    <svg viewBox="0 0 24 24" aria-hidden="true" width="20" height="20"><path d="m6 6 12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
            <div class="auth-status-content">
                <p>Submission received</p>
                <h2 id="verification-success-title" class="auth-status-heading">Verification submitted</h2>
                <p id="verification-success-description">{{ session('status') }}</p>
                <div class="auth-status-next">
                    <strong>What happens next?</strong>
                    <p>An administrator will review your account. You can vote once your student details are verified.</p>
                </div>
            </div>
            <div class="auth-status-actions">
                <button type="button" id="ok-verification-success" class="auth-btn">Got it</button>
            </div>
        </section>
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
</div>
@endsection

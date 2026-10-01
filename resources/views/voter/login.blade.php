@extends('layouts.voter')

@section('title', 'Login')
@section('body-class', 'auth-page auth-login-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pcu-auth.css') }}">
<style>
    .qr-scan-overlay { pointer-events: none; }
    .qr-scan-line { animation: qr-scan-line 1.6s linear infinite; }
    @media (prefers-reduced-motion: reduce) { .qr-scan-line { animation: none; } }
</style>
@endpush

@section('content')
<div class="page-content auth-wrap">
    <section class="auth-card" aria-labelledby="login-title">
        <header class="auth-brand">
            <img src="{{ asset('images/pculogo.png') }}" alt="" width="64" height="64">
            <h1 id="login-title">Welcome back</h1>
            <p>Sign in to vote in the PCU student elections.</p>
        </header>

        @if ($errors->any())
            <div class="auth-alert" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="auth-field">
                <label for="username">Username</label>
                <input id="username" name="email" type="text" placeholder="Enter your username" value="{{ old('email') }}" autocomplete="username" required>
            </div>
            <div class="auth-field">
                <label for="password">Password</label>
                <div class="auth-input">
                    <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
                    <button class="auth-toggle" type="button" data-password-toggle="password" aria-label="Show password" aria-pressed="false">Show</button>
                </div>
            </div>
            <div class="auth-row"><a class="auth-link" href="{{ route('password.request') }}">Forgot password?</a></div>

            <button class="auth-btn" type="submit">Log in</button>

            <div class="auth-or" aria-hidden="true">or</div>
            <button class="auth-btn secondary" type="button" id="scan-qr-button">
                <svg class="icon" aria-hidden="true"><use href="#pcu-qr"/></svg>
                Sign in with QR code
            </button>

            <p class="auth-foot">New to PCU Online Election? <a class="auth-link" href="{{ route('register') }}">Create account</a></p>
        </form>
    </section>
</div>

<div id="qr-modal" class="overlay-dialog" role="dialog" aria-modal="true" aria-labelledby="qr-modal-title" aria-hidden="true" hidden>
    <div class="dialog-panel">
        <div class="dialog-head">
            <h2 id="qr-modal-title">Scan QR code</h2>
            <button class="icon-button" type="button" id="close-qr-button" aria-label="Close QR scanner">
                <svg class="icon" aria-hidden="true"><use href="#pcu-x"/></svg>
            </button>
        </div>
        <div class="dialog-body">
            <div class="qr-reader" style="position:relative;overflow:hidden">
                <div id="qr-reader"></div>
                <div class="qr-scan-overlay"><div class="qr-scan-glow"></div><div class="qr-scan-line"></div></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (() => {
        function loadScript(src, onload, onerror) {
            const script = document.createElement('script');
            script.src = src;
            script.async = true;
            if (onload) script.onload = onload;
            if (onerror) script.onerror = onerror;
            document.head.append(script);
        }
        loadScript('https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js', () => {}, () => {
            loadScript('https://cdn.jsdelivr.net/npm/html5-qrcode/minified/html5-qrcode.min.js');
        });
    })();
</script>
@if (session('status'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.Swal) return;
            window.Swal.fire({
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
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.getElementById('close-qr-button').focus();
    }

    function closeQrModal() {
        const modal = document.getElementById('qr-modal');
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        document.getElementById('scan-qr-button').focus();
    }

    async function loginViaQr(qrValue) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const response = await fetch("{{ route('login.qr') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
            credentials: 'same-origin',
            body: JSON.stringify({ qr: (qrValue || '').trim() })
        });
        let data = null;
        try {
            data = await response.json();
        } catch (error) {
            data = null;
        }

        if (data && data.ok === false && data.action === 'register' && data.field && data.value) {
            const url = new URL("{{ route('register') }}", window.location.origin);
            if (data.idnumber) url.searchParams.set('idnumber', data.idnumber);
            if (data.email) url.searchParams.set('email', data.email);
            if (!data.idnumber && !data.email) url.searchParams.set(data.field, data.value);
            if (data.name) url.searchParams.set('name', data.name);
            window.location.href = url.toString();
            return;
        }

        if (!response.ok || !data || data.ok !== true || !data.redirect) {
            throw new Error(data?.message || 'QR login failed');
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
        } catch (error) {
            console.error(error);
        } finally {
            closeQrModal();
            isScanning = false;
            html5QrCode = null;
            document.getElementById('qr-reader').replaceChildren();
        }
    }

    document.getElementById('close-qr-button').addEventListener('click', stopScanner);
    document.getElementById('qr-modal').addEventListener('click', (event) => {
        if (event.target === event.currentTarget) stopScanner();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !document.getElementById('qr-modal').hidden) stopScanner();
    });

    document.getElementById('scan-qr-button').addEventListener('click', async () => {
        if (typeof Html5Qrcode === 'undefined') {
            alert('QR scanner failed to load. Please refresh the page or check your internet connection.');
            return;
        }
        if (isScanning) {
            await stopScanner();
            return;
        }
        openQrModal();
        html5QrCode = new Html5Qrcode('qr-reader');
        isScanning = true;
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 250 },
            async (qrCodeMessage) => {
                try {
                    await stopScanner();
                    await loginViaQr(qrCodeMessage);
                } catch (error) {
                    console.error(error);
                    alert(error?.message || 'Invalid QR code');
                }
            },
            () => {}
        ).catch(async (error) => {
            console.error(error);
            await stopScanner();
        });
    });
</script>
<script>
    document.querySelectorAll('[data-password-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.passwordToggle);
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.textContent = show ? 'Hide' : 'Show';
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            btn.setAttribute('aria-pressed', show);
        });
    });
</script>
@endpush

@extends('layouts.voter')

@section('title', 'Account QR Codes | PCU Online Election')

@section('content')
<div class="page-content auth-content account-qr-page">
    <header class="page-intro">
        <h1>Account QR codes</h1>
        <p>Keep both codes private. Anyone with your login QR can sign in; your recovery QR can reset your password once.</p>
    </header>
    <section class="surface-card qr-account-card">
        <a href="{{ $continueTo }}" class="text-link">Continue</a>
        @if (session('status'))
            <p class="notice account-qr-status" role="status">{{ session('status') }}</p>
        @endif
        <div class="surface-grid qr-account-grid">
            <section class="surface-subcard qr-account-item">
                <h2>Login QR</h2>
                @if ($loginSecret)
                    <p>Download and save this reusable sign-in code. Generating a replacement invalidates this one.</p>
                    <div id="login-qr" class="qr-display" aria-label="Your private login QR"></div>
                    <button id="download-login-qr" type="button" class="button button-secondary">Download login QR</button>
                @else
                    <p>No login QR is being displayed. Generate a new one to download it.</p>
                @endif
                <form method="POST" action="{{ route('account.login-qr.generate') }}">
                    @csrf
                    <button type="submit" class="button button-primary button-block">{{ $loginSecret ? 'Generate replacement login QR' : 'Generate login QR' }}</button>
                </form>
            </section>
            <section class="surface-subcard qr-account-item">
                <h2>Password recovery QR</h2>
                @if ($recoverySecret)
                    <p>Save this one-time recovery code. Scanning it to reset your password invalidates it.</p>
                    <div id="recovery-qr" class="qr-display" aria-label="Your private password recovery QR"></div>
                    <button id="download-recovery-qr" type="button" class="button button-secondary">Download recovery QR</button>
                @else
                    <p>No recovery QR is being displayed. Generate a new one to download it.</p>
                @endif
                <form method="POST" action="{{ route('account.recovery-qr.generate') }}">
                    @csrf
                    <button type="submit" class="button button-primary button-block">{{ $recoverySecret ? 'Generate replacement recovery QR' : 'Generate recovery QR' }}</button>
                </form>
            </section>
        </div>
    </section>
</div>
@endsection

@if ($loginSecret || $recoverySecret)
    @push('scripts')
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
                purpose: 'login', user_id: @json($userId), secret: @json($loginSecret),
            });
        @endif
        @if ($recoverySecret)
            renderDownloadableQr('recovery-qr', 'download-recovery-qr', 'password-recovery-qr.png', {
                purpose: 'password-recovery', user_id: @json($userId), secret: @json($recoverySecret),
            });
        @endif
    </script>
    @endpush
@endif

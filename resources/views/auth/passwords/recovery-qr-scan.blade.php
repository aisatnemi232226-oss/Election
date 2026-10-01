@extends('layouts.voter')

@section('title', 'Recover with QR | PCU Online Election')

@section('content')
<div class="page-content auth-content password-recovery-page">
    <section class="surface-card">
        <a href="{{ route('password.request') }}" class="text-link">← Use email recovery</a>
        <h1 class="auth-page-title">Scan recovery QR</h1>
        <p class="muted-text">Scan the private password recovery QR you saved. It can be used once and expires after a successful scan.</p>
        <div id="qr-reader" class="recovery-qr-reader" aria-label="Camera view for scanning a recovery QR"></div>
        <p id="scan-status" class="recovery-scan-status" role="status" aria-live="polite"></p>
        <button id="start-scan" type="button" class="button button-primary button-block">Start camera</button>
    </section>
</div>
@endsection

@push('scripts')
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
            if (window.Html5Qrcode) resolve();
            else loadSource(index + 1);
        };
        script.onerror = function () { loadSource(index + 1); };
        document.head.appendChild(script);
    }
    loadSource(0);
});
</script>
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
@endpush

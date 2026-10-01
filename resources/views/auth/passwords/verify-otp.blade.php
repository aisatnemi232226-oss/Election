@extends('layouts.voter')

@section('title', 'Verify Code | PCU Online Election')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('status') || session('mail_error'))
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (!window.Swal) return;

    window.Swal.fire({
        icon: @json(session('mail_error') ? 'error' : 'success'),
        title: @json(session('mail_error') ? 'Email not sent' : 'Check your email'),
        text: @json(session('mail_error') ?? session('status')),
        confirmButtonText: 'OK',
        confirmButtonColor: '#1d4ed8'
    });
});
</script>
@endif
@endpush

@section('content')
<div class="page-content auth-content password-recovery-page">
    <section class="surface-card">
        <a href="{{ route('login') }}" class="text-link">← Back to login</a>
        <h1 class="auth-page-title">Check your email</h1>
        <p class="muted-text">If an account exists for that address, a six-digit code has been sent. It expires in 10 minutes.</p>
        @if (session('status'))
            <p class="notice password-recovery-status" role="status">{{ session('status') }}</p>
        @endif
        @error('otp')
            <p class="notice notice-error password-recovery-error" role="alert">{{ $message }}</p>
        @enderror
        <form class="password-recovery-form" method="POST" action="{{ route('password.otp.verify') }}">
            @csrf
            <div class="field">
                <label for="otp">Verification code</label>
                <input id="otp" name="otp" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" maxlength="6" required autofocus class="otp-input" value="{{ old('otp') }}">
            </div>
            <button type="submit" class="button button-primary button-block">Verify code</button>
        </form>
        <form class="otp-resend-form" method="POST" action="{{ route('password.email') }}">
            @csrf
            <input type="hidden" name="email" value="{{ session('password_otp_email') }}">
            <button type="submit" class="text-link">Send a new code</button>
        </form>
    </section>
</div>
@endsection

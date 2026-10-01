@extends('layouts.voter')

@section('title', 'Forgot Password | PCU Online Election')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session('status') || session('mail_error'))
<script>
document.addEventListener('DOMContentLoaded', () => {
    Swal.fire({
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
        <h1 class="auth-page-title">Forgot password?</h1>
        <p class="muted-text">Enter the email address on your account and we will send a verification code.</p>
        @error('email')
            <p class="notice notice-error password-recovery-error" role="alert">{{ $message }}</p>
        @enderror
        <form class="password-recovery-form" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}">
            </div>
            <button type="submit" class="button button-primary button-block">Send verification code</button>
        </form>
        <a href="{{ route('password.recovery-qr') }}" class="text-link password-recovery-qr-link">Use my recovery QR instead</a>
    </section>
</div>
@endsection

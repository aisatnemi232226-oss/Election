@extends('layouts.voter')

@section('title', 'Create Account | PCU Online Election')
@section('body-class', 'auth-page auth-register-page')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pcu-auth.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
    toggle.addEventListener('click', () => {
        const passwordInput = document.getElementById(toggle.dataset.passwordToggle);
        const isVisible = passwordInput.type === 'text';
        passwordInput.type = isVisible ? 'password' : 'text';
        toggle.setAttribute('aria-pressed', String(!isVisible));
        toggle.setAttribute('aria-label', `${isVisible ? 'Show' : 'Hide'} ${passwordInput.id === 'password' ? 'password' : 'confirmation password'}`);
        toggle.textContent = isVisible ? 'Show' : 'Hide';
    });
});
</script>
@if (session('status'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Account Created!',
    text: @json(session('status')),
    confirmButtonText: 'Continue to Login',
    confirmButtonColor: '#2563eb',
    allowOutsideClick: false
}).then((result) => {
    if (result.isConfirmed) window.location.href = @json(route('login'));
});
</script>
@endif
@endpush

@section('content')
<div class="page-content auth-wrap">
    <section class="auth-card wide" aria-labelledby="register-title">
        <header class="auth-brand">
            <img src="{{ asset('images/pculogo.png') }}" alt="" width="64" height="64">
            <h1 id="register-title">Create Account</h1>
            <p>Create your account to access the election system.</p>
        </header>

        <div class="auth-note" role="note">Creating an account does not verify voter eligibility. You can apply for voter verification separately after signing in.</div>
        @if ($errors->any())
            <div class="auth-alert" role="alert" aria-live="assertive">
                <ul class="error-list">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <form action="{{ route('register.store') }}" method="POST" class="account-register-form">
            @csrf
            <div class="auth-field">
                <label for="idnumber">Student Number</label>
                <input id="idnumber" name="idnumber" type="text" inputmode="numeric" autocomplete="off" value="{{ old('idnumber', $prefillIdNumber) }}" required>
            </div>
            <div class="auth-grid two">
                <div class="auth-field"><label for="first_name">First Name</label><input id="first_name" name="first_name" type="text" autocomplete="given-name" value="{{ old('first_name', $prefillFirstName) }}" required></div>
                <div class="auth-field"><label for="middle_initial">Middle Initial</label><input id="middle_initial" name="middle_initial" type="text" maxlength="10" autocomplete="additional-name" value="{{ old('middle_initial', $prefillMiddleInitial) }}"></div>
            </div>
            <div class="auth-grid two">
                <div class="auth-field"><label for="last_name">Last Name</label><input id="last_name" name="last_name" type="text" autocomplete="family-name" value="{{ old('last_name', $prefillLastName) }}" required></div>
                <div class="auth-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" autocomplete="email" value="{{ old('email', $prefillEmail) }}" required>
                    <p class="hint">Use your PCU email address ending in @pcu.edu.ph.</p>
                </div>
            </div>
            <div class="auth-field">
                <label for="password">Password</label>
                <div class="auth-input">
                    <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
                    <button type="button" class="auth-toggle" data-password-toggle="password" aria-controls="password" aria-label="Show password" aria-pressed="false">Show</button>
                </div>
                <p class="hint">Use at least 8 characters.</p>
            </div>
            <div class="auth-field">
                <label for="password_confirmation">Confirm Password</label>
                <div class="auth-input">
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required>
                    <button type="button" class="auth-toggle" data-password-toggle="password_confirmation" aria-controls="password_confirmation" aria-label="Show confirmation password" aria-pressed="false">Show</button>
                </div>
            </div>
            <button class="auth-btn" type="submit">Create Account</button>
        </form>
        <p class="auth-foot">Already have an account? <a class="auth-link" href="{{ route('login') }}">Log in</a></p>
    </section>
</div>
@endsection

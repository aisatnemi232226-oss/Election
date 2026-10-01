@extends('layouts.voter')

@section('title', 'Choose a New Password | PCU Online Election')

@section('content')
<div class="page-content auth-content password-recovery-page">
    <section class="surface-card">
        <a href="{{ route('login') }}" class="text-link">← Back to login</a>
        <h1 class="auth-page-title">Choose a new password</h1>
        <p class="muted-text">Your new password must be at least 8 characters long.</p>
        <form class="password-recovery-form" method="POST" action="{{ route('password.update') }}">
            @csrf
            <div class="field">
                <label for="password">New password</label>
                <div class="password-control">
                    <input id="password" name="password" type="password" autocomplete="new-password" required>
                    <button type="button" data-password-toggle="password" aria-controls="password" aria-pressed="false" aria-label="Show new password" class="password-toggle">Show</button>
                </div>
                @error('password')<p class="field-error">{{ $message }}</p>@enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm new password</label>
                <div class="password-control">
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                    <button type="button" data-password-toggle="password_confirmation" aria-controls="password_confirmation" aria-pressed="false" aria-label="Show confirmation password" class="password-toggle">Show</button>
                </div>
            </div>
            <button type="submit" class="button button-primary button-block">Update password</button>
        </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordToggle);
        const isVisible = input.type === 'password';
        input.type = isVisible ? 'text' : 'password';
        button.textContent = isVisible ? 'Hide' : 'Show';
        button.setAttribute('aria-pressed', String(isVisible));
        button.setAttribute('aria-label', isVisible ? 'Hide password' : 'Show password');
    });
});
</script>
@endpush

@extends('layouts.voter')

@section('title', 'Registration | PCU Online Election')

@section('content')
<div class="page-content registration-options-page">
    <header class="page-intro">
        <h1>Register</h1>
        <p>Choose the type of registration you need.</p>
    </header>
    <div class="surface-grid registration-options-grid">
        <a href="{{ auth()->check() ? route('candidate.register') : route('login') }}" class="surface-card registration-option">
            <span class="registration-option-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2M9.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM17 8l2 2 4-4"/></svg>
            </span>
            <h2>Candidate Registration</h2>
            <p>Apply to run for a student representative position.</p>
            <span class="button button-primary">{{ auth()->check() ? 'Continue as Candidate' : 'Log In to Apply' }}</span>
        </a>
        <a href="{{ auth()->check() ? route('register.verify') : route('login') }}" class="surface-card registration-option">
            <span class="registration-option-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M4 4h16v12H4zM8 20h8M12 16v4m-4-8 2 2 4-5"/></svg>
            </span>
            <h2>Voter Registration</h2>
            <p>Submit your existing student account for administrator verification.</p>
            <span class="button button-primary">{{ auth()->check() ? 'Continue to Verification' : 'Log In to Verify' }}</span>
        </a>
    </div>
    <p class="registration-back"><a class="text-link" href="{{ url('/') }}">Back to Home</a></p>
</div>
@endsection

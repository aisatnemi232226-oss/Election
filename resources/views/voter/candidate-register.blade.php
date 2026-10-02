@extends('layouts.voter')

@section('title', 'PCU Online Election System - Candidate Registration')

@php
    $candidateApplicationPending = !session('success')
        && ($existingApplicationStatus ?? null) === 'Pending'
        && !$errors->any();
@endphp

@section('body-class', $candidateApplicationPending ? 'auth-page auth-register-page' : '')

@if ($candidateApplicationPending)
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/pcu-auth.css') }}">
    @endpush
@endif

@section('content')
@if ($candidateApplicationPending)
    <div class="page-content auth-wrap candidate-pending-wrap">
        <section class="auth-card wide" aria-labelledby="candidate-pending-title">
            <header class="auth-brand">
                <img src="{{ asset('images/pculogo.png') }}" alt="" width="64" height="64">
                <h1 id="candidate-pending-title">Candidate Registration</h1>
                <p>Your candidate application is being reviewed by the administrator.</p>
            </header>

            <div class="auth-note warning" role="status">
                <h2>Pending Candidate Application</h2>
                <p>Your candidate application has been submitted and is awaiting administrator review.</p>
            </div>

            <p class="auth-foot"><a class="auth-link" href="{{ url('/') }}" aria-label="Back to home">Home</a></p>
        </section>
    </div>
@else
<div class="page-content registration-shell registration-shell-candidate-register">
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
        <a href="{{ url('/') }}"
            class="candidate-register-home-link inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-bold text-blue-700 shadow-md transition hover:bg-blue-50 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            aria-label="Back to home">

            <span>Home</span>
        </a>
        <section class="candidate-register-intro text-center my-8">
            <h2 class="candidate-register-title text-4xl font-bold text-gray-800 mb-2">Candidate Registration</h2>
            <p class="candidate-register-description text-gray-600 text-lg">Apply as a candidate by selecting your position and submitting your details.</p>
        </section>

        <section class="my-8">
            <div class="candidate-register-card bg-white p-6 rounded-lg shadow-lg max-w-4xl mx-auto">
                @if (!session('success') && !($hasExistingApplication ?? false))
                    <div class="mb-4 rounded-lg bg-blue-50 px-4 py-3 text-blue-900 border border-blue-200">
                        After submitting your application, it will be reviewed by the admin within 1-3 working days. Please wait for approval before appearing as an official candidate.
                    </div>
                @endif

                @if (!session('success') && ($existingApplicationStatus ?? null) === 'Pending' && !$errors->any())
                    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-4 text-amber-900" role="status">
                        <div class="flex items-start gap-3">
                            
                            <div>
                                <h3 class="font-bold">Pending Candidate Application</h3>
                                <p class="mt-1 text-sm">Your candidate application has been submitted and is awaiting administrator review.</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div id="success-inline-notice" class="candidate-success-inline" role="status" tabindex="-1" hidden>
                        <span class="candidate-success-inline-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span>{{ session('success') }}</span>
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
                    @endif
                @endif

                @if (!session('success') && !($hasExistingApplication ?? false))
                    <form class="candidate-registration-form" method="POST" action="{{ route('candidate.register.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="candidate-register-grid grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="position_id">Position</label>
                                <select class="candidate-register-select w-full p-3 border border-gray-300 rounded-lg" id="position_id" name="position_id" required>
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
                                <select class="candidate-register-select w-full p-3 border border-gray-300 rounded-lg" id="course_id" name="course_id" required>
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
                                <select class="candidate-register-select w-full p-3 border border-gray-300 rounded-lg" id="party_id" name="party_id">
                                    <option value="">No Party</option>
                                    @foreach($parties as $party)
                                        <option value="{{ $party->id }}" {{ (string) old('party_id') === (string) $party->id ? 'selected' : '' }}>
                                            {{ $party->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="candidate-register-credential mt-5">
                            <label class="block text-gray-700 font-bold mb-2" for="credential">Student QR / Credential</label>
                            <div class="relative">
                                <input class="w-full p-3 border border-gray-300 rounded-lg pr-12" id="credential" type="text" placeholder="Scan QR or type student number/email" value="{{ $credentialValue }}" {{ auth()->check() ? 'readonly' : '' }}>
                                <button class="absolute inset-y-0 right-0 z-10 flex items-center bg-green-600 text-white px-3 rounded-r-lg hover:bg-green-700 {{ auth()->check() ? 'hidden' : '' }}" type="button" id="scan-qr-button" aria-label="Scan QR">
                                    Scan QR
                                </button>
                            </div>
                        </div>

                        <input type="hidden" id="idnumber" name="idnumber" value="{{ old('idnumber', auth()->check() ? (auth()->user()->idnumber ?? '') : ($prefillIdNumber ?? '')) }}" />
                        <input type="hidden" id="fullname" name="name" value="{{ old('name', auth()->check() ? (auth()->user()->name ?? '') : ($prefillName ?? '')) }}" />
                        <input type="hidden" id="email" name="email" value="{{ old('email', auth()->check() ? (auth()->user()->email ?? '') : ($prefillEmail ?? '')) }}" />

                        <div class="candidate-register-grid candidate-register-identity-grid grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
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

                        <div class="candidate-register-grid candidate-register-upload-grid grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="profile_img">Profile Image</label>
                                <input class="w-full p-2 border border-gray-300 rounded" id="profile_img" type="file" name="profile_img" accept="image/*" required>
                                <p class="mt-1 text-xs text-gray-500">Maximum file size: 10 MB.</p>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-bold mb-2" for="banner_img">Banner Image</label>
                                <input class="w-full p-2 border border-gray-300 rounded" id="banner_img" type="file" name="banner_img" accept="image/*" required>
                                <p class="mt-1 text-xs text-gray-500">Maximum file size: 10 MB.</p>
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

                        <div class="candidate-register-actions mt-6 text-center">
                            <button class="candidate-register-submit bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700" type="submit">Submit Application</button>
                        </div>
                    </form>

                    <div id="qr-reader-container" class="mt-4" style="display: none;">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-700 text-sm font-bold">Scan QR Code</span>
                            <button class="text-gray-600 hover:text-gray-800" type="button" id="close-qr-button" aria-label="Close scanner">
                                Close
                            </button>
                        </div>
                        <div id="qr-reader"></div>
                    </div>
                @endif
            </div>
        </section>

    @if ($errors->any())
        <div id="candidate-error-toast" popover="manual" class="w-[calc(100%-2rem)] max-w-md rounded-lg border border-red-200 bg-white p-4 text-left text-red-800 shadow-xl" role="alert" aria-live="assertive">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-bold">Please fix the following errors</h2>
                    <ul class="mt-2 list-disc pl-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" id="close-candidate-error-toast" class="text-red-700 hover:text-red-900" aria-label="Dismiss errors">
                    Dismiss
                </button>
            </div>
        </div>
    @endif

    @if (session('success'))
        <div id="success-modal" class="success-modal-overlay hidden" role="dialog" aria-modal="true" aria-labelledby="success-modal-title" aria-describedby="success-modal-description">
            <div class="success-modal-backdrop" id="success-modal-backdrop"></div>
            <div class="success-modal-stage">
                <div class="success-modal-panel">
                    <div class="success-modal-accent" aria-hidden="true"></div>
                    <button type="button" id="close-success-modal" class="success-modal-close" aria-label="Close confirmation">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 6 12 12M18 6 6 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>

                    <div class="success-modal-content">
                        <div class="success-modal-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <p class="success-modal-eyebrow">Submission received</p>
                        <h2 id="success-modal-title">Application submitted</h2>
                        <p id="success-modal-description" class="success-modal-description">
                            Your candidate application has been sent successfully. The admin team will review it within 1–3 working days.
                        </p>
                        <div class="success-modal-next">
                            <span class="success-modal-next-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M12 11v5m0-8h.01" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                            <p>
                                <strong>What happens next?</strong>
                                <span>Please wait for the review before appearing as an official candidate.</span>
                            </p>
                        </div>
                    </div>

                    <div class="success-modal-footer">
                        <button type="button" id="ok-success-modal" class="success-modal-confirm">
                            Got it
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

<script>
    let html5QrCode = null;
    let isScanning = false;

    const candidateErrorToast = document.getElementById('candidate-error-toast');
    if (candidateErrorToast) {
        const dismissCandidateErrorToast = () => {
            if (typeof candidateErrorToast.hidePopover === 'function') {
                candidateErrorToast.hidePopover();
            } else {
                candidateErrorToast.remove();
            }
        };

        document.getElementById('close-candidate-error-toast')?.addEventListener('click', dismissCandidateErrorToast);

        if (typeof candidateErrorToast.showPopover === 'function') {
            candidateErrorToast.showPopover();
        }
        window.setTimeout(dismissCandidateErrorToast, 5000);
    }

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
        const closeButton = document.getElementById('close-success-modal');
        const confirmButton = document.getElementById('ok-success-modal');
        const inlineNotice = document.getElementById('success-inline-notice');
        const focusableElements = [closeButton, confirmButton].filter(Boolean);

        function close() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
            document.removeEventListener('keydown', handleEscape);
            if (inlineNotice) {
                inlineNotice.hidden = false;
                inlineNotice.focus();
            }
        }

        function handleEscape(event) {
            if (event.key === 'Escape') {
                close();
                return;
            }

            if (event.key !== 'Tab' || focusableElements.length < 2) return;

            const first = focusableElements[0];
            const last = focusableElements[focusableElements.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        closeButton?.focus();

        document.getElementById('close-success-modal')?.addEventListener('click', close);
        document.getElementById('ok-success-modal')?.addEventListener('click', close);
        document.getElementById('success-modal-backdrop')?.addEventListener('click', close);
        document.addEventListener('keydown', handleEscape);
    })();
</script>
</div>
@endif
@endsection

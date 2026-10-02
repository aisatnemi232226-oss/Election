@php
    $headerUser = Auth::user();
    $isVerifiedStudent = $headerUser && !$headerUser->is_admin && $headerUser->isVerifiedVoter();
    $verificationPending = $headerUser
        && !$headerUser->is_admin
        && !$isVerifiedStudent
        && $headerUser->voter_verification_submitted_at !== null;
    $verificationBadgeClass = $isVerifiedStudent ? 'badge-ok' : ($verificationPending ? 'badge-pending' : 'badge-bad');
    $verificationBadgeLabel = $isVerifiedStudent ? 'Verified' : ($verificationPending ? 'Pending' : 'Not verified');
    $verificationBadgeIcon = $isVerifiedStudent ? '#pcu-ok' : ($verificationPending ? '#pcu-clock' : '#pcu-x');
    $headerName = $headerUser
        ? ($headerUser->first_name ?: \Illuminate\Support\Str::before((string) $headerUser->name, ' ') ?: 'Profile')
        : null;
@endphp

<svg class="icon-sprite" aria-hidden="true" focusable="false">
    <symbol id="pcu-user" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></symbol>
    <symbol id="pcu-grad" viewBox="0 0 24 24"><path d="m2 9 10-5 10 5-10 5z"/><path d="M6 11.5V16c0 1.6 2.7 3 6 3s6-1.4 6-3v-4.5"/></symbol>
    <symbol id="pcu-vote" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="12" rx="2"/><path d="m8.5 9 2.5 2.5L15.5 7M5 21h14M12 15v6"/></symbol>
    <symbol id="pcu-chart" viewBox="0 0 24 24"><path d="M4 4v16h16M9 16v-4M13 16V8M17 16v-6"/></symbol>
    <symbol id="pcu-bell" viewBox="0 0 24 24"><path d="M18 16v-5a6 6 0 1 0-12 0v5l-2 2h16ZM10 21h4"/></symbol>
    <symbol id="pcu-chev" viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></symbol>
    <symbol id="pcu-ticket" viewBox="0 0 24 24"><path d="M3 9V6a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v3a3 3 0 0 0 0 6v3a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-3a3 3 0 0 0 0-6Z"/><path d="M14 5v14" stroke-dasharray="2 3"/></symbol>
    <symbol id="pcu-qr" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM20 14v.01M14 20h3M20 17v4"/></symbol>
    <symbol id="pcu-out" viewBox="0 0 24 24"><path d="M9 21H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h4M16 17l5-5-5-5M21 12H9"/></symbol>
    <symbol id="pcu-cal" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></symbol>
    <symbol id="pcu-arrow" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
    <symbol id="pcu-chat" viewBox="0 0 24 24"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"/></symbol>
    <symbol id="pcu-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></symbol>
    <symbol id="pcu-x" viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></symbol>
    <symbol id="pcu-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></symbol>
    <symbol id="pcu-moon" viewBox="0 0 24 24"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/></symbol>
    <symbol id="pcu-ok" viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></symbol>
    <symbol id="pcu-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></symbol>
</svg>

<header class="site-header">
    <div class="page-wrap header-inner">
        <a class="site-brand" href="{{ url('/') }}" aria-label="PCU Online Election System home">
            <img src="{{ asset('images/pculogo.png') }}" alt="" class="brand-seal">
            <span>PCU Online Election<small>Philippine Christian University</small></span>
        </a>

        <nav class="desktop-nav" aria-label="Main navigation">
            <a href="{{ url('/') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
            <a href="{{ url('/election') }}">Election</a>
            <a href="{{ route('election.details') }}">Vote</a>
            <a href="{{ url('/result') }}">Results</a>
            <a href="{{ url('/contact') }}">Contact</a>
        </nav>

        <div class="header-actions">
            <button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark theme">
                <svg class="icon theme-moon" aria-hidden="true"><use href="#pcu-moon"/></svg>
                <svg class="icon theme-sun" aria-hidden="true"><use href="#pcu-sun"/></svg>
            </button>
            <ul class="header-account">
                @auth
                    @include('voter.partials.verification-notifications')
                    <li class="account-wrap">
                        <button class="account-trigger" id="profile-toggle" type="button" aria-controls="profile-menu" aria-expanded="false" aria-label="Open profile menu">
                            <span class="avatar">
                                <svg class="icon" aria-hidden="true"><use href="#pcu-user"/></svg>
                                @unless($headerUser->is_admin)
                                    <span class="avatar-dot {{ $isVerifiedStudent ? 'is-verified' : ($verificationPending ? 'is-pending' : 'is-unverified') }}" aria-hidden="true"></span>
                                @endunless
                            </span>
                            <span class="account-trigger-copy">
                                <span class="account-short-name">{{ $headerName }}</span>
                                @unless($headerUser->is_admin)
                                    <span role="status" class="status-badge {{ $verificationBadgeClass }}">
                                        <svg class="icon" aria-hidden="true"><use href="{{ $verificationBadgeIcon }}"/></svg>
                                        {{ $verificationBadgeLabel }}
                                    </span>
                                @endunless
                            </span>
                            <svg class="icon account-chevron" aria-hidden="true"><use href="#pcu-chev"/></svg>
                        </button>
                        <div class="account-menu" id="profile-menu">
                            <div class="account-menu-head">
                                <div class="account-identity">
                                    <span class="avatar avatar-large"><svg class="icon" aria-hidden="true"><use href="#pcu-user"/></svg></span>
                                    <div class="account-user-text">
                                        <strong>{{ $headerUser->name }}</strong>
                                        <small>{{ $headerUser->email }}</small>
                                    </div>
                                </div>
                                @unless($headerUser->is_admin)
                                    <div class="account-meta">
                                        <span>Student No: <b>{{ $headerUser->idnumber }}</b></span>
                                        <span role="status" class="status-badge {{ $verificationBadgeClass }}">
                                            <svg class="icon" aria-hidden="true"><use href="{{ $verificationBadgeIcon }}"/></svg>
                                            {{ $verificationBadgeLabel }}
                                        </span>
                                    </div>
                                @endunless
                            </div>
                            @if(!$headerUser->is_admin && !$isVerifiedStudent)
                                <div class="verification-note {{ $verificationPending ? 'note-pending' : 'note-unverified' }}">
                                    <p>{{ $verificationPending ? 'Your voter verification application is awaiting review.' : 'Verify your account to vote in elections.' }}</p>
                                    <a href="{{ route('register.verify') }}">
                                        {{ $verificationPending ? 'View application' : 'Verify now' }}
                                        <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
                                    </a>
                                </div>
                            @endif
                            <div class="menu-links">
                                <a href="{{ url('/tickets') }}"><svg class="icon" aria-hidden="true"><use href="#pcu-ticket"/></svg>Tickets</a>
                                <a href="{{ route('account.qr-codes') }}"><svg class="icon" aria-hidden="true"><use href="#pcu-qr"/></svg>Account QR codes</a>
                            </div>
                            <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout') }}" class="logout-form">
                                @csrf
                                <button type="submit"><svg class="icon" aria-hidden="true"><use href="#pcu-out"/></svg>Log out</button>
                            </form>
                        </div>
                    </li>
                @else
                    @guest
                        <li><a class="login-button" href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}">Log in</a></li>
                    @endguest
                @endauth
            </ul>
            <button class="icon-button mobile-nav-toggle" id="menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="mobile-menu">
                <svg class="icon" aria-hidden="true"><use href="#pcu-menu"/></svg>
            </button>
        </div>
    </div>

    <nav class="mobile-nav" id="mobile-menu" aria-label="Mobile navigation">
        <div class="page-wrap mobile-nav-inner">
            <a href="{{ url('/') }}">Home</a>
            <a href="{{ url('/election') }}">Election</a>
            <a href="{{ route('election.details') }}">Vote</a>
            <a href="{{ url('/result') }}">Results</a>
            <a href="{{ url('/contact') }}">Contact</a>
            @auth
                <div class="mobile-account">
                    <span class="avatar"><svg class="icon" aria-hidden="true"><use href="#pcu-user"/></svg></span>
                    <span class="mobile-account-copy">
                        <strong>{{ $headerUser->name }}</strong>
                        <small>{{ $headerUser->email }}</small>
                        @unless($headerUser->is_admin)
                            <small>Student No: {{ $headerUser->idnumber }}</small>
                            <span role="status" class="status-badge {{ $verificationBadgeClass }}">
                                <svg class="icon" aria-hidden="true"><use href="{{ $verificationBadgeIcon }}"/></svg>
                                {{ $verificationBadgeLabel }}
                            </span>
                            @if(!$isVerifiedStudent)
                                <span class="mobile-verification-note {{ $verificationPending ? 'note-pending' : 'note-unverified' }}">
                                    {{ $verificationPending ? 'Your voter verification application is awaiting review.' : 'Verify your account to vote in elections.' }}
                                </span>
                                <a class="mobile-verify-link" href="{{ route('register.verify') }}">{{ $verificationPending ? 'View application' : 'Verify now' }}</a>
                            @endif
                        @endunless
                    </span>
                </div>
                <a href="{{ url('/tickets') }}">Tickets</a>
                <a href="{{ route('account.qr-codes') }}">Account QR codes</a>
                <form method="POST" action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : url('/logout') }}">
                    @csrf
                    <button class="mobile-logout" type="submit">Log out</button>
                </form>
            @else
                @guest
                    <a href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}">Log in</a>
                @endguest
            @endauth
        </div>
    </nav>
</header>

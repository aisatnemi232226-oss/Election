@php
    $profileUser = auth()->user();
    $profileIsVerified = !$profileUser->is_admin && $profileUser->isVerifiedVoter();
    $profileVerificationPending = $profileUser->voter_verification_submitted_at !== null;
@endphp
<li class="relative flex items-center">
    <button
        id="profile-toggle"
        type="button"
        class="group inline-flex h-12 max-w-full items-center gap-2.5 rounded-full border border-white/30 bg-white/15 py-1 pl-1 pr-3 text-left text-white transition-colors hover:bg-white/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-blue-600"
        aria-controls="profile-menu"
        aria-expanded="false"
        aria-label="Open profile menu"
    >
        <span class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-blue-700 shadow-sm">
            <i class="fas fa-user" aria-hidden="true"></i>
            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-blue-600 {{ $profileUser->is_admin ? 'bg-blue-300' : ($profileIsVerified ? 'bg-green-400' : 'bg-red-400') }}" aria-hidden="true"></span>
        </span>
        <span class="hidden min-w-0 items-center gap-2 md:flex">
            <span class="max-w-28 truncate text-sm font-semibold">{{ $profileUser->first_name ?: \Illuminate\Support\Str::before((string) $profileUser->name, ' ') ?: 'Profile' }}</span>
            @unless($profileUser->is_admin)
                <span role="status" aria-label="{{ $profileIsVerified ? 'Verified' : 'Not verified' }}" class="inline-flex shrink-0 items-center gap-1 rounded-full px-2 py-1 text-[10px] font-semibold leading-none {{ $profileIsVerified ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    <i class="fas {{ $profileIsVerified ? 'fa-check' : 'fa-times' }}" aria-hidden="true"></i>
                    Verified
                </span>
            @endunless
        </span>
        <i class="fas fa-chevron-down hidden shrink-0 text-[10px] text-blue-100 transition-transform md:block" aria-hidden="true" data-profile-chevron></i>
    </button>
    <div id="profile-menu" class="pointer-events-auto absolute right-0 top-full z-50 mt-2 hidden w-[300px] max-w-[calc(100vw-24px)] overflow-hidden rounded-2xl bg-white text-gray-800 shadow-[0_12px_32px_rgba(15,23,42,0.18),0_2px_6px_rgba(15,23,42,0.08)]">
        <div class="border-b border-gray-100 px-5 pb-4 pt-5">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                    <i class="fas fa-user" aria-hidden="true"></i>
                </span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-gray-900">{{ $profileUser->name }}</p>
                    <p class="mt-0.5 truncate text-xs text-gray-500">{{ $profileUser->email }}</p>
                </div>
            </div>
            @unless($profileUser->is_admin)
                <div class="mt-4 flex items-center justify-between gap-3 text-xs">
                    <span class="text-gray-500">Student No: <strong class="font-semibold text-gray-700">{{ $profileUser->idnumber }}</strong></span>
                    <span role="status" class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 font-semibold {{ $profileIsVerified ? 'bg-green-50 text-green-700 ring-1 ring-green-200' : 'bg-red-50 text-red-700 ring-1 ring-red-200' }}">
                        <i class="fas {{ $profileIsVerified ? 'fa-check' : 'fa-times' }}" aria-hidden="true"></i>
                        {{ $profileIsVerified ? 'Verified' : 'Not verified' }}
                    </span>
                </div>
            @endunless
        </div>
        @if(!$profileUser->is_admin && !$profileIsVerified)
            <div class="mx-3 my-3 rounded-xl border px-3.5 py-3 {{ $profileVerificationPending ? 'border-amber-200 bg-amber-50' : 'border-red-200 bg-red-50' }}">
                <p class="text-xs leading-relaxed {{ $profileVerificationPending ? 'text-amber-900' : 'text-red-900' }}">
                    {{ $profileVerificationPending ? 'Your voter verification application is awaiting review.' : 'Verify your account to vote in elections.' }}
                </p>
                <a href="{{ route('register.verify') }}" class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold {{ $profileVerificationPending ? 'text-amber-800 hover:text-amber-950' : 'text-red-700 hover:text-red-900' }} focus:outline-none focus-visible:underline">
                    {{ $profileVerificationPending ? 'View application' : 'Verify now' }}
                    <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
                </a>
            </div>
        @endif
        <div class="px-2 pb-2">
            <a href="{{ url('/tickets') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
                <i class="fas fa-ticket-alt w-4 text-center text-gray-400" aria-hidden="true"></i>
                Tickets
            </a>
            <a href="{{ route('account.qr-codes') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
                <i class="fas fa-qrcode w-4 text-center text-gray-400" aria-hidden="true"></i>
                Account QR codes
            </a>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 p-2">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-red-600 transition-colors hover:bg-red-50 focus:bg-red-50 focus:outline-none">
                <i class="fas fa-sign-out-alt w-4 text-center" aria-hidden="true"></i>
                Log out
            </button>
        </form>
    </div>
</li>
<script>
    (() => {
        const profileToggle = document.getElementById('profile-toggle');
        const profileMenu = document.getElementById('profile-menu');

        if (!profileToggle || !profileMenu) return;

        const setProfileMenuOpen = (isOpen) => {
            profileMenu.classList.toggle('hidden', !isOpen);
            profileToggle.setAttribute('aria-expanded', String(isOpen));
            profileToggle.setAttribute('aria-label', isOpen ? 'Close profile menu' : 'Open profile menu');
            document.querySelector('[data-profile-chevron]')?.classList.toggle('rotate-180', isOpen);
        };

        profileToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            setProfileMenuOpen(profileToggle.getAttribute('aria-expanded') !== 'true');
        });

        profileMenu.addEventListener('click', (event) => event.stopPropagation());

        document.addEventListener('click', (event) => {
            if (!profileMenu.contains(event.target) && !profileToggle.contains(event.target)) {
                setProfileMenuOpen(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && profileToggle.getAttribute('aria-expanded') === 'true') {
                setProfileMenuOpen(false);
                profileToggle.focus();
            }
        });
    })();
</script>

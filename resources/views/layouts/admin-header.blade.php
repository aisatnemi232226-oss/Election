<header class="bg-blue-600 text-white p-4 fixed top-0 left-0 w-full shadow-md transition-transform duration-300 z-50">
    <div class="container mx-auto">
        <div class="hidden md:flex items-center justify-between">
            <div class="flex w-full items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.dashboard') }}" class="text-inherit inline-flex items-center gap-3">
                    <img src="{{ asset('images/pculogo.png') }}" alt="School Logo" class="w-20 h-20 object-contain flex-shrink-0" onerror="this.onerror=null;this.style.display='none';">
                    <div class="leading-tight">
                        <div class="text-2xl font-bold">Philippine Christian University</div>
                        <div class="text-base font-semibold flex items-center gap-2">
                            <span>Online Election System</span>
                            <span class="text-xs text-blue-100" id="dashboard-section-title">@yield('dashboard-title', 'Admin Dashboard')</span>
                        </div>
                    </div>
                </a>
            </div>

            <nav class="hidden lg:block">
                <ul class="flex items-center justify-center gap-4 text-base">
                    <li><a class="hover:underline" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="relative group">
                        <a id="election-management-toggle" class="hover:underline cursor-pointer block whitespace-nowrap">Election Management</a>
                        <ul id="election-management-menu" class="absolute left-0 mt-1 hidden bg-white text-gray-800 shadow-lg rounded-lg w-56 z-50 p-2 group-hover:block">
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.elections.index') }}">Manage Elections</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.election-events-settings.edit') }}">Manage Election Events</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.candidates.index') }}">Manage Candidates</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.election-parties.index') }}">Manage Election Party</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.positions.index') }}">Manage Positions</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.voters.index') }}">Manage Voters</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.election-articles.index') }}">Manage Election Articles</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.results.index') }}">View Results</a></li>
                        </ul>
                    </li>
                    <li><a class="hover:underline" href="{{ route('admin.reports.index') }}">Reports</a></li>
                    <li class="relative group">
                        <a id="settings-toggle" class="hover:underline cursor-pointer block">Settings</a>
                        <ul id="settings-menu" class="absolute left-0 mt-1 hidden bg-white text-gray-800 shadow-lg rounded-lg w-56 z-50 p-2 group-hover:block">
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.admin-roles.index') }}">Admin Roles</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.chatbot.index') }}">Manage Chatbot</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.tickets.index') }}">Manage Tickets</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.profile-settings.index') }}">Profile Settings</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="#">User Management</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.audit-logs.index') }}">Audit Logs</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.verified-voters.index') }}">Verify Voters</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.academic-year.index') }}">Set Current Academic Year</a></li>
                            <li><a class="block px-4 py-2 hover:bg-gray-200" href="{{ route('admin.courses.index') }}">Courses</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>

            <div class="flex items-center justify-end">
                @auth
                @php
                    $pendingCandidates = (int) data_get($adminWidget ?? [], 'candidates.pending', 0);
                    $pendingVoterVerification = (int) data_get($adminWidget ?? [], 'voters.pending_verification', 0);
                    $pendingTotal = $pendingCandidates + $pendingVoterVerification;
                @endphp

                <div class="relative mr-4">
                    <button type="button" id="admin-notif-toggle" class="relative" aria-haspopup="true" aria-expanded="false" title="Notifications">
                        <span class="inline-flex items-center justify-center w-9 h-9 bg-white/15 rounded-full hover:bg-white/20">
                            <i class="fas fa-bell"></i>
                        </span>
                        <span id="admin-notif-badge" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[11px] font-bold inline-flex items-center justify-center {{ $pendingTotal > 0 ? '' : 'hidden' }}">
                            {{ $pendingTotal > 99 ? '99+' : $pendingTotal }}
                        </span>
                    </button>

                    <div id="admin-notif-panel" class="hidden absolute right-0 mt-2 bg-white text-gray-800 rounded-md shadow-lg z-50 w-96 overflow-hidden">
                        <div class="px-4 py-3 border-b flex items-center justify-between">
                            <div class="font-semibold">Notifications</div>
                            <button type="button" id="admin-notif-close" class="text-gray-500 hover:text-gray-700" aria-label="Close notifications">
                                <span class="text-xl leading-none">&times;</span>
                            </button>
                        </div>

                        <div class="px-2 py-2 border-b">
                            <div class="flex gap-2">
                                <button type="button" id="admin-notif-tab-candidates" class="flex-1 px-3 py-2 text-sm font-semibold rounded bg-blue-50 text-blue-700">Candidates</button>
                                <button type="button" id="admin-notif-tab-voters" class="flex-1 px-3 py-2 text-sm font-semibold rounded hover:bg-gray-50">Verify Voters</button>
                            </div>
                        </div>

                        <div class="max-h-96 overflow-auto">
                            <div id="admin-notif-loading" class="px-4 py-6 text-sm text-gray-500 hidden">Loading...</div>

                            <div id="admin-notif-candidates" class="divide-y">
                                <div class="px-4 py-3 text-xs text-gray-500">Pending candidate approvals</div>
                                <div id="admin-notif-candidates-list"></div>
                                <div class="px-4 py-3 border-t">
                                    <a class="text-sm text-blue-700 hover:underline" href="{{ route('admin.candidates.index', ['status' => 'Pending']) }}">View all pending candidates</a>
                                </div>
                            </div>

                            <div id="admin-notif-voters" class="hidden divide-y">
                                <div class="px-4 py-3 text-xs text-gray-500">Voters needing verification</div>
                                <div id="admin-notif-voters-list"></div>
                                <div class="px-4 py-3 border-t">
                                    <a class="text-sm text-blue-700 hover:underline" href="{{ route('admin.verified-voters.index') }}">Open Verify Voters</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <button id="admin-profile-toggle" type="button"
                        class="group inline-flex h-12 max-w-full items-center gap-2.5 rounded-full border border-white/30 bg-white/15 py-1 pl-1 pr-3 text-left text-white transition-colors hover:bg-white/25 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-blue-600"
                        aria-controls="admin-profile-menu" aria-expanded="false" aria-label="Open profile menu">
                        <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-blue-700 shadow-sm">
                            <i class="fas fa-user-shield" aria-hidden="true"></i>
                        </span>
                        <span class="hidden min-w-0 items-center gap-2 md:flex">
                            <span class="max-w-28 truncate text-sm font-semibold">{{ Auth::user()->first_name ?: \Illuminate\Support\Str::before((string) Auth::user()->name, ' ') ?: 'Admin' }}</span>
                            <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-blue-100 px-2 py-1 text-[10px] font-semibold leading-none text-blue-800">
                                <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                Admin
                            </span>
                        </span>
                        <i class="fas fa-chevron-down hidden shrink-0 text-[10px] text-blue-100 transition-transform md:block" aria-hidden="true" data-admin-profile-chevron></i>
                    </button>

                    <div id="admin-profile-menu" class="pointer-events-auto absolute right-0 top-full z-50 mt-2 hidden w-[300px] max-w-[calc(100vw-24px)] overflow-hidden rounded-2xl bg-white text-gray-800 shadow-[0_12px_32px_rgba(15,23,42,0.18),0_2px_6px_rgba(15,23,42,0.08)]">
                        <div class="border-b border-gray-100 px-5 pb-4 pt-5">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-700 ring-1 ring-blue-100">
                                    <i class="fas fa-user-shield" aria-hidden="true"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="mt-0.5 truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between gap-3 text-xs">
                                <span class="text-gray-500">Administrator account</span>
                                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 font-semibold text-blue-700 ring-1 ring-blue-200">
                                    <i class="fas fa-shield-alt" aria-hidden="true"></i>
                                    Admin
                                </span>
                            </div>
                        </div>
                        <div class="px-2 py-2">
                            <a href="{{ route('admin.profile-settings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 transition-colors hover:bg-gray-50 focus:bg-gray-50 focus:outline-none">
                                <i class="fas fa-cog w-4 text-center text-gray-400" aria-hidden="true"></i>
                                Profile settings
                            </a>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="border-t border-gray-100 p-2">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-red-600 transition-colors hover:bg-red-50 focus:bg-red-50 focus:outline-none">
                                <i class="fas fa-sign-out-alt w-4 text-center" aria-hidden="true"></i>
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
            </div>
        </div>

        <div class="lg:hidden flex flex-col items-center">
            <a href="{{ route('admin.dashboard') }}" class="text-inherit inline-flex items-center gap-3 md:hidden">
                <img src="{{ asset('images/pculogo.png') }}" alt="School Logo" class="w-16 h-16 -my-2 object-contain flex-shrink-0" onerror="this.onerror=null;this.style.display='none';">
                <div class="leading-tight text-center">
                    <div class="text-xl font-bold">Philippine Christian University</div>
                    <div class="text-sm font-semibold">
                        <span>Online Election System</span>
                        <span class="text-xs text-blue-100">@yield('dashboard-title', 'Admin Dashboard')</span>
                    </div>
                </div>
            </a>

            <button id="menu-toggle" class="text-2xl text-white mt-2 focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="hidden w-full flex-col space-y-2 text-center mt-2" id="menu">
                <li><a class="hover:underline block py-1" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a class="hover:underline block py-1" href="{{ route('admin.elections.index') }}">Elections</a></li>
                <li>
                    <a class="hover:underline block py-1" href="{{ route('admin.candidates.index') }}">
                        Candidates
                        @php
                            $pendingCandidates = (int) data_get($adminWidget ?? [], 'candidates.pending', 0);
                        @endphp
                        @if($pendingCandidates > 0)
                            <span class="ml-2 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[11px] font-bold">{{ $pendingCandidates > 99 ? '99+' : $pendingCandidates }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a class="hover:underline block py-1" href="{{ route('admin.verified-voters.index') }}">
                        Verify Voters
                        @php
                            $pendingVoterVerification = (int) data_get($adminWidget ?? [], 'voters.pending_verification', 0);
                        @endphp
                        @if($pendingVoterVerification > 0)
                            <span class="ml-2 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[11px] font-bold">{{ $pendingVoterVerification > 99 ? '99+' : $pendingVoterVerification }}</span>
                        @endif
                    </a>
                </li>
                <li><a class="hover:underline block py-1" href="{{ route('admin.reports.index') }}">Reports</a></li>
                <li><a class="hover:underline block py-1" href="{{ route('admin.chatbot.index') }}">Manage Chatbot</a></li>
                <li><a class="hover:underline block py-1" href="{{ route('admin.tickets.index') }}">Manage Tickets</a></li>
                <li><a class="hover:underline block py-1" href="{{ route('admin.profile-settings.index') }}">Settings</a></li>
            </ul>
        </div>

    </div>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let lastScrollTop = 0;
        const navbar = document.querySelector("header");
        let scrollTimeout;

        const timeEl = document.getElementById('admin-live-time');
        const dateEl = document.getElementById('admin-live-date');
        const countdownWrap = document.querySelector('[data-countdown-target]');
        const countdownLabel = document.getElementById('admin-countdown-label');
        const countdownEl = document.getElementById('admin-countdown');
        const countdownSelect = document.getElementById('admin-countdown-election');

        function pad2(n) {
            return String(n).padStart(2, '0');
        }

        function updateLiveTime() {
            const now = new Date();
            const time = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true });
            const date = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'short', day: '2-digit' });
            if (timeEl) timeEl.textContent = time;
            if (dateEl) dateEl.textContent = date;
        }

        function updateCountdown() {
            if (!countdownWrap || !countdownEl || !countdownLabel) return;

            const now = new Date();
            let mode = countdownWrap.getAttribute('data-countdown-mode');
            let targetIso = countdownWrap.getAttribute('data-countdown-target');

            if (countdownSelect) {
                const opt = countdownSelect.options[countdownSelect.selectedIndex];
                const startIso = opt?.getAttribute('data-start-iso') || '';
                const endIso = opt?.getAttribute('data-end-iso') || '';

                const start = startIso ? new Date(startIso) : null;
                const end = endIso ? new Date(endIso) : null;

                if (start && !Number.isNaN(start.getTime()) && now.getTime() < start.getTime()) {
                    mode = 'start';
                    targetIso = start.toISOString();
                } else if (end && !Number.isNaN(end.getTime()) && now.getTime() < end.getTime()) {
                    mode = 'end';
                    targetIso = end.toISOString();
                } else {
                    mode = null;
                    targetIso = null;
                }
            }

            if (!mode || !targetIso) {
                countdownLabel.textContent = '---';
                countdownEl.textContent = '--d --:--:--';
                return;
            }

            const target = new Date(targetIso);
            const diffMs = target.getTime() - now.getTime();
            if (Number.isNaN(target.getTime()) || diffMs <= 0) {
                countdownLabel.textContent = mode === 'start' ? 'Election started' : 'Election ended';
                countdownEl.textContent = '00d 00:00:00';
                return;
            }

            const totalSeconds = Math.floor(diffMs / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            countdownLabel.textContent = mode === 'start' ? 'Starts in' : 'Ends in';
            countdownEl.textContent = `${pad2(days)}d ${pad2(hours)}:${pad2(minutes)}:${pad2(seconds)}`;
        }

        updateLiveTime();
        updateCountdown();
        if (countdownSelect) {
            countdownSelect.addEventListener('change', () => {
                updateCountdown();
            });
        }
        setInterval(() => {
            updateLiveTime();
            updateCountdown();
        }, 1000);

        window.addEventListener("scroll", function () {
            clearTimeout(scrollTimeout);
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop === 0) {
                navbar.style.transform = "translateY(0)";
            } else if (scrollTop > lastScrollTop) {
                navbar.style.transform = "translateY(-100%)";
            } else {
                navbar.style.transform = "translateY(0)";
            }

            lastScrollTop = scrollTop;

            // Keep navbar visible for a short period before hiding it
            scrollTimeout = setTimeout(() => {
                if (window.pageYOffset > 100) {
                    navbar.style.transform = "translateY(-100%)";
                }
            }, 1500); // 1.5 seconds before hiding again
        });

        // Admin profile dropdown (desktop)
        const adminProfileToggle = document.getElementById('admin-profile-toggle');
        const adminProfileMenu = document.getElementById('admin-profile-menu');

        if (adminProfileToggle && adminProfileMenu) {
            const setAdminProfileMenuOpen = (isOpen) => {
                adminProfileMenu.classList.toggle('hidden', !isOpen);
                adminProfileToggle.setAttribute('aria-expanded', String(isOpen));
                adminProfileToggle.setAttribute('aria-label', isOpen ? 'Close profile menu' : 'Open profile menu');
                document.querySelector('[data-admin-profile-chevron]')?.classList.toggle('rotate-180', isOpen);
            };

            adminProfileToggle.addEventListener('click', (event) => {
                event.stopPropagation();
                setAdminProfileMenuOpen(adminProfileToggle.getAttribute('aria-expanded') !== 'true');
            });

            adminProfileMenu.addEventListener('click', (event) => event.stopPropagation());

            document.addEventListener('click', (event) => {
                if (!adminProfileMenu.contains(event.target) && !adminProfileToggle.contains(event.target)) {
                    setAdminProfileMenuOpen(false);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && adminProfileToggle.getAttribute('aria-expanded') === 'true') {
                    setAdminProfileMenuOpen(false);
                    adminProfileToggle.focus();
                }
            });
        }

        // Notifications panel (bell)
        const notifToggle = document.getElementById('admin-notif-toggle');
        const notifPanel = document.getElementById('admin-notif-panel');
        const notifClose = document.getElementById('admin-notif-close');
        const notifBadge = document.getElementById('admin-notif-badge');
        const tabCandidates = document.getElementById('admin-notif-tab-candidates');
        const tabVoters = document.getElementById('admin-notif-tab-voters');
        const wrapCandidates = document.getElementById('admin-notif-candidates');
        const wrapVoters = document.getElementById('admin-notif-voters');
        const candidatesList = document.getElementById('admin-notif-candidates-list');
        const votersList = document.getElementById('admin-notif-voters-list');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function setNotifTab(tab) {
            if (!tabCandidates || !tabVoters || !wrapCandidates || !wrapVoters) return;
            if (tab === 'voters') {
                tabVoters.classList.add('bg-blue-50', 'text-blue-700');
                tabCandidates.classList.remove('bg-blue-50', 'text-blue-700');
                wrapVoters.classList.remove('hidden');
                wrapCandidates.classList.add('hidden');
            } else {
                tabCandidates.classList.add('bg-blue-50', 'text-blue-700');
                tabVoters.classList.remove('bg-blue-50', 'text-blue-700');
                wrapCandidates.classList.remove('hidden');
                wrapVoters.classList.add('hidden');
            }
        }

        function updateBadge(total) {
            if (!notifBadge) return;
            if (!total || total <= 0) {
                notifBadge.classList.add('hidden');
                return;
            }
            notifBadge.classList.remove('hidden');
            notifBadge.textContent = total > 99 ? '99+' : String(total);
        }

        function renderEmpty(container, text) {
            if (!container) return;
            container.innerHTML = `<div class="px-4 py-6 text-sm text-gray-500">${text}</div>`;
        }

        function renderCandidates(items) {
            if (!candidatesList) return;
            if (!items || items.length === 0) return renderEmpty(candidatesList, 'No pending candidates.');
            candidatesList.innerHTML = items.map((c) => {
                const name = c.name || ('#' + c.id);
                return `
                    <div class="px-4 py-3 hover:bg-gray-50">
                        <div class="flex items-start justify-between gap-3">
                            <a class="text-sm font-semibold text-gray-900 hover:underline" href="{{ route('admin.candidates.index') }}?status=Pending">${name}</a>
                            <div class="flex gap-2">
                                <button type="button" class="notif-candidate-action px-3 py-1.5 text-xs font-semibold rounded bg-emerald-600 text-white hover:bg-emerald-700" data-action="approve" data-id="${c.id}">Approve</button>
                                <button type="button" class="notif-candidate-action px-3 py-1.5 text-xs font-semibold rounded bg-amber-600 text-white hover:bg-amber-700" data-action="reject" data-id="${c.id}">Reject</button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        let voterAcademicYears = [];
        let voterSemesters = [];
        let currentVoters = [];

        function renderVoters(items) {
            if (!votersList) return;
            currentVoters = Array.isArray(items) ? items : [];
            items = currentVoters;
            if (!items || items.length === 0) return renderEmpty(votersList, 'No voters waiting for verification.');
            votersList.replaceChildren();

            items.forEach((user) => {
                const item = document.createElement('div');
                item.className = 'px-4 py-3 hover:bg-gray-50';

                const header = document.createElement('div');
                header.className = 'flex items-start justify-between gap-3';

                const voterLink = document.createElement('a');
                voterLink.className = 'text-sm font-semibold text-gray-900 hover:underline';
                voterLink.href = `{{ route('admin.verified-voters.index') }}?focus=${encodeURIComponent(user.id)}`;
                voterLink.textContent = user.name || `User #${user.id}`;

                const details = document.createElement('div');
                details.className = 'text-xs text-gray-500';
                details.textContent = user.idnumber ? `Student No: ${user.idnumber}` : 'Needs verification';

                const actions = document.createElement('div');
                actions.className = 'flex shrink-0 gap-2';
                actions.dataset.voterActions = '';
                actions.dataset.userId = user.id;

                const verify = document.createElement('button');
                verify.type = 'button';
                verify.className = 'notif-voter-action rounded bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700';
                verify.dataset.action = 'verify';
                verify.textContent = 'Verify';

                const reject = document.createElement('button');
                reject.type = 'button';
                reject.className = 'notif-voter-action rounded bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700';
                reject.dataset.action = 'reject';
                reject.textContent = 'Reject';

                actions.append(verify, reject);
                header.append(voterLink, actions);
                item.append(header, details);
                votersList.append(item);
            });
        }

        async function loadNotifs() {
            try {
                const res = await fetch("{{ route('admin.notifications.index') }}", {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                if (!res.ok) throw new Error('Unable to load admin notifications.');
                const data = await res.json();
                const c = Number(data?.counts?.candidates_pending || 0);
                const v = Number(data?.counts?.voters_pending_verification || 0);
                voterAcademicYears = Array.isArray(data?.academic_years) ? data.academic_years : [];
                voterSemesters = Array.isArray(data?.semesters) ? data.semesters : [];
                updateBadge(c + v);
                renderCandidates(data?.candidates || []);
                renderVoters(data?.voters || []);
            } catch (e) {
                console.error(e);
                renderEmpty(candidatesList, 'Failed to load notifications.');
                renderEmpty(votersList, 'Failed to load notifications.');
            }
        }

        async function actCandidate(id, action) {
            const url = action === 'approve'
                ? `{{ url('/admin/candidates') }}/${id}/approve`
                : `{{ url('/admin/candidates') }}/${id}/reject`;
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf || '',
                },
                credentials: 'same-origin'
            });
            if (!res.ok) throw new Error('Failed');
        }

        async function submitVoterAction(userId, action, acadyrId, semesterId) {
            const isReject = action === 'reject';
            const url = isReject
                ? `{{ url('/admin/verified-voters') }}/${encodeURIComponent(userId)}/reject`
                : "{{ route('admin.verified-voters.store') }}";
            const body = isReject ? undefined : JSON.stringify({
                user_id: userId,
                acadyr_id: acadyrId,
                sem_id: semesterId,
            });
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf || '',
                    ...(body ? { 'Content-Type': 'application/json' } : {}),
                },
                body,
                credentials: 'same-origin'
            });
            const data = await res.json();
            if (!res.ok || data?.success !== true) {
                throw new Error(data?.message || 'Unable to update voter application.');
            }
        }

        function showVoterApprovalForm(userId, actions) {
            actions.replaceChildren();

            const form = document.createElement('div');
            form.className = 'mt-3 grid grid-cols-1 gap-2';
            form.dataset.voterApprovalForm = '';

            const yearSelect = document.createElement('select');
            yearSelect.className = 'w-full rounded border border-gray-300 px-2 py-1 text-xs text-gray-800';
            yearSelect.setAttribute('aria-label', 'Academic year');
            const yearPlaceholder = new Option('Select academic year', '');
            yearSelect.add(yearPlaceholder);
            voterAcademicYears.forEach((year) => yearSelect.add(new Option(year.name || `Year ${year.id}`, year.id)));

            const semesterSelect = document.createElement('select');
            semesterSelect.className = 'w-full rounded border border-gray-300 px-2 py-1 text-xs text-gray-800';
            semesterSelect.setAttribute('aria-label', 'Semester');
            semesterSelect.add(new Option('Select semester', ''));
            voterSemesters.forEach((semester) => semesterSelect.add(new Option(semester.name || `Semester ${semester.id}`, semester.id)));

            const message = document.createElement('p');
            message.className = 'hidden text-xs text-red-600';
            message.setAttribute('role', 'alert');
            if (voterAcademicYears.length === 0 || voterSemesters.length === 0) {
                message.textContent = 'Set up an academic year and semester before verifying voters.';
                message.classList.remove('hidden');
            }

            const controls = document.createElement('div');
            controls.className = 'flex justify-end gap-2';

            const cancel = document.createElement('button');
            cancel.type = 'button';
            cancel.className = 'notif-voter-action rounded bg-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-300';
            cancel.dataset.action = 'cancel';
            cancel.textContent = 'Cancel';

            const confirm = document.createElement('button');
            confirm.type = 'button';
            confirm.className = 'notif-voter-action rounded bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50';
            confirm.dataset.action = 'confirm';
            confirm.textContent = 'Confirm';
            confirm.disabled = voterAcademicYears.length === 0 || voterSemesters.length === 0;

            controls.append(cancel, confirm);
            form.append(yearSelect, semesterSelect, message, controls);
            actions.append(form);
        }

        function openNotifs() {
            if (!notifPanel || !notifToggle) return;
            notifPanel.classList.remove('hidden');
            notifToggle.setAttribute('aria-expanded', 'true');
            setNotifTab('candidates');
            loadNotifs();
        }

        function closeNotifs() {
            if (!notifPanel || !notifToggle) return;
            notifPanel.classList.add('hidden');
            notifToggle.setAttribute('aria-expanded', 'false');
        }

        if (tabCandidates) tabCandidates.addEventListener('click', (e) => { e.preventDefault(); setNotifTab('candidates'); });
        if (tabVoters) tabVoters.addEventListener('click', (e) => { e.preventDefault(); setNotifTab('voters'); });

        if (notifToggle) {
            notifToggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (!notifPanel) return;
                const isOpen = !notifPanel.classList.contains('hidden');
                if (isOpen) closeNotifs();
                else openNotifs();
            });
        }

        if (notifClose) {
            notifClose.addEventListener('click', (e) => {
                e.preventDefault();
                closeNotifs();
            });
        }

        if (notifPanel) {
            notifPanel.addEventListener('click', (e) => {
                e.stopPropagation();
                const btn = e.target.closest('.notif-candidate-action');
                if (btn) {
                    const id = btn.getAttribute('data-id');
                    const action = btn.getAttribute('data-action');
                    if (!id || !action) return;
                    btn.disabled = true;
                    btn.classList.add('opacity-60');
                    actCandidate(id, action)
                        .then(() => loadNotifs())
                        .catch((error) => {
                            console.error(error);
                            renderEmpty(candidatesList, 'Could not update candidate. Please try again.');
                        })
                        .finally(() => {
                            btn.disabled = false;
                            btn.classList.remove('opacity-60');
                        });
                    return;
                }

                const voterButton = e.target.closest('.notif-voter-action');
                if (!voterButton) return;
                const voterActions = voterButton.closest('[data-voter-actions]');
                const userId = voterActions?.dataset.userId;
                const action = voterButton.dataset.action;
                if (!voterActions || !userId || !action) return;

                if (action === 'verify') {
                    showVoterApprovalForm(userId, voterActions);
                    return;
                }
                if (action === 'cancel') {
                    renderVotersFromCurrentData();
                    return;
                }

                if (action === 'confirm') {
                    const form = voterButton.closest('[data-voter-approval-form]');
                    const yearSelect = form?.querySelector('select[aria-label="Academic year"]');
                    const semesterSelect = form?.querySelector('select[aria-label="Semester"]');
                    const message = form?.querySelector('[role="alert"]');
                    if (!yearSelect?.value || !semesterSelect?.value || !message) {
                        if (message) {
                            message.textContent = 'Select an academic year and semester.';
                            message.classList.remove('hidden');
                        }
                        return;
                    }

                    voterButton.disabled = true;
                    submitVoterAction(userId, 'verify', yearSelect.value, semesterSelect.value)
                        .then(() => loadNotifs())
                        .catch((error) => {
                            console.error(error);
                            message.textContent = error.message;
                            message.classList.remove('hidden');
                            voterButton.disabled = false;
                        });
                    return;
                }

                if (action === 'reject') {
                    voterButton.disabled = true;
                    submitVoterAction(userId, 'reject')
                        .then(() => loadNotifs())
                        .catch((error) => {
                            console.error(error);
                            renderEmpty(votersList, 'Could not reject this application. Please try again.');
                        })
                        .finally(() => {
                            voterButton.disabled = false;
                        });
                }
            });
        }

        function renderVotersFromCurrentData() {
            renderVoters(currentVoters);
        }

        document.addEventListener('click', () => {
            closeNotifs();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeNotifs();
        });

        window.setInterval(() => {
            if (notifPanel && !notifPanel.classList.contains('hidden')) loadNotifs();
        }, 30000);
    });
</script>

<header class="bg-blue-600 text-white p-4 fixed top-0 left-0 w-full shadow-md transition-transform duration-300 z-50">
    <div class="container mx-auto">
        <div class="hidden lg:flex items-center justify-center">
            <div class="flex items-center justify-center gap-6">
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

            <nav>
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
                    <button id="admin-profile-toggle" class="flex items-center space-x-2 focus:outline-none text-base">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-white text-blue-600 rounded-full">
                            <i class="fas fa-user"></i>
                        </span>
                        <span class="text-base no-wrap">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </button>

                    <div id="admin-profile-menu" class="hidden absolute right-0 mt-2 w-56 bg-white text-gray-800 rounded-md shadow-lg z-50">
                        <div class="px-4 py-2 border-b">
                            <p class="font-semibold text-sm truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="px-4 py-2 text-sm">
                            <p class="mb-1">Student No: {{ Auth::user()->idnumber }}</p>
                        </div>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="border-t">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Log out</button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
            </div>
        </div>

        <div class="lg:hidden flex flex-col items-center">
            <a href="{{ route('admin.dashboard') }}" class="text-inherit inline-flex items-center gap-3">
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
            adminProfileToggle.addEventListener('click', (event) => {
                event.stopPropagation();
                adminProfileMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', (event) => {
                if (!adminProfileMenu.classList.contains('hidden') &&
                    !adminProfileMenu.contains(event.target) &&
                    event.target !== adminProfileToggle &&
                    !adminProfileToggle.contains(event.target)) {
                    adminProfileMenu.classList.add('hidden');
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

        function renderVoters(items) {
            if (!votersList) return;
            if (!items || items.length === 0) return renderEmpty(votersList, 'No voters waiting for verification.');
            votersList.innerHTML = items.map((u) => {
                const name = u.name || ('User #' + u.id);
                const idnum = u.idnumber ? ` (${u.idnumber})` : '';
                return `
                    <a class="block px-4 py-3 hover:bg-gray-50" href="{{ route('admin.verified-voters.index') }}?focus=${u.id}">
                        <div class="text-sm font-semibold text-gray-900">${name}${idnum}</div>
                        <div class="text-xs text-gray-500">Needs verification</div>
                    </a>
                `;
            }).join('');
        }

        async function loadNotifs() {
            try {
                const res = await fetch("{{ route('admin.notifications.index') }}", {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                const data = await res.json();
                const c = Number(data?.counts?.candidates_pending || 0);
                const v = Number(data?.counts?.voters_pending_verification || 0);
                updateBadge(c + v);
                renderCandidates(data?.candidates || []);
                renderVoters(data?.voters || []);
            } catch (e) {
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
                if (!btn) return;
                const id = btn.getAttribute('data-id');
                const action = btn.getAttribute('data-action');
                if (!id || !action) return;
                btn.disabled = true;
                btn.classList.add('opacity-60');
                actCandidate(id, action)
                    .then(() => loadNotifs())
                    .finally(() => {
                        btn.disabled = false;
                        btn.classList.remove('opacity-60');
                    });
            });
        }

        document.addEventListener('click', () => {
            closeNotifs();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeNotifs();
        });
    });
</script>

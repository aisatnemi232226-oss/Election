@auth
    @php
        $studentVerificationUnreadCount = auth()->user()
            ->unreadNotifications()
            ->where('type', \App\Notifications\StudentVoterVerified::class)
            ->count();
    @endphp
    <li class="relative">
        <button
            type="button"
            class="relative inline-flex h-10 w-10 items-center justify-center rounded-full text-white transition hover:bg-white/15 focus:outline-none focus:ring-2 focus:ring-white"
            aria-label="Notifications"
            aria-haspopup="true"
            aria-expanded="false"
            data-student-notification-toggle
            data-list-url="{{ route('student.notifications.index') }}"
            data-read-url="{{ route('student.notifications.read') }}"
        >
            <i class="fas fa-bell" aria-hidden="true"></i>
            <span data-student-notification-badge class="{{ $studentVerificationUnreadCount > 0 ? '' : 'hidden' }} absolute -right-1 -top-1 inline-flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-600 px-1 text-[11px] font-bold text-white">
                {{ $studentVerificationUnreadCount > 99 ? '99+' : $studentVerificationUnreadCount }}
            </span>
        </button>
        <section id="student-notification-panel" class="fixed right-4 top-20 z-[60] hidden w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl bg-white text-left text-gray-800 shadow-xl sm:w-96" aria-label="Notifications">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <h2 class="font-semibold">Notifications</h2>
                <button type="button" id="student-notification-close" class="rounded px-2 text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Close notifications">
                    <span aria-hidden="true" class="text-xl leading-none">&times;</span>
                </button>
            </div>
            <div id="student-notification-list" class="max-h-80 overflow-y-auto" aria-live="polite">
                <p class="px-4 py-6 text-sm text-gray-500">Open notifications to check for updates.</p>
            </div>
        </section>
    </li>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggles = [...document.querySelectorAll('[data-student-notification-toggle]')];
            const panel = document.getElementById('student-notification-panel');
            const close = document.getElementById('student-notification-close');
            const badges = [...document.querySelectorAll('[data-student-notification-badge]')];
            const list = document.getElementById('student-notification-list');

            if (!toggles.length || !panel || !badges.length || !list) return;
            document.body.append(panel);

            function setUnreadCount(count) {
                const unreadCount = Number(count) || 0;
                badges.forEach((badge) => {
                    badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
                    badge.classList.toggle('hidden', unreadCount === 0);
                });
            }

            function showMessage(message) {
                list.replaceChildren();
                const item = document.createElement('p');
                item.className = 'px-4 py-6 text-sm text-gray-500';
                item.textContent = message;
                list.append(item);
            }

            function renderNotifications(notifications) {
                list.replaceChildren();
                if (!notifications.length) {
                    showMessage('No notifications yet.');
                    return;
                }

                notifications.forEach((notification) => {
                    const link = document.createElement('a');
                    link.className = 'block border-b px-4 py-3 transition hover:bg-gray-50';
                    link.href = notification.url;

                    const title = document.createElement('p');
                    title.className = 'text-sm font-semibold text-gray-900';
                    title.textContent = notification.title;

                    const message = document.createElement('p');
                    message.className = 'mt-1 text-sm leading-5 text-gray-600';
                    message.textContent = notification.message;

                    link.append(title, message);
                    list.append(link);
                });
            }

            async function refreshNotifications(markRead) {
                const response = await fetch(toggles[0].dataset.listUrl, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                if (!response.ok) throw new Error('Unable to load notifications.');
                const data = await response.json();
                renderNotifications(data.notifications || []);
                setUnreadCount(data.unread_count);

                if (markRead && Number(data.unread_count) > 0) {
                    const readResponse = await fetch(toggles[0].dataset.readUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        credentials: 'same-origin'
                    });
                    if (!readResponse.ok) throw new Error('Unable to mark notifications as read.');
                    setUnreadCount(0);
                }
            }

            function closePanel() {
                panel.classList.add('hidden');
                toggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'false'));
            }

            toggles.forEach((toggle) => toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                const opening = panel.classList.contains('hidden');
                panel.classList.toggle('hidden', !opening);
                toggles.forEach((item) => item.setAttribute('aria-expanded', String(opening)));
                if (opening) {
                    refreshNotifications(true).catch((error) => {
                        console.error(error);
                        showMessage('Notifications could not be loaded. Please try again.');
                    });
                }
            }));

            close?.addEventListener('click', closePanel);
            panel.addEventListener('click', (event) => event.stopPropagation());
            document.addEventListener('click', closePanel);
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closePanel();
            });

            window.setInterval(() => {
                refreshNotifications(!panel.classList.contains('hidden'))
                    .catch((error) => {
                        console.error(error);
                        if (!panel.classList.contains('hidden')) {
                            showMessage('Notifications could not be refreshed. Please try again.');
                        }
                    });
            }, 30000);
        });
    </script>
@endauth

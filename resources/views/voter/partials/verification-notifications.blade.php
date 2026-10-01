@auth
    @php
        $studentVerificationUnreadCount = auth()->user()
            ->unreadNotifications()
            ->where('type', \App\Notifications\StudentVoterVerified::class)
            ->count();
    @endphp
    <li>
        <button
            type="button"
            class="icon-button notification-toggle"
            aria-label="Notifications"
            aria-haspopup="true"
            aria-expanded="false"
            data-student-notification-toggle
            data-list-url="{{ route('student.notifications.index') }}"
            data-read-url="{{ route('student.notifications.read') }}"
        >
            <svg class="icon" aria-hidden="true"><use href="#pcu-bell"/></svg>
            <span
                data-student-notification-badge
                class="notification-badge"
                @if($studentVerificationUnreadCount === 0) hidden @endif
            >{{ $studentVerificationUnreadCount > 99 ? '99+' : $studentVerificationUnreadCount }}</span>
        </button>
        <section id="student-notification-panel" class="notification-panel" aria-label="Notifications" hidden>
            <div class="notification-panel-head">
                <h2>Notifications</h2>
                <button type="button" id="student-notification-close" class="icon-button" aria-label="Close notifications">
                    <svg class="icon" aria-hidden="true"><use href="#pcu-x"/></svg>
                </button>
            </div>
            <div id="student-notification-list" class="notification-panel-content" aria-live="polite">
                <p class="notification-empty">Open notifications to check for updates.</p>
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
                    badge.hidden = unreadCount === 0;
                });
            }

            function showMessage(message) {
                list.replaceChildren();
                const item = document.createElement('p');
                item.className = 'notification-empty';
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
                    link.className = 'notification-item';
                    link.href = notification.url;

                    const title = document.createElement('strong');
                    title.textContent = notification.title;
                    const message = document.createElement('span');
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
                panel.hidden = true;
                toggles.forEach((toggle) => toggle.setAttribute('aria-expanded', 'false'));
            }

            toggles.forEach((toggle) => toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                const opening = panel.hidden;
                panel.hidden = !opening;
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
                refreshNotifications(!panel.hidden).catch((error) => {
                    console.error(error);
                    if (!panel.hidden) showMessage('Notifications could not be refreshed. Please try again.');
                });
            }, 30000);
        });
    </script>
@endauth

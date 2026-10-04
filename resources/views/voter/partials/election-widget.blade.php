@push('styles')
<style>
    body.voter-home-page .election-widget {
        --election-widget-surface: var(--card);
        --election-widget-soft: var(--bg);
        --election-widget-border: var(--line);
        --election-widget-text: var(--ink);
        --election-widget-muted: var(--mut);
        --election-widget-brand: var(--brand);
        --election-widget-success: var(--ok);
        container-type: inline-size;
        min-width: 0;
        max-height: min(76vh, 620px);
        overflow-y: auto;
        padding: clamp(18px, 2.4vw, 24px);
        border: 1px solid var(--election-widget-border);
        border-radius: 20px;
        background: var(--election-widget-surface);
        box-shadow: var(--sh);
        color: var(--election-widget-text);
    }

    body.voter-home-page .election-widget-dock {
        position: fixed;
        z-index: 9998;
        bottom: max(20px, env(safe-area-inset-bottom));
        left: max(20px, env(safe-area-inset-left));
    }

    body.voter-home-page .election-widget-panel {
        position: absolute;
        bottom: 62px;
        left: 0;
        width: min(378px, calc(100vw - 40px));
    }

    body.voter-home-page .election-widget[hidden] {
        display: none !important;
    }

    body.voter-home-page .election-widget-launcher {
        display: inline-flex;
        width: 48px;
        height: 48px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--line);
        border-radius: 50%;
        background: var(--card);
        box-shadow: 0 8px 20px rgba(2, 6, 23, .12);
        color: var(--brand);
        cursor: pointer;
    }

    body.voter-home-page .election-widget-launcher[hidden] {
        display: none !important;
    }

    body.voter-home-page .election-widget-launcher .icon {
        width: 21px;
        height: 21px;
    }

    body.voter-home-page .election-widget-launcher:hover {
        transform: translateY(-2px);
    }

    body.voter-home-page .election-widget-launcher:focus-visible,
    body.voter-home-page .election-widget-close:focus-visible {
        outline: 3px solid var(--brand);
        outline-offset: 3px;
    }

    body.voter-home-page .election-widget-heading,
    body.voter-home-page .election-widget-brand,
    body.voter-home-page .election-widget-status,
    body.voter-home-page .election-widget-close,
    body.voter-home-page .election-widget-turnout-heading,
    body.voter-home-page .election-widget-actions,
    body.voter-home-page .election-widget-title-row {
        display: flex;
        align-items: center;
    }

    body.voter-home-page .election-widget-heading,
    body.voter-home-page .election-widget-title-row,
    body.voter-home-page .election-widget-turnout-heading {
        justify-content: space-between;
        gap: 12px;
    }

    body.voter-home-page .election-widget-close {
        display: inline-flex;
        width: 34px;
        height: 34px;
        flex: none;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--election-widget-border);
        border-radius: 10px;
        background: var(--election-widget-soft);
        color: var(--election-widget-muted);
        cursor: pointer;
        font-size: 1.15rem;
        line-height: 1;
    }

    body.voter-home-page .election-widget-brand {
        min-width: 0;
        gap: 9px;
        color: var(--election-widget-brand);
        font-size: .72rem;
        font-weight: 850;
        letter-spacing: .12em;
    }

    body.voter-home-page .election-widget-brand .icon {
        width: 19px;
        height: 19px;
    }

    body.voter-home-page .election-widget-status {
        flex: none;
        gap: 7px;
        max-width: 54%;
        border: 1px solid var(--election-widget-border);
        border-radius: 999px;
        padding: 5px 9px;
        background: var(--election-widget-soft);
        color: var(--election-widget-muted);
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .045em;
        line-height: 1.25;
        text-align: center;
        text-transform: uppercase;
    }

    body.voter-home-page .election-widget-status::before,
    body.voter-home-page .election-widget-freshness::before {
        width: 7px;
        height: 7px;
        flex: none;
        border-radius: 50%;
        background: currentColor;
        content: "";
    }

    body.voter-home-page .election-widget-status[data-state="voting_open"],
    body.voter-home-page .election-widget-status[data-state="results_available"],
    body.voter-home-page .election-widget-freshness[data-freshness-state="live"] {
        color: var(--election-widget-success);
    }

    body.voter-home-page .election-widget-title {
        flex: 1;
        min-width: 0;
        margin: 12px 0 3px;
        color: var(--election-widget-text);
        font-size: 1.18rem;
        font-weight: 800;
        letter-spacing: -.025em;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    body.voter-home-page .election-widget-summary {
        min-height: 1.5em;
        margin: 0;
        color: var(--election-widget-muted);
        font-size: .9rem;
        line-height: 1.45;
    }

    body.voter-home-page .election-widget-election-picker {
        display: grid;
        gap: 6px;
        margin-top: 14px;
    }

    body.voter-home-page .election-widget-election-picker[hidden] {
        display: none !important;
    }

    body.voter-home-page .election-widget-election-picker label {
        color: var(--election-widget-muted);
        font-size: .74rem;
        font-weight: 750;
    }

    body.voter-home-page .election-widget-election-picker select {
        width: 100%;
        min-width: 0;
        padding: 9px 34px 9px 11px;
        border: 1px solid var(--election-widget-border);
        border-radius: 10px;
        background: var(--election-widget-surface);
        color: var(--election-widget-text);
        font: inherit;
        font-size: .85rem;
    }

    body.voter-home-page .election-widget-election-picker select:focus-visible {
        outline: 3px solid color-mix(in srgb, var(--election-widget-brand), transparent 65%);
        outline-offset: 2px;
    }

    body.voter-home-page .election-widget-countdown {
        margin-top: 17px;
        padding: 13px 14px;
        border: 1px solid var(--election-widget-border);
        border-radius: 14px;
        background: var(--election-widget-soft);
    }

    body.voter-home-page .election-widget-countdown-label {
        margin: 0 0 5px;
        color: var(--election-widget-muted);
        font-size: .78rem;
        font-weight: 650;
    }

    body.voter-home-page .election-widget-countdown-value {
        color: var(--election-widget-brand);
        font-size: clamp(1.08rem, 2vw, 1.35rem);
        font-variant-numeric: tabular-nums;
        font-weight: 850;
        letter-spacing: .015em;
        line-height: 1.3;
        white-space: nowrap;
    }

    body.voter-home-page .election-widget-turnout {
        margin-top: 15px;
    }

    body.voter-home-page .election-widget-turnout-heading {
        color: var(--election-widget-muted);
        font-size: .8rem;
    }

    body.voter-home-page .election-widget-turnout-heading strong {
        color: var(--election-widget-text);
        font-variant-numeric: tabular-nums;
        font-size: .87rem;
    }

    body.voter-home-page .election-widget-progress {
        height: 9px;
        overflow: hidden;
        margin-top: 8px;
        border-radius: 999px;
        background: var(--election-widget-soft);
    }

    body.voter-home-page .election-widget-progress-fill {
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: var(--election-widget-brand);
    }

    body.voter-home-page .election-widget-next {
        margin: 12px 0 0;
        color: var(--election-widget-muted);
        font-size: .78rem;
        line-height: 1.45;
    }

    body.voter-home-page .election-widget-next strong {
        color: var(--election-widget-text);
    }

    body.voter-home-page .election-widget-results-note {
        margin: 10px 0 0;
        color: var(--election-widget-muted);
        font-size: .76rem;
        line-height: 1.4;
    }

    body.voter-home-page .election-widget-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 16px;
        padding-top: 13px;
        border-top: 1px solid var(--election-widget-border);
    }

    body.voter-home-page .election-widget-freshness {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 0;
        margin: 0;
        color: var(--election-widget-muted);
        font-size: .7rem;
        line-height: 1.35;
    }

    body.voter-home-page .election-widget-freshness::before {
        width: 6px;
        height: 6px;
    }

    body.voter-home-page .election-widget-actions {
        flex: none;
        justify-content: flex-end;
        gap: 8px;
    }

    body.voter-home-page .election-widget-action {
        display: inline-flex;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid transparent;
        border-radius: 11px;
        padding: 8px 12px;
        background: var(--brand);
        color: var(--brand-contrast);
        font-size: .82rem;
        font-weight: 750;
        line-height: 1.2;
        text-align: center;
        transition: background-color .16s ease, border-color .16s ease, transform .16s ease;
    }

    body.voter-home-page .election-widget-action:hover {
        transform: translateY(-1px);
        background: var(--brand-d);
    }

    body.voter-home-page .election-widget-action:focus-visible {
        outline: 3px solid var(--brand);
        outline-offset: 3px;
    }

    body.voter-home-page .election-widget-action-secondary {
        border-color: var(--election-widget-border);
        background: var(--election-widget-surface);
        color: var(--election-widget-brand);
    }

    body.voter-home-page .election-widget-action-secondary:hover {
        background: var(--election-widget-soft);
    }

    body.voter-home-page .election-widget [hidden] {
        display: none !important;
    }

    @container (max-width: 360px) {
        body.voter-home-page .election-widget {
            padding: 16px;
        }

        body.voter-home-page .election-widget-heading {
            align-items: flex-start;
        }

        body.voter-home-page .election-widget-status {
            max-width: 48%;
        }

        body.voter-home-page .election-widget-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        body.voter-home-page .election-widget-actions {
            width: 100%;
        }

        body.voter-home-page .election-widget-action {
            flex: 1;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        body.voter-home-page .election-widget-action {
            transition: none;
        }

        body.voter-home-page .election-widget-action:hover {
            transform: none;
        }
    }

    @media (max-width: 440px) {
        body.voter-home-page .election-widget-dock {
            bottom: max(14px, env(safe-area-inset-bottom));
            left: max(14px, env(safe-area-inset-left));
        }

        body.voter-home-page .election-widget-panel {
            bottom: 62px;
            width: min(378px, calc(100vw - 28px));
        }
    }
</style>
@endpush

<div class="election-widget-dock" data-election-widget-dock>
    <button
        class="election-widget-launcher"
        type="button"
        data-election-widget-open
        aria-label="Open election widget"
        aria-expanded="false"
        aria-controls="election-widget-panel"
        title="Show election widget"
    >
        <svg class="icon" aria-hidden="true"><use href="#pcu-vote"/></svg>
    </button>

    <article
        class="election-widget election-widget-panel"
        id="election-widget-panel"
        data-election-widget
        data-endpoint="{{ route('election.widget') }}"
        aria-labelledby="election-widget-title"
        hidden
    >
        <div class="election-widget-heading">
            <span class="election-widget-brand">
                <svg class="icon" aria-hidden="true"><use href="#pcu-vote"/></svg>
                PCU ELECTION
            </span>
            <span class="election-widget-status" data-election-state data-state="loading">Updating</span>
            <button class="election-widget-close" type="button" data-election-widget-close aria-label="Close election widget">×</button>
        </div>
        <div class="election-widget-election-picker" data-election-picker hidden>
            <label for="election-widget-select">Election timeline</label>
            <select id="election-widget-select" data-election-select aria-label="Choose an election timeline">
                <option value="">Loading elections…</option>
            </select>
        </div>
        <div class="election-widget-title-row">
            <h3 class="election-widget-title" id="election-widget-title" data-election-name>Current student election</h3>
        </div>
        <p class="election-widget-summary" data-election-summary>Loading election information.</p>

        <div class="election-widget-countdown">
            <p class="election-widget-countdown-label" data-countdown-label>Checking the election schedule</p>
            <div class="election-widget-countdown-value" data-countdown aria-label="Election countdown">--</div>
        </div>

        <div class="election-widget-turnout">
            <div class="election-widget-turnout-heading">
                <span>Voter turnout</span>
                <strong data-turnout>--</strong>
            </div>
            <div class="election-widget-turnout-heading">
                <span><span data-voters-participated>--</span> participated</span>
                <span><span data-eligible-voters>--</span> eligible</span>
            </div>
            <div
                class="election-widget-progress"
                data-turnout-progress
                role="progressbar"
                aria-label="Voter turnout"
                aria-valuemin="0"
                aria-valuemax="100"
                aria-valuenow="0"
            >
                <div class="election-widget-progress-fill" data-turnout-fill></div>
            </div>
        </div>

        <p class="election-widget-next" data-next-event hidden></p>
        <p class="election-widget-results-note" data-results-note>Results will be available after official publication.</p>

        <div class="election-widget-footer">
            <p class="election-widget-freshness" data-freshness data-freshness-state="updating">Updating election information</p>
            <div class="election-widget-actions">
                <a class="election-widget-action election-widget-action-secondary" href="{{ route('result') }}" data-results-action hidden>Results</a>
                <a class="election-widget-action" href="{{ route('election.details') }}" data-election-action>
                    <span data-election-action-label>View Election</span>
                    <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
                </a>
            </div>
        </div>
        <p class="visually-hidden" data-widget-announcement role="status" aria-live="polite" aria-atomic="true"></p>
    </article>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-election-widget-dock]').forEach(initializeElectionWidgetDock);
    document.querySelectorAll('[data-election-widget]:not([data-initialized])').forEach(initializeElectionWidget);
});

function initializeElectionWidgetDock(dock) {
    const panel = dock.querySelector('[data-election-widget]');
    const openButton = dock.querySelector('[data-election-widget-open]');
    const closeButton = dock.querySelector('[data-election-widget-close]');

    if (!panel || !openButton || !closeButton) return;

    const closePanel = (restoreFocus = false) => {
        panel.hidden = true;
        openButton.setAttribute('aria-expanded', 'false');
        if (restoreFocus) openButton.focus();
    };

    openButton.addEventListener('click', () => {
        panel.hidden = false;
        openButton.hidden = true;
        openButton.setAttribute('aria-expanded', 'true');
        closeButton.focus();
    });
    closeButton.addEventListener('click', () => {
        openButton.hidden = false;
        closePanel(true);
    });
    panel.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            openButton.hidden = false;
            closePanel(true);
        }
    });
}

function initializeElectionWidget(widget) {
    widget.dataset.initialized = 'true';

    const endpoint = widget.dataset.endpoint;
    const numberFormat = new Intl.NumberFormat();
    const elements = {
        name: widget.querySelector('[data-election-name]'),
        picker: widget.querySelector('[data-election-picker]'),
        electionSelect: widget.querySelector('[data-election-select]'),
        summary: widget.querySelector('[data-election-summary]'),
        state: widget.querySelector('[data-election-state]'),
        countdownLabel: widget.querySelector('[data-countdown-label]'),
        countdown: widget.querySelector('[data-countdown]'),
        voters: widget.querySelector('[data-voters-participated]'),
        eligible: widget.querySelector('[data-eligible-voters]'),
        turnout: widget.querySelector('[data-turnout]'),
        turnoutProgress: widget.querySelector('[data-turnout-progress]'),
        turnoutFill: widget.querySelector('[data-turnout-fill]'),
        nextEvent: widget.querySelector('[data-next-event]'),
        resultsNote: widget.querySelector('[data-results-note]'),
        resultsAction: widget.querySelector('[data-results-action]'),
        electionActionLabel: widget.querySelector('[data-election-action-label]'),
        freshness: widget.querySelector('[data-freshness]'),
        announcement: widget.querySelector('[data-widget-announcement]')
    };

    let refreshTimerId = null;
    let countdownTimerId = null;
    let requestController = null;
    let failureCount = 0;
    let lastSuccessAt = null;
    let targetDeadline = null;
    let targetSignature = '';
    let expiryRefreshRequested = false;

    const updateElectionLinks = (electionId) => {
        for (const [selector, route] of [
            ['[data-results-action]', '{{ route('result') }}'],
            ['[data-election-action]', '{{ route('election.details') }}']
        ]) {
            const link = widget.querySelector(selector);
            const url = new URL(route, window.location.href);
            if (electionId) url.searchParams.set('election_id', electionId);
            link.href = url.toString();
        }
    };

    const renderElectionOptions = (elections, selectedId) => {
        const options = Array.isArray(elections) ? elections : [];
        elements.picker.hidden = options.length < 2;
        elements.electionSelect.replaceChildren();

        for (const election of options) {
            const option = document.createElement('option');
            option.value = String(election.id);
            option.textContent = election.label || election.name;
            option.selected = Number(election.id) === Number(selectedId);
            elements.electionSelect.append(option);
        }

        if (options.length && !elements.electionSelect.value) {
            elements.electionSelect.value = String(selectedId || options[0].id);
        }
    };

    const setFreshness = (state, label) => {
        elements.freshness.dataset.freshnessState = state;
        elements.freshness.textContent = label;
    };

    const renderNextEvent = (payload) => {
        const nextPhase = Array.isArray(payload.timeline)
            ? payload.timeline.find((phase) => phase.state === 'upcoming')
            : null;
        const nextEvent = Array.isArray(payload.upcoming_events)
            ? payload.upcoming_events[0]
            : null;

        if (nextPhase) {
            elements.nextEvent.replaceChildren();
            const label = document.createElement('span');
            label.textContent = 'Next phase: ';
            const title = document.createElement('strong');
            title.textContent = nextPhase.title;
            const date = document.createElement('span');
            date.textContent = ` · ${nextPhase.starts_label}`;
            elements.nextEvent.append(label, title, date);
            elements.nextEvent.hidden = false;
        } else if (nextEvent) {
            elements.nextEvent.replaceChildren();
            const label = document.createElement('span');
            label.textContent = 'Upcoming: ';
            const title = document.createElement('strong');
            title.textContent = nextEvent.title;
            const date = document.createElement('span');
            date.textContent = ` · ${nextEvent.display_date}`;
            elements.nextEvent.append(label, title, date);
            elements.nextEvent.hidden = false;
        } else {
            elements.nextEvent.replaceChildren();
            elements.nextEvent.hidden = true;
        }
    };

    const tickCountdown = () => {
        if (targetDeadline === null) return;

        const seconds = Math.max(0, Math.ceil((targetDeadline - performance.now()) / 1000));
        const days = Math.floor(seconds / 86400);
        const hours = Math.floor((seconds % 86400) / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const remainingSeconds = seconds % 60;
        elements.countdown.textContent = `${days}d ${String(hours).padStart(2, '0')}h ${String(minutes).padStart(2, '0')}m ${String(remainingSeconds).padStart(2, '0')}s`;

        if (seconds === 0 && !expiryRefreshRequested) {
            expiryRefreshRequested = true;
            refresh();
        }
    };

    const render = (payload) => {
        const election = payload.election;
        const status = payload.status || 'unavailable';
        const resultsVisible = payload.results?.visible === true;
        renderElectionOptions(payload.elections, election?.id);
        updateElectionLinks(election?.id);
        elements.name.textContent = election?.name || 'Election information';
        elements.summary.textContent = payload.status_label || 'Election information is not available.';
        elements.state.textContent = status.replaceAll('_', ' ');
        elements.state.dataset.state = status;
        elements.electionActionLabel.textContent = status === 'voting_open' ? 'Vote Now' : 'View Election';

        const stats = payload.statistics;
        if (stats) {
            const participated = Math.max(0, Number(stats.voters_participated) || 0);
            const eligible = Math.max(0, Number(stats.eligible_voters) || 0);
            const percentage = Math.min(100, Math.max(0, Number(stats.turnout_percentage) || 0));
            elements.voters.textContent = numberFormat.format(participated);
            elements.eligible.textContent = numberFormat.format(eligible);
            elements.turnout.textContent = `${percentage.toFixed(2)}%`;
            elements.turnoutProgress.setAttribute('aria-valuemax', String(eligible || 100));
            elements.turnoutProgress.setAttribute('aria-valuenow', String(eligible ? Math.min(participated, eligible) : 0));
            elements.turnoutProgress.setAttribute('aria-valuetext', `${percentage.toFixed(2)}% turnout`);
            elements.turnoutFill.style.width = `${percentage}%`;
        } else {
            elements.voters.textContent = '--';
            elements.eligible.textContent = '--';
            elements.turnout.textContent = '--';
            elements.turnoutProgress.setAttribute('aria-valuemax', '100');
            elements.turnoutProgress.setAttribute('aria-valuenow', '0');
            elements.turnoutProgress.setAttribute('aria-valuetext', 'Turnout information unavailable');
            elements.turnoutFill.style.width = '0%';
        }

        const countdownCopy = {
            completed: ['Election status', 'Voting has ended'],
            results_available: ['Results status', 'Official results are available'],
            unavailable: ['Election schedule', 'Election dates are not available']
        };
        const target = payload.countdown?.target_time;
        const signature = `${target || ''}:${payload.countdown?.target_label || ''}`;
        const fallbackCountdown = countdownCopy[status];
        elements.countdown.setAttribute(
            'aria-label',
            target ? `${payload.countdown.target_label} countdown` : 'Election status'
        );
        elements.countdownLabel.textContent = payload.countdown?.target_label
            || fallbackCountdown?.[0]
            || 'Election schedule';
        if (target && signature !== targetSignature) {
            const targetTime = Date.parse(target);
            const serverTime = Date.parse(payload.server_time);
            targetDeadline = Number.isFinite(targetTime) && Number.isFinite(serverTime)
                ? performance.now() + Math.max(0, targetTime - serverTime)
                : null;
            targetSignature = signature;
            expiryRefreshRequested = false;
        } else if (!target) {
            targetDeadline = null;
            targetSignature = signature;
            expiryRefreshRequested = false;
        }

        renderNextEvent(payload);
        if (resultsVisible) {
            elements.resultsNote.hidden = true;
            elements.resultsAction.textContent = 'View Results';
            elements.resultsAction.hidden = false;
        } else {
            elements.resultsNote.textContent = status === 'completed' || status === 'counting'
                ? 'Official results are pending publication. Check the Results page for updates.'
                : 'Official results will be available after voting closes and the totals are published.';
            elements.resultsNote.hidden = false;
            elements.resultsAction.textContent = 'Check Results';
            elements.resultsAction.hidden = status !== 'completed' && status !== 'counting';
        }

        if (!target) {
            elements.countdown.textContent = fallbackCountdown?.[1]
                || (resultsVisible ? 'Official results are available' : 'Election schedule not available');
        } else {
            tickCountdown();
        }
    };

    const renderFailure = () => {
        const elapsedSeconds = lastSuccessAt === null
            ? null
            : Math.floor((performance.now() - lastSuccessAt) / 1000);

        if (elapsedSeconds !== null && elapsedSeconds < 60) {
            setFreshness('stale', `Last update ${elapsedSeconds}s ago · retrying`);
        } else {
            setFreshness('unavailable', 'Election information temporarily unavailable');
        }
        elements.announcement.textContent = 'Live election information is temporarily unavailable.';
    };

    async function refresh() {
        if (document.hidden || requestController) return;

        const controller = new AbortController();
        requestController = controller;
        const timeoutId = window.setTimeout(() => controller.abort(), 8000);

        try {
            const url = new URL(endpoint, window.location.href);
            if (elements.electionSelect.value) {
                url.searchParams.set('election_id', elements.electionSelect.value);
            }
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                cache: 'no-store',
                signal: controller.signal
            });
            if (!response.ok) {
                const error = new Error('Election widget request failed.');
                error.retryAfter = response.status === 429 ? Number(response.headers.get('Retry-After')) : 0;
                throw error;
            }

            const payload = await response.json();
            if (!payload || typeof payload.status !== 'string' || !payload.freshness) {
                throw new Error('Election widget response is invalid.');
            }

            render(payload);
            failureCount = 0;
            lastSuccessAt = performance.now();
            elements.announcement.textContent = payload.status_label || '';
            const age = Number(payload.freshness.cache_age_seconds) || 0;
            const ageInSeconds = Math.floor(age);
            setFreshness(
                age <= 15 ? 'live' : 'updating',
                `${age <= 15 ? 'Live' : 'Updated'} · ${ageInSeconds} seconds ago`
            );
            scheduleRefresh(10000);
        } catch (error) {
            if (document.hidden || controller.signal.aborted && requestController !== controller) return;
            failureCount += 1;
            renderFailure();
            const backoff = Math.min(30000, 5000 * (2 ** Math.min(failureCount - 1, 3)));
            const retryAfter = Number(error.retryAfter) || 0;
            scheduleRefresh(Math.max(backoff, Math.min(60000, retryAfter * 1000)));
        } finally {
            window.clearTimeout(timeoutId);
            if (requestController === controller) requestController = null;
        }
    }

    function scheduleRefresh(delay) {
        window.clearTimeout(refreshTimerId);
        if (!document.hidden) refreshTimerId = window.setTimeout(refresh, delay);
    }

    elements.electionSelect.addEventListener('change', () => {
        window.clearTimeout(refreshTimerId);
        if (requestController) {
            requestController.abort();
            requestController = null;
        }
        refresh();
    });

    const startCountdown = () => {
        if (countdownTimerId === null) countdownTimerId = window.setInterval(tickCountdown, 1000);
    };

    const stopTimers = () => {
        window.clearTimeout(refreshTimerId);
        window.clearInterval(countdownTimerId);
        refreshTimerId = null;
        countdownTimerId = null;
    };

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopTimers();
            if (requestController) {
                requestController.abort();
                requestController = null;
            }
            return;
        }
        startCountdown();
        refresh();
    });

    refresh();
    startCountdown();
}
</script>
@endpush

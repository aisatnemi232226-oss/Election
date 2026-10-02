@extends('layouts.voter')

@section('title', 'Elections')
@section('body-class', 'election-index-page')

@push('styles')
<style>
    body.election-index-page .page-content {
        width: min(1280px, 100% - 2 * clamp(20px, 5vw, 48px));
        padding-block: clamp(36px, 6vw, 72px);
    }

    body.election-index-page .page-intro {
        max-width: 1280px;
        margin: 0 0 clamp(36px, 5vw, 58px);
        text-align: left;
    }

    body.election-index-page .page-intro::before {
        display: block;
        margin-bottom: 10px;
        color: var(--brand);
        content: "PCU ONLINE ELECTION";
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .14em;
    }

    body.election-index-page .page-intro h1 {
        font-size: clamp(2.25rem, 5vw, 3.5rem);
        letter-spacing: -.045em;
        text-wrap: balance;
    }

    body.election-index-page .page-intro p {
        max-width: 55ch;
        margin: 12px 0 0;
        font-size: clamp(1rem, 1.8vw, 1.15rem);
    }

    body.election-index-page .election-list-section + .election-list-section,
    body.election-index-page .page-content > .election-list-section + .surface-card {
        margin-top: clamp(38px, 5vw, 58px);
    }

    body.election-index-page .section-heading {
        align-items: center;
        margin-bottom: 18px;
    }

    body.election-index-page .section-heading h2,
    body.election-index-page .election-history-card h2 {
        margin: 0;
        font-size: clamp(1.4rem, 2.5vw, 1.8rem);
        letter-spacing: -.03em;
    }

    body.election-index-page .carousel-controls {
        gap: 8px;
        flex: none;
    }

    body.election-index-page .carousel-controls[hidden] {
        display: none;
    }

    body.election-index-page .carousel-controls .icon-button {
        width: 44px;
        height: 44px;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 72%);
        border-radius: 14px;
        background: var(--card);
        color: var(--brand);
        box-shadow: 0 4px 12px rgba(15, 27, 51, .06);
        transition: transform .18s ease, background-color .18s ease, color .18s ease, box-shadow .18s ease;
    }

    body.election-index-page .carousel-controls .icon-button:hover {
        transform: translateY(-2px);
        background: var(--brand);
        color: var(--brand-contrast);
        box-shadow: 0 8px 18px color-mix(in srgb, var(--brand), transparent 76%);
    }

    body.election-index-page .carousel-controls .icon-button:active {
        transform: translateY(0) scale(.96);
    }

    body.election-index-page .election-grid {
        grid-template-columns: minmax(0, 1fr);
        grid-auto-rows: 1fr;
        align-items: stretch;
        gap: clamp(14px, 2vw, 22px);
    }

    body.election-index-page .election-card {
        align-self: stretch;
        display: flex;
        min-width: 0;
        min-height: 100%;
        flex-direction: column;
        margin-top: 0;
        overflow: hidden;
        padding: 0;
        border-color: color-mix(in srgb, var(--line), transparent 8%);
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(15, 27, 51, .045), 0 16px 34px rgba(15, 27, 51, .065);
        animation: election-card-in .38s cubic-bezier(.2, .75, .25, 1) both;
        transition: transform .22s cubic-bezier(.2, .75, .25, 1), border-color .22s ease, box-shadow .22s ease;
    }

    body.election-index-page .election-card:nth-child(2) { animation-delay: 55ms; }
    body.election-index-page .election-card:nth-child(3) { animation-delay: 110ms; }

    body.election-index-page .election-card:hover {
        transform: translateY(-5px);
        border-color: color-mix(in srgb, var(--brand), transparent 55%);
        box-shadow: 0 10px 20px rgba(15, 27, 51, .06), 0 24px 44px rgba(37, 99, 235, .12);
    }

    body.election-index-page .election-card img {
        width: 100%;
        height: auto;
        aspect-ratio: 16 / 8;
        display: block;
        flex: none;
        border-radius: 0;
        background: var(--tint);
        object-fit: cover;
        object-position: center top;
        transition: transform .45s cubic-bezier(.2, .75, .25, 1), filter .25s ease;
    }

    body.election-index-page .election-card:hover img {
        transform: scale(1.035);
        filter: saturate(1.08);
    }

    body.election-index-page .election-card h3 {
        margin: 0;
        padding: 8px 22px 6px;
        font-size: clamp(1.08rem, 1.7vw, 1.25rem);
        line-height: 1.3;
        letter-spacing: -.02em;
        text-wrap: balance;
    }

    body.election-index-page .election-card-label {
        align-self: flex-start;
        margin: 18px 22px 0;
        padding: 5px 10px;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 82%);
        border-radius: 999px;
        background: var(--tint);
        color: var(--brand);
        font-size: .73rem;
        font-weight: 800;
        letter-spacing: .025em;
        line-height: 1.4;
    }

    body.election-index-page .election-card p {
        margin: 0;
        padding: 0 22px 22px;
        color: var(--mut);
        font-size: .96rem;
        line-height: 1.65;
    }

    body.election-index-page .election-history-card {
        margin-top: clamp(38px, 5vw, 58px);
        padding: clamp(20px, 3vw, 30px);
        border-radius: 22px;
        background: linear-gradient(145deg, var(--card), color-mix(in srgb, var(--tint), var(--card) 58%));
    }

    body.election-index-page .election-history-card h2 {
        margin-bottom: 18px;
    }

    body.election-index-page .registration-history {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 10px;
    }

    body.election-index-page .registration-history li {
        min-width: 0;
        display: grid;
        justify-content: stretch;
        gap: 2px;
        padding: 13px 15px;
        border: 1px solid var(--line);
        border-radius: 14px;
        background: var(--card);
        transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    body.election-index-page .registration-history li:hover {
        transform: translateY(-2px);
        border-color: color-mix(in srgb, var(--brand), transparent 70%);
        box-shadow: 0 7px 16px rgba(15, 27, 51, .06);
    }

    body.election-index-page .registration-history li strong {
        font-size: .98rem;
    }

    body.election-index-page .registration-history li span {
        color: var(--mut);
        font-size: .88rem;
        overflow-wrap: anywhere;
    }

    @keyframes election-card-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (min-width: 640px) {
        body.election-index-page .election-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        body.election-index-page .registration-history {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 960px) {
        body.election-index-page .election-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        body.election-index-page .registration-history {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (max-width: 480px) {
        body.election-index-page .page-content {
            width: min(100% - 32px, 420px);
            padding-block: 32px 44px;
        }

        body.election-index-page .section-heading {
            gap: 10px;
        }

        body.election-index-page .carousel-controls .icon-button {
            width: 40px;
            height: 40px;
        }

        body.election-index-page .election-card img {
            aspect-ratio: 16 / 9;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        body.election-index-page .election-card {
            animation: none;
            transition: none;
        }

        body.election-index-page .election-card img {
            transition: none;
        }

        body.election-index-page .carousel-controls .icon-button,
        body.election-index-page .registration-history li {
            transition: none;
        }
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <header class="page-intro">
        <h1>Elections</h1>
        <p>Explore the different elections happening at our university.</p>
    </header>

    <section class="election-list-section">
        <div class="section-heading">
            <div><h2>Current elections</h2></div>
            <div class="carousel-controls" aria-label="Current election carousel controls">
                <button class="icon-button" type="button" id="prev-btn-current" aria-label="Previous current elections"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
                <button class="icon-button" type="button" id="next-btn-current" aria-label="Next current elections"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
            </div>
        </div>
        <div class="surface-grid election-grid" id="current-elections" aria-live="polite"></div>
    </section>

    <section class="election-list-section">
        <div class="section-heading">
            <div><h2>Previous elections</h2></div>
            <div class="carousel-controls" aria-label="Previous election carousel controls">
                <button class="icon-button" type="button" id="prev-btn-previous" aria-label="Earlier previous elections"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
                <button class="icon-button" type="button" id="next-btn-previous" aria-label="Later previous elections"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
            </div>
        </div>
        <div class="surface-grid election-grid" id="previous-elections" aria-live="polite"></div>
    </section>

    <section class="surface-card election-history-card">
        <h2>Registration history</h2>
        <ul class="surface-list registration-history">
            @foreach([
                ['Election 2023', 'Jan 15, 2023'],
                ['Election 2022', 'Jan 15, 2022'],
                ['Election 2021', 'Jan 15, 2021'],
                ['Election 2020', 'Jan 15, 2020'],
                ['Election 2019', 'Jan 15, 2019'],
                ['Election 2018', 'Jan 15, 2018'],
                ['Election 2017', 'Jan 15, 2017'],
                ['Election 2016', 'Jan 15, 2016'],
            ] as [$electionName, $registrationDate])
                <li><strong>{{ $electionName }}</strong><span>Registered on {{ $registrationDate }}</span></li>
            @endforeach
        </ul>
    </section>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const currentElections = [
            { title: 'Election 2025', description: 'Vote for various positions including President, Vice President, Secretary, Treasurer, and Class Representatives.' },
            { title: 'Presidential Election 2026', description: 'Vote for the next student body president. Learn more about the candidates and their platforms.' },
            { title: 'Vice Presidential Election 2026', description: 'Vote for the next student body vice president. Learn more about the candidates and their platforms.' },
            { title: 'Secretary Election 2026', description: 'Vote for the next student body secretary. Learn more about the candidates and their platforms.' },
            { title: 'Treasurer Election 2026', description: 'Vote for the next student body treasurer. Learn more about the candidates and their platforms.' },
            { title: 'Class Representative Election 2026', description: 'Vote for the next class representatives. Learn more about the candidates and their platforms.' }
        ];
        const previousElections = [
            { title: 'Election 2024', description: 'Review the results and highlights from the 2024 student government elections.' },
            { title: 'Election 2023', description: 'Review the results and highlights from the 2023 student government elections.' },
            { title: 'Election 2022', description: 'Review the results and highlights from the 2022 student government elections.' },
            { title: 'Election 2021', description: 'Review the results and highlights from the 2021 student government elections.' },
            { title: 'Election 2020', description: 'Review the results and highlights from the 2020 student government elections.' },
            { title: 'Election 2019', description: 'Review the results and highlights from the 2019 student government elections.' }
        ];

        let currentIndex = 0;
        let previousIndex = 0;
        const campusImage = @json(asset('images/background1.jpg'));
        const perPage = () => window.matchMedia('(min-width: 960px)').matches ? 3 : (window.matchMedia('(min-width: 640px)').matches ? 2 : 1);

        function render(containerId, items, startIndex) {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.replaceChildren();
            const count = Math.min(perPage(), items.length);
            const controls = document.getElementById(containerId === 'current-elections' ? 'prev-btn-current' : 'prev-btn-previous')?.parentElement;
            if (controls) controls.hidden = items.length <= count;
            for (let offset = 0; offset < count; offset++) {
                const item = items[(startIndex + offset) % items.length];
                const card = document.createElement('article');
                card.className = 'surface-card election-card';
                const image = document.createElement('img');
                image.src = campusImage;
                image.alt = 'PCU campus';
                image.loading = 'lazy';
                image.decoding = 'async';
                const label = document.createElement('span');
                label.className = 'election-card-label';
                label.textContent = containerId === 'current-elections' ? 'Current election' : 'Past election';
                const title = document.createElement('h3');
                title.textContent = item.title;
                const description = document.createElement('p');
                description.textContent = item.description;
                card.append(image, label, title, description);
                container.append(card);
            }
        }

        function renderCurrent() { render('current-elections', currentElections, currentIndex); }
        function renderPrevious() { render('previous-elections', previousElections, previousIndex); }
        document.getElementById('next-btn-current')?.addEventListener('click', () => {
            currentIndex = (currentIndex + perPage()) % currentElections.length;
            renderCurrent();
        });
        document.getElementById('prev-btn-current')?.addEventListener('click', () => {
            currentIndex = (currentIndex - perPage() + currentElections.length) % currentElections.length;
            renderCurrent();
        });
        document.getElementById('next-btn-previous')?.addEventListener('click', () => {
            previousIndex = (previousIndex + perPage()) % previousElections.length;
            renderPrevious();
        });
        document.getElementById('prev-btn-previous')?.addEventListener('click', () => {
            previousIndex = (previousIndex - perPage() + previousElections.length) % previousElections.length;
            renderPrevious();
        });
        let lastPerPage = perPage();
        window.addEventListener('resize', () => {
            const nextPerPage = perPage();
            if (nextPerPage === lastPerPage) return;
            lastPerPage = nextPerPage;
            currentIndex = Math.floor(currentIndex / nextPerPage) * nextPerPage;
            previousIndex = Math.floor(previousIndex / nextPerPage) * nextPerPage;
            renderCurrent();
            renderPrevious();
        });
        renderCurrent();
        renderPrevious();
    })();
</script>
@endpush

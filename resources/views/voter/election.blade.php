@extends('layouts.voter')

@section('title', 'Elections')

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

    <section class="surface-card">
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
            { title: 'Election 2025', description: 'Vote for various positions including President, Vice President, Secretary, Treasurer, and Class Representatives.', imgSrc: 'https://placehold.co/600x400?text=Election+2025', altText: 'Image representing the Election 2025' },
            { title: 'Presidential Election 2026', description: 'Vote for the next student body president. Learn more about the candidates and their platforms.', imgSrc: 'https://placehold.co/600x400?text=Presidential+Election+2026', altText: 'Image representing the Presidential Election 2026' },
            { title: 'Vice Presidential Election 2026', description: 'Vote for the next student body vice president. Learn more about the candidates and their platforms.', imgSrc: 'https://placehold.co/600x400?text=Vice+Presidential+Election+2026', altText: 'Image representing the Vice Presidential Election 2026' },
            { title: 'Secretary Election 2026', description: 'Vote for the next student body secretary. Learn more about the candidates and their platforms.', imgSrc: 'https://placehold.co/600x400?text=Secretary+Election+2026', altText: 'Image representing the Secretary Election 2026' },
            { title: 'Treasurer Election 2026', description: 'Vote for the next student body treasurer. Learn more about the candidates and their platforms.', imgSrc: 'https://placehold.co/600x400?text=Treasurer+Election+2026', altText: 'Image representing the Treasurer Election 2026' },
            { title: 'Class Representative Election 2026', description: 'Vote for the next class representatives. Learn more about the candidates and their platforms.', imgSrc: 'https://placehold.co/600x400?text=Class+Representative+Election+2026', altText: 'Image representing the Class Representative Election 2026' }
        ];
        const previousElections = [
            { title: 'Election 2024', description: 'Review the results and highlights from the 2024 student government elections.', imgSrc: 'https://placehold.co/600x400?text=Election+2024', altText: 'Image representing the 2024 Election' },
            { title: 'Election 2023', description: 'Review the results and highlights from the 2023 student government elections.', imgSrc: 'https://placehold.co/600x400?text=Election+2023', altText: 'Image representing the 2023 Election' },
            { title: 'Election 2022', description: 'Review the results and highlights from the 2022 student government elections.', imgSrc: 'https://placehold.co/600x400?text=Election+2022', altText: 'Image representing the 2022 Election' },
            { title: 'Election 2021', description: 'Review the results and highlights from the 2021 student government elections.', imgSrc: 'https://placehold.co/600x400?text=Election+2021', altText: 'Image representing the 2021 Election' },
            { title: 'Election 2020', description: 'Review the results and highlights from the 2020 student government elections.', imgSrc: 'https://placehold.co/600x400?text=Election+2020', altText: 'Image representing the 2020 Election' },
            { title: 'Election 2019', description: 'Review the results and highlights from the 2019 student government elections.', imgSrc: 'https://placehold.co/600x400?text=Election+2019', altText: 'Image representing the 2019 Election' }
        ];

        let currentIndex = 0;
        let previousIndex = 0;
        const perPage = () => window.matchMedia('(min-width: 900px)').matches ? 3 : (window.matchMedia('(min-width: 640px)').matches ? 2 : 1);

        function render(containerId, items, startIndex) {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.replaceChildren();
            const count = Math.min(perPage(), items.length);
            for (let offset = 0; offset < count; offset++) {
                const item = items[(startIndex + offset) % items.length];
                const card = document.createElement('article');
                card.className = 'surface-card election-card';
                const image = document.createElement('img');
                image.src = item.imgSrc;
                image.alt = item.altText;
                image.loading = 'lazy';
                const title = document.createElement('h3');
                title.textContent = item.title;
                const description = document.createElement('p');
                description.textContent = item.description;
                card.append(image, title, description);
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
        window.addEventListener('resize', () => { renderCurrent(); renderPrevious(); });
        renderCurrent();
        renderPrevious();
    })();
</script>
@endpush

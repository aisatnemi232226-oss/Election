@extends('layouts.voter')

@section('title', 'PCU Online Election System')

@section('content')
@php
    $homeUser = Auth::user();
    $homeIsAdmin = $homeUser && $homeUser->is_admin;
    $homeIsVerified = $homeUser && !$homeIsAdmin && $homeUser->isVerifiedVoter();
    $homeVerificationPending = $homeUser && $homeUser->voter_verification_submitted_at !== null;
    $homeElection = $elections->first();
    $eventsLabel = $eventSettings?->label ?: 'Election events';
    $labelImageRelative = $eventSettings?->label_image;
    $labelImageExists = $labelImageRelative
        ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $labelImageRelative)
        : false;
    $labelImageUrl = $labelImageExists
        ? route('storage.image', ['folder' => 'upload', 'filename' => $labelImageRelative])
        : null;

    $events = [];
    for ($i = 1; $i <= 3; $i++) {
        $title = $eventSettings?->{"event_{$i}_title"};
        $date = $eventSettings?->{"event_{$i}_date"};
        $description = $eventSettings?->{"event_{$i}_description"};
        $relativeImage = $eventSettings?->{"event_{$i}_image"};
        $hasImage = $relativeImage
            ? \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $relativeImage)
            : false;
        $image = $hasImage
            ? route('storage.image', ['folder' => 'upload', 'filename' => $relativeImage])
            : null;

        if ($title || $date || $description || $image) {
            $events[] = compact('title', 'date', 'description', 'image');
        }
    }

    $eventStatus = $eventSettings?->status;
    $eventStatusLabel = match ($eventStatus) {
        'ongoing' => 'Election ongoing',
        'upcoming' => 'Election upcoming',
        'completed' => 'Election completed',
        default => 'Election information',
    };
    $eventStatusClass = match ($eventStatus) {
        'ongoing' => 'status-open',
        'completed' => 'status-ended',
        default => '',
    };

    $formatElectionDate = static fn ($date) => $date
        ? \Illuminate\Support\Carbon::parse($date)->format('F j, Y')
        : null;
    $dateRows = [];
    if ($homeElection) {
        $registrationStart = $formatElectionDate($homeElection->reg_start);
        $registrationEnd = $formatElectionDate($homeElection->reg_end);
        $campaignStart = $formatElectionDate($homeElection->campaign_start);
        $campaignEnd = $formatElectionDate($homeElection->campaign_end);
        $votingStart = $formatElectionDate($homeElection->start_date);
        $votingEnd = $formatElectionDate($homeElection->end_date);
        $resultsDate = $formatElectionDate($homeElection->result_date);

        if ($registrationStart || $registrationEnd) {
            $dateRows[] = ['Registration period', implode(' – ', array_filter([$registrationStart, $registrationEnd])) , 'pcu-grad'];
        }
        if ($campaignStart || $campaignEnd) {
            $dateRows[] = ['Campaign period', implode(' – ', array_filter([$campaignStart, $campaignEnd])) , 'pcu-cal'];
        }
        if ($votingStart || $votingEnd) {
            $dateRows[] = ['Voting period', implode(' – ', array_filter([$votingStart, $votingEnd])) , 'pcu-vote'];
        }
        if ($resultsDate) {
            $dateRows[] = ['Results announcement', $resultsDate, 'pcu-chart'];
        }
    }

    $currentStep = !Auth::check() ? 1 : ($homeIsVerified || $homeIsAdmin ? 3 : 2);
    $stepDescription = !Auth::check()
        ? 'Log in with your student account to get started.'
        : ($homeIsVerified ? 'Your account is verified. You can continue to the ballot.' : ($homeIsAdmin ? 'Review the election and its results.' : 'Verify your account before casting a vote.'));
    $heroLead = !Auth::check()
        ? 'Sign in with your student account to follow the election and cast your vote.'
        : ($homeIsVerified ? 'Your account is verified. Choose your candidates and submit your ballot.' : ($homeIsAdmin ? 'Review current election information and student voting activity.' : 'Your account needs to be verified before you can vote.'));

    $articleQuery = \App\Models\ElectionArticle::query()->with(['articles']);
    if (\Illuminate\Support\Facades\Schema::hasColumn('election_articles', 'show_status')) {
        $articleQuery->where('show_status', 1);
    }
    $latestArticles = $articleQuery
        ->orderByDesc('created_at')
        ->get()
        ->map(fn ($electionArticle) => $electionArticle->articles)
        ->filter();
@endphp

<section class="hero">
    <div class="page-wrap hero-inner">
        <div class="hero-copy">
            <span class="hero-status {{ $eventStatusClass }}">{{ $eventStatusLabel }}</span>
            <h1>Vote for your student leaders</h1>
            <p class="hero-lead">{{ $heroLead }}</p>
            <div class="button-row">
                @guest
                    <a class="button button-primary" href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}">
                        Log in to vote <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
                    </a>
                @else
                    @if($homeIsVerified || $homeIsAdmin)
                        <a class="button button-primary" href="{{ route('election.details') }}">
                            Vote now <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
                        </a>
                    @else
                        <a class="button button-primary" href="{{ route('register.verify') }}">
                            {{ $homeVerificationPending ? 'View application' : 'Verify my account' }}
                            <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
                        </a>
                    @endif
                @endguest
                <a class="button button-secondary" href="#how-it-works">How it works</a>
            </div>
        </div>

        <aside class="steps-card" aria-labelledby="steps-title">
            <h2 id="steps-title">Your steps to vote</h2>
            <p>{{ $stepDescription }}</p>
            <ol class="steps-list">
                @php
                    $steps = [
                        ['Log in', 'Use your student account'],
                        ['Get verified', 'Submit your student details'],
                        ['Cast your vote', 'Choose your candidates'],
                        ['See the results', 'Follow the published results'],
                    ];
                @endphp
                @foreach($steps as $index => [$stepTitle, $stepHint])
                    @php
                        $stepNumber = $index + 1;
                        $stepState = $stepNumber < $currentStep ? 'is-done' : ($stepNumber === $currentStep ? 'is-current' : '');
                    @endphp
                    <li class="step-item {{ $stepState }}">
                        <span class="step-number">
                            @if($stepState === 'is-done')
                                <svg class="icon" aria-hidden="true"><use href="#pcu-ok"/></svg>
                            @else
                                {{ $stepNumber }}
                            @endif
                        </span>
                        <span class="step-copy"><strong>{{ $stepTitle }}</strong><span>{{ $stepHint }}</span></span>
                        @if($stepState)
                            <span class="step-tag">{{ $stepState === 'is-done' ? 'Done' : 'Next step' }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </aside>
    </div>
</section>

<section class="content-section" id="how-it-works">
    <div class="page-wrap">
        <div class="section-heading">
            <div><h2>What would you like to do?</h2><p>Pick a next step to continue.</p></div>
        </div>
        <div class="action-grid">
            <a class="action-card" href="{{ route('register.options') }}">
                <span class="action-icon"><svg class="icon" aria-hidden="true"><use href="#pcu-grad"/></svg></span>
                <h3>Register</h3>
                <p>Choose voter or candidate registration.</p>
                <span class="action-link">Start registration <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg></span>
            </a>
            <a class="action-card" href="{{ route('election.details') }}">
                <span class="action-icon"><svg class="icon" aria-hidden="true"><use href="#pcu-vote"/></svg></span>
                <h3>Vote</h3>
                <p>Choose your candidates and submit your ballot.</p>
                <span class="action-link">Open election <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg></span>
            </a>
            <a class="action-card" href="{{ url('/result') }}">
                <span class="action-icon"><svg class="icon" aria-hidden="true"><use href="#pcu-chart"/></svg></span>
                <h3>Results</h3>
                <p>Review election results when they are published.</p>
                <span class="action-link">View results <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg></span>
            </a>
        </div>
    </div>
</section>

<section class="content-section" id="dates">
    <div class="page-wrap">
        <div class="section-heading">
            <div>
                <h2>Key dates</h2>
                @if($homeElection)
                    <p>{{ $homeElection->name }}</p>
                @else
                    <p>Election schedule</p>
                @endif
            </div>
        </div>
        @if(count($dateRows))
            <div class="date-grid">
                @foreach($dateRows as [$dateTitle, $dateValue, $dateIcon])
                    <article class="date-card">
                        <svg class="icon" aria-hidden="true"><use href="#{{ $dateIcon }}"/></svg>
                        <div><strong>{{ $dateTitle }}</strong><span>{{ $dateValue }}</span></div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="empty-card">Election dates have not been published yet.</div>
        @endif
    </div>
</section>

<section class="content-section" id="events">
    <div class="page-wrap">
        <div class="section-heading">
            <div><h2>News and events</h2><p>{{ $eventsLabel }}</p></div>
            @if(count($events) > 1)
                <div class="carousel-controls" id="event-carousel-controls" aria-label="Election events carousel controls">
                    <button class="icon-button" id="prev-btn" type="button" aria-label="Previous election event"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
                    <button class="icon-button" id="next-btn" type="button" aria-label="Next election event"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
                </div>
            @endif
        </div>

        @if($labelImageUrl || $eventsLabel || $eventSettings?->start_date || $eventSettings?->end_date)
            <div class="election-banner">
                @if($labelImageUrl)
                    <img src="{{ $labelImageUrl }}" alt="{{ $eventsLabel }}">
                @endif
                <div class="election-banner-copy">
                    <span>Election details</span>
                    <strong>{{ $eventsLabel }}</strong>
                    @if($eventSettings?->start_date || $eventSettings?->end_date)
                        <p>
                            {{ $eventSettings?->start_date ? \Illuminate\Support\Carbon::parse($eventSettings->start_date)->format('F j, Y') : '' }}
                            @if($eventSettings?->start_date && $eventSettings?->end_date) – @endif
                            {{ $eventSettings?->end_date ? \Illuminate\Support\Carbon::parse($eventSettings->end_date)->format('F j, Y') : '' }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        @if(count($events))
            <div class="event-grid" id="events-container">
                @foreach($events as $index => $event)
                    <button type="button" class="event-card" data-event-card data-event-title="{{ $event['title'] }}" data-event-date="{{ $event['date'] }}" data-event-image="{{ $event['image'] }}" data-event-description="{{ $event['description'] }}">
                        @if($event['image'])
                            <img class="event-image" src="{{ $event['image'] }}" alt="{{ $event['title'] }}">
                        @else
                            <span class="event-placeholder" aria-hidden="true"><svg class="icon"><use href="#pcu-cal"/></svg></span>
                        @endif
                        <span class="event-card-copy">
                            @if($event['title'])<span class="event-title">{{ $event['title'] }}</span>@endif
                            @if($event['date'])<time>{{ $event['date'] }}</time>@endif
                            @if($event['description'])<span class="event-description">{{ $event['description'] }}</span>@endif
                        </span>
                    </button>
                @endforeach
            </div>
        @else
            <div class="empty-card">Election announcements and event details will appear here when published.</div>
        @endif
    </div>
</section>

<section class="content-section" id="articles">
    <div class="page-wrap">
        <div class="section-heading">
            <div><h2>Latest articles</h2><p>Election updates and announcements.</p></div>
            @if($latestArticles->count() > 1)
                <div class="carousel-controls" id="article-carousel-controls" aria-label="Article carousel controls">
                    <button class="icon-button" id="latest-articles-prev" type="button" aria-label="Previous article"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
                    <button class="icon-button" id="latest-articles-next" type="button" aria-label="Next article"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
                </div>
            @endif
        </div>
        @if($latestArticles->isEmpty())
            <div class="empty-card">No articles found.</div>
        @else
            <div class="article-grid" id="latest-articles-container">
                @foreach($latestArticles as $article)
                    <article class="article-card">
                        <button type="button" class="article-open" data-article-card data-article-id="{{ $article->id }}">
                            <img class="article-image" src="{{ $article->featured_image_url ?? asset('images/logo.png') }}" alt="{{ $article->title }}" onerror="this.onerror=null;this.src='{{ asset('images/logo.png') }}'">
                            <span class="article-copy">
                                <span class="article-title">{{ $article->title }}</span>
                                <time>{{ optional($article->created_at)->format('F j, Y') }}</time>
                                <span class="article-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($article->content), 160) }}</span>
                                <span class="article-read-more">Read more <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg></span>
                            </span>
                        </button>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="content-section" id="faq">
    <div class="page-wrap">
        <div class="section-heading"><div><h2>Common questions</h2></div></div>
        <details class="faq-item">
            <summary>Who can vote?<svg class="icon" aria-hidden="true"><use href="#pcu-chev"/></svg></summary>
            <p class="faq-answer">Eligible, verified students can access the ballot using their student account.</p>
        </details>
        <details class="faq-item">
            <summary>How do I get verified?<svg class="icon" aria-hidden="true"><use href="#pcu-chev"/></svg></summary>
            <p class="faq-answer">Open your account menu, choose the verification option, and submit your student details. You can return to view an application that is awaiting review.</p>
        </details>
        <details class="faq-item">
            <summary>Can I change my vote?<svg class="icon" aria-hidden="true"><use href="#pcu-chev"/></svg></summary>
            <p class="faq-answer">Review your selections before submitting your ballot. Contact the election office if you need assistance.</p>
        </details>
        <details class="faq-item">
            <summary>Where can I see election results?<svg class="icon" aria-hidden="true"><use href="#pcu-chev"/></svg></summary>
            <p class="faq-answer">Published results are available from the Results link in the navigation.</p>
        </details>
    </div>
</section>

<section class="content-section" id="guidelines">
    <div class="page-wrap">
        <div class="section-heading"><div><h2>Election guidelines</h2></div></div>
        <div class="guidelines-card">
            <p>Follow the university's election guidelines and review the rules before participating.</p>
            <ul>
                <li>Only eligible students may cast a ballot.</li>
                <li>Each student may vote only once.</li>
                <li>Do not attempt to interfere with election processes.</li>
                <li>Respect candidates and their campaigns.</li>
                <li>Follow the university's code of conduct during the election period.</li>
            </ul>
        </div>
    </div>
</section>

@include('layouts.partials.help-button')

<div class="overlay-dialog" id="article-modal" role="dialog" aria-modal="true" aria-labelledby="article-modal-title" tabindex="-1" hidden>
    <div class="dialog-panel">
        <div class="dialog-head">
            <h2 id="article-modal-title"></h2>
            <button class="icon-button" type="button" data-close-article aria-label="Close article"><svg class="icon" aria-hidden="true"><use href="#pcu-x"/></svg></button>
        </div>
        <div class="dialog-body">
            <div class="dialog-meta"><span id="article-modal-author"></span><span id="article-modal-date"></span></div>
            <img id="article-modal-image" alt="Article image" hidden>
            <div class="dialog-article-content" id="article-modal-content"></div>
        </div>
    </div>
</div>

<div class="overlay-dialog" id="event-modal" role="dialog" aria-modal="true" aria-labelledby="event-modal-title" tabindex="-1" hidden>
    <div class="dialog-panel">
        <div class="dialog-head">
            <h2 id="event-modal-title"></h2>
            <button class="icon-button" type="button" data-close-event aria-label="Close event details"><svg class="icon" aria-hidden="true"><use href="#pcu-x"/></svg></button>
        </div>
        <div class="dialog-body">
            <div class="dialog-meta" id="event-modal-date"></div>
            <img id="event-modal-image" alt="Event image" hidden>
            <div id="event-modal-content"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (() => {
        const eventContainer = document.getElementById('events-container');
        const articleContainer = document.getElementById('latest-articles-container');
        const eventControls = document.getElementById('event-carousel-controls');
        const articleControls = document.getElementById('article-carousel-controls');
        const articleModal = document.getElementById('article-modal');
        const eventModal = document.getElementById('event-modal');
        let eventIndex = 0;
        let articleIndex = 0;
        let previouslyFocused = null;

        function visibleCount(container) {
            if (!container || window.matchMedia('(min-width: 900px)').matches) return 3;
            if (window.matchMedia('(min-width: 640px)').matches) return 2;
            return 1;
        }

        function rotateCards(container, startIndex, direction) {
            if (!container) return startIndex;
            const cards = [...container.children];
            const count = visibleCount(container);
            const maxStart = Math.max(0, cards.length - count);
            if (maxStart === 0) {
                cards.forEach((card) => { card.hidden = false; });
                return 0;
            }
            const nextIndex = startIndex + direction * count;
            const boundedIndex = nextIndex < 0 ? maxStart : (nextIndex > maxStart ? 0 : nextIndex);
            cards.forEach((card, index) => {
                card.hidden = index < boundedIndex || index >= boundedIndex + count;
            });
            return boundedIndex;
        }

        function refreshCarousels() {
            if (eventControls) eventControls.hidden = (eventContainer?.children.length || 0) <= visibleCount(eventContainer);
            if (articleControls) articleControls.hidden = (articleContainer?.children.length || 0) <= visibleCount(articleContainer);
            eventIndex = rotateCards(eventContainer, eventIndex, 0);
            articleIndex = rotateCards(articleContainer, articleIndex, 0);
            if (eventContainer && eventContainer.children.length > visibleCount(eventContainer)) {
                [...eventContainer.children].forEach((card, index) => {
                    card.hidden = index < eventIndex || index >= eventIndex + visibleCount(eventContainer);
                });
            }
            if (articleContainer && articleContainer.children.length > visibleCount(articleContainer)) {
                [...articleContainer.children].forEach((card, index) => {
                    card.hidden = index < articleIndex || index >= articleIndex + visibleCount(articleContainer);
                });
            }
        }

        function setModalOpen(modal, open) {
            if (!modal) return;
            if (open) {
                previouslyFocused = document.activeElement;
                modal.hidden = false;
                modal.querySelector('button')?.focus();
            } else {
                modal.hidden = true;
                previouslyFocused?.focus();
            }
        }

        async function openArticle(card) {
            const articleId = card.dataset.articleId;
            if (!articleId) return;
            setModalOpen(articleModal, true);
            document.getElementById('article-modal-title').textContent = 'Loading...';
            document.getElementById('article-modal-author').textContent = '';
            document.getElementById('article-modal-date').textContent = '';
            document.getElementById('article-modal-content').textContent = '';
            const image = document.getElementById('article-modal-image');
            image.hidden = true;
            image.removeAttribute('src');

            try {
                const response = await fetch(`/article/${encodeURIComponent(articleId)}/json`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (!response.ok) throw new Error(`Article request failed: ${response.status}`);
                const article = await response.json();
                document.getElementById('article-modal-title').textContent = article.title || '';
                document.getElementById('article-modal-author').textContent = article.author ? `By ${article.author}` : '';
                document.getElementById('article-modal-date').textContent = article.created_at ? new Date(article.created_at).toLocaleString() : '';
                document.getElementById('article-modal-content').innerHTML = article.content || '';

                const imageUrl = article.featured_image_url || article.featured_image;
                if (imageUrl) {
                    image.src = imageUrl;
                    image.hidden = false;
                    image.onerror = () => {
                        image.hidden = true;
                        image.removeAttribute('src');
                    };
                }
            } catch (error) {
                console.error(error);
                document.getElementById('article-modal-title').textContent = 'Unable to load article';
                document.getElementById('article-modal-content').textContent = 'Article details could not be loaded. Please try again.';
            }
        }

        function openEvent(card) {
            document.getElementById('event-modal-title').textContent = card.dataset.eventTitle || '';
            document.getElementById('event-modal-date').textContent = card.dataset.eventDate ? `Date: ${card.dataset.eventDate}` : '';
            document.getElementById('event-modal-content').textContent = card.dataset.eventDescription || '';
            const image = document.getElementById('event-modal-image');
            image.hidden = true;
            image.removeAttribute('src');
            if (card.dataset.eventImage) {
                image.src = card.dataset.eventImage;
                image.hidden = false;
                image.onerror = () => {
                    image.hidden = true;
                    image.removeAttribute('src');
                };
            }
            setModalOpen(eventModal, true);
        }

        document.getElementById('prev-btn')?.addEventListener('click', () => {
            eventIndex = rotateCards(eventContainer, eventIndex, -1);
        });
        document.getElementById('next-btn')?.addEventListener('click', () => {
            eventIndex = rotateCards(eventContainer, eventIndex, 1);
        });
        document.getElementById('latest-articles-prev')?.addEventListener('click', () => {
            articleIndex = rotateCards(articleContainer, articleIndex, -1);
        });
        document.getElementById('latest-articles-next')?.addEventListener('click', () => {
            articleIndex = rotateCards(articleContainer, articleIndex, 1);
        });
        window.addEventListener('resize', refreshCarousels);

        document.addEventListener('click', (event) => {
            const eventCard = event.target.closest('[data-event-card]');
            const articleCard = event.target.closest('[data-article-card]');
            if (eventCard) openEvent(eventCard);
            if (articleCard) openArticle(articleCard);
            if (event.target.matches('[data-close-article]')) setModalOpen(articleModal, false);
            if (event.target.matches('[data-close-event]')) setModalOpen(eventModal, false);
            if (event.target === articleModal) setModalOpen(articleModal, false);
            if (event.target === eventModal) setModalOpen(eventModal, false);
        });
        document.addEventListener('keydown', (event) => {
            const activeModal = articleModal && !articleModal.hidden ? articleModal : (eventModal && !eventModal.hidden ? eventModal : null);
            if (!activeModal) return;
            if (event.key === 'Escape') {
                setModalOpen(activeModal, false);
                return;
            }
            if (event.key === 'Tab') {
                const focusable = [...activeModal.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])')];
                if (!focusable.length) return;
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        });
        refreshCarousels();
    })();
</script>

@guest
<script>
    (() => {
        const chatbotToggle = document.getElementById('chatbot-toggle');
        const chatbotPanel = document.getElementById('chatbot-panel');
        const chatbotClose = document.getElementById('chatbot-close');
        const chatbotForm = document.getElementById('chatbot-form');
        const chatbotInput = document.getElementById('chatbot-input');
        const chatbotMessages = document.getElementById('chatbot-messages');
        const chatbotQuickReplies = document.getElementById('chatbot-quick-replies');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        if (!chatbotToggle || !chatbotPanel || !chatbotForm || !chatbotInput || !chatbotMessages || !chatbotQuickReplies) return;

        function appendMessage(role, text) {
            const bubble = document.createElement('div');
            bubble.className = `chat-message ${role === 'user' ? 'chat-message-user' : 'chat-message-bot'}`;
            bubble.textContent = text;
            chatbotMessages.append(bubble);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        function renderQuickReplies(replies) {
            chatbotQuickReplies.replaceChildren();
            (replies || []).forEach((reply) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'chat-quick';
                button.textContent = reply;
                button.addEventListener('click', () => {
                    chatbotInput.value = reply;
                    chatbotForm.requestSubmit();
                });
                chatbotQuickReplies.append(button);
            });
        }

        async function sendToBot(message) {
            const response = await fetch("{{ route('chatbot.respond') }}", {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf
                },
                credentials: 'same-origin',
                body: JSON.stringify({ message })
            });
            if (!response.ok) throw new Error(`Chatbot request failed: ${response.status}`);
            return response.json();
        }

        function isGibberish(text) {
            const trimmed = String(text || '').trim();
            if (trimmed.length < 2) return true;
            const compact = trimmed.replace(/\s+/g, '');
            if (/^[a-z]+$/i.test(compact) && compact.length >= 8) {
                const lower = compact.toLowerCase();
                if (new Set(lower).size <= 3 || /^(.{1,3})\1+$/.test(lower)) return true;
            }
            const meaningful = (trimmed.match(/[a-z0-9]/gi) || []).length;
            return trimmed.length >= 6 && meaningful <= 2;
        }

        let quickReplies = [
            'How do I log in?',
            'How do I vote?',
            'How do I register as candidate?',
            'Where can I see results?'
        ];

        async function openChatbot() {
            chatbotPanel.hidden = false;
            chatbotToggle.setAttribute('aria-expanded', 'true');
            if (!chatbotMessages.childElementCount) {
                try {
                    const data = await sendToBot('hi');
                    appendMessage('bot', data.reply || "Hi! I'm PCU Assistant.");
                    if (Array.isArray(data.quick_replies) && data.quick_replies.length) quickReplies = data.quick_replies;
                    renderQuickReplies(quickReplies);
                } catch (error) {
                    console.error(error);
                    appendMessage('bot', "Hi! I'm PCU Assistant. Ask how to log in, register as a candidate, vote, or view results.");
                    renderQuickReplies(quickReplies);
                }
            }
            chatbotInput.focus();
        }

        function closeChatbot() {
            chatbotPanel.hidden = true;
            chatbotToggle.setAttribute('aria-expanded', 'false');
        }

        chatbotToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            if (chatbotPanel.hidden) openChatbot();
            else closeChatbot();
        });
        chatbotClose?.addEventListener('click', closeChatbot);
        chatbotPanel.addEventListener('click', (event) => event.stopPropagation());
        chatbotForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const message = chatbotInput.value.trim();
            if (!message) return;
            if (isGibberish(message)) {
                chatbotInput.value = '';
                appendMessage('bot', "I couldn't understand that. Please try a clear question or choose one of these:");
                renderQuickReplies(quickReplies);
                return;
            }
            chatbotInput.value = '';
            appendMessage('user', message);
            try {
                const data = await sendToBot(message);
                appendMessage('bot', data.reply || 'Sorry, I could not respond.');
                renderQuickReplies(data.quick_replies || []);
            } catch (error) {
                console.error(error);
                appendMessage('bot', 'Sorry, something went wrong. Please try again.');
            }
        });
    })();
</script>
@endguest
@endpush

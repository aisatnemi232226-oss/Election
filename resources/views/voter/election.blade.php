@extends('layouts.voter')

@section('title', 'Elections')
@section('body-class', 'election-index-page')

@push('styles')
<style>
    body.election-index-page #main-content {
        --election-bg-start: #eaf2ff;
        --election-bg-mid: #d7e6ff;
        --election-bg-end: #f0f6ff;
        position: relative;
        isolation: isolate;
        overflow: hidden;
        background:
            radial-gradient(ellipse at 8% 14%, rgba(56, 111, 229, .2), transparent 38%),
            radial-gradient(ellipse at 100% 42%, rgba(83, 145, 255, .17), transparent 42%),
            linear-gradient(135deg, var(--election-bg-start), var(--election-bg-mid) 48%, var(--election-bg-end));
        color: #142746;
    }

    body.election-index-page #main-content::before,
    body.election-index-page #main-content::after {
        position: absolute;
        z-index: -1;
        pointer-events: none;
        content: "";
    }

    body.election-index-page #main-content::before {
        inset: 0;
        background-image: radial-gradient(rgba(40, 87, 158, .16) .8px, transparent .8px);
        background-size: 26px 26px;
        opacity: .34;
        mask-image: linear-gradient(125deg, #000, transparent 68%);
    }

    body.election-index-page #main-content::after {
        top: 80px;
        right: -210px;
        width: min(62vw, 760px);
        aspect-ratio: 1;
        border: 1px solid rgba(255, 255, 255, .52);
        border-radius: 50%;
        box-shadow: 0 0 0 42px rgba(255, 255, 255, .16), 0 0 0 84px rgba(255, 255, 255, .1);
        animation: election-background-orbit 32s ease-in-out infinite alternate;
    }

    body.election-index-page .page-content {
        position: relative;
        z-index: 1;
    }

    :root[data-theme="dark"] body.election-index-page #main-content {
        --election-bg-start: #17396b;
        --election-bg-mid: #24508c;
        --election-bg-end: #162f58;
        color: #f0f5ff;
    }

    :root[data-theme="dark"] body.election-index-page #main-content::before {
        background-image: radial-gradient(rgba(215, 230, 255, .18) .8px, transparent .8px);
        opacity: .23;
    }

    :root[data-theme="dark"] body.election-index-page #main-content::after {
        border-color: rgba(220, 234, 255, .2);
        box-shadow: 0 0 0 42px rgba(220, 234, 255, .055), 0 0 0 84px rgba(220, 234, 255, .035);
    }

    @keyframes election-background-orbit {
        from { transform: translate3d(0, 0, 0); }
        to { transform: translate3d(-18px, 14px, 0); }
    }

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

    body.election-index-page .election-hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(280px, .95fr);
        align-items: stretch;
        gap: clamp(24px, 5vw, 62px);
        min-height: 360px;
        margin-bottom: clamp(44px, 6vw, 72px);
        overflow: hidden;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 86%);
        border-radius: 28px;
        background:
            radial-gradient(ellipse at 9% 100%, color-mix(in srgb, var(--brand), transparent 91%), transparent 44%),
            linear-gradient(120deg, var(--card), color-mix(in srgb, var(--tint), var(--card) 50%));
        box-shadow: 0 16px 44px rgba(15, 27, 51, .08);
        animation: election-hero-in .7s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.election-index-page .election-hero::before {
        display: none;
        content: none;
    }

    body.election-index-page .election-hero-copy {
        align-self: center;
        padding: clamp(28px, 5vw, 60px) 0 clamp(28px, 5vw, 60px) clamp(26px, 5vw, 62px);
    }

    body.election-index-page .election-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 18px;
        color: var(--brand);
        font-size: .77rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    body.election-index-page .election-eyebrow .icon {
        width: 19px;
        height: 19px;
    }

    body.election-index-page .election-hero h1 {
        max-width: 11ch;
        font-size: clamp(2.65rem, 5.5vw, 4.65rem);
        line-height: .99;
        letter-spacing: -.065em;
        text-wrap: balance;
    }

    body.election-index-page .election-hero h1 span {
        color: var(--brand);
    }

    body.election-index-page .election-hero-copy > p {
        max-width: 44ch;
        margin-top: 18px;
        font-size: clamp(1rem, 1.6vw, 1.12rem);
        line-height: 1.7;
    }

    body.election-index-page .election-hero-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-top: 24px;
    }

    body.election-index-page .election-hero-actions .button {
        min-height: 46px;
        border-radius: 10px;
        transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    body.election-index-page .election-hero-actions .button:hover {
        transform: translateY(-2px);
    }

    body.election-index-page .election-hero-actions .button-primary:hover {
        box-shadow: 0 8px 18px color-mix(in srgb, var(--brand), transparent 78%);
    }

    body.election-index-page .election-hero-actions .button .icon {
        width: 18px;
        transition: transform .2s ease;
    }

    body.election-index-page .election-hero-actions .button-primary:hover .icon {
        transform: translateX(3px);
    }

    body.election-index-page .election-hero-visual {
        position: relative;
        min-height: 300px;
        align-self: stretch;
        overflow: hidden;
        margin: 12px;
        border-radius: 20px;
        background: var(--brand-d);
        isolation: isolate;
        animation: election-visual-in .85s .12s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.election-index-page .election-hero-visual::after {
        position: absolute;
        z-index: 0;
        inset: 0;
        background: linear-gradient(180deg, rgba(8, 22, 49, .03) 30%, rgba(8, 22, 49, .72) 100%);
        content: "";
    }

    body.election-index-page .election-hero-visual img {
        width: 100%;
        height: 100%;
        min-height: 300px;
        display: block;
        object-fit: cover;
        object-position: center;
        transition: transform .8s cubic-bezier(.2, .75, .25, 1);
    }

    body.election-index-page .election-hero-visual:hover img {
        transform: scale(1.045);
    }

    body.election-index-page .election-hero-caption {
        position: absolute;
        z-index: 1;
        right: clamp(18px, 3vw, 34px);
        bottom: clamp(18px, 3vw, 32px);
        left: clamp(18px, 3vw, 34px);
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        color: #fff;
    }

    body.election-index-page .election-hero-caption-copy {
        max-width: 24ch;
    }

    body.election-index-page .election-hero-caption-copy span {
        display: block;
        margin-bottom: 6px;
        color: rgba(255, 255, 255, .82);
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    body.election-index-page .election-hero-caption-copy strong {
        display: block;
        font-size: clamp(1.3rem, 2.2vw, 1.85rem);
        line-height: 1.15;
        letter-spacing: -.035em;
        text-wrap: balance;
    }

    body.election-index-page .election-hero-mark {
        width: 54px;
        height: 54px;
        display: grid;
        place-items: center;
        flex: none;
        border: 1px solid rgba(255, 255, 255, .48);
        border-radius: 50%;
        background: rgba(255, 255, 255, .14);
        color: #fff;
        backdrop-filter: blur(8px);
    }

    body.election-index-page .election-hero-mark .icon {
        width: 25px;
        height: 25px;
    }

    body.election-index-page .election-list-section + .election-list-section,
    body.election-index-page .page-content > .election-list-section + .surface-card {
        margin-top: clamp(38px, 5vw, 58px);
    }

    body.election-index-page .section-heading {
        align-items: center;
        margin-bottom: 20px;
    }

    body.election-index-page .section-heading h2,
    body.election-index-page .election-history-card h2 {
        margin: 0;
        font-size: clamp(1.4rem, 2.5vw, 1.8rem);
        letter-spacing: -.03em;
    }

    body.election-index-page .section-heading-copy {
        min-width: 0;
    }

    body.election-index-page .section-eyebrow {
        display: block;
        margin-bottom: 4px;
        color: var(--brand);
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    body.election-index-page .section-heading p {
        margin: 5px 0 0;
        color: var(--mut);
        font-size: .93rem;
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
        position: relative;
        display: flex;
        min-width: 0;
        min-height: 100%;
        flex-direction: column;
        margin-top: 0;
        overflow: hidden;
        padding: 0;
        border-color: color-mix(in srgb, var(--line), transparent 8%);
        border-radius: 18px;
        box-shadow: 0 2px 6px rgba(15, 27, 51, .035), 0 12px 30px rgba(15, 27, 51, .055);
        animation: election-card-in .62s cubic-bezier(.2, .75, .25, 1) both;
        transition: transform .24s cubic-bezier(.2, .75, .25, 1), border-color .24s ease, box-shadow .24s ease;
    }

    body.election-index-page .election-card:nth-child(2) { animation-delay: 90ms; }
    body.election-index-page .election-card:nth-child(3) { animation-delay: 180ms; }

    body.election-index-page .election-card:hover {
        transform: translateY(-7px);
        border-color: color-mix(in srgb, var(--brand), transparent 55%);
        box-shadow: 0 12px 24px rgba(15, 27, 51, .075), 0 26px 48px color-mix(in srgb, var(--brand), transparent 86%);
    }

    body.election-index-page .election-card-media {
        position: relative;
        overflow: hidden;
        background: var(--tint);
        isolation: isolate;
    }

    body.election-index-page .election-card-media::after {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(8, 22, 49, .02) 40%, rgba(8, 22, 49, .52) 100%);
        content: "";
        pointer-events: none;
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
        transition: transform .65s cubic-bezier(.2, .75, .25, 1), filter .35s ease;
    }

    body.election-index-page .election-card:hover img {
        transform: scale(1.065);
        filter: saturate(1.1);
    }

    body.election-index-page .election-card h3 {
        margin: 0;
        padding: 20px 22px 7px;
        font-size: clamp(1.08rem, 1.7vw, 1.25rem);
        line-height: 1.3;
        letter-spacing: -.02em;
        text-wrap: balance;
    }

    body.election-index-page .election-card-label {
        position: absolute;
        z-index: 1;
        right: 14px;
        bottom: 14px;
        left: 14px;
        width: fit-content;
        max-width: calc(100% - 28px);
        padding: 6px 10px;
        border: 1px solid rgba(255, 255, 255, .32);
        border-radius: 999px;
        background: rgba(8, 22, 49, .7);
        color: #fff;
        font-size: .73rem;
        font-weight: 800;
        letter-spacing: .025em;
        line-height: 1.4;
        backdrop-filter: blur(8px);
    }

    body.election-index-page .election-card-label.is-current {
        background: color-mix(in srgb, var(--brand-d), transparent 12%);
    }

    body.election-index-page .election-card-label.is-past {
        background: rgba(8, 22, 49, .7);
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
        border-radius: 20px;
        background: linear-gradient(145deg, var(--card), color-mix(in srgb, var(--tint), var(--card) 64%));
    }

    body.election-index-page .election-history-card h2 {
        margin-bottom: 0;
    }

    body.election-index-page .election-history-heading {
        margin-bottom: 20px;
    }

    body.election-index-page .election-history-heading p {
        margin: 5px 0 0;
        color: var(--mut);
        font-size: .93rem;
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
        transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease, background-color .22s ease;
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
        from { opacity: 0; translate: 0 22px; scale: .985; }
        to { opacity: 1; translate: 0 0; scale: 1; }
    }

    @keyframes election-hero-in {
        from { opacity: 0; transform: translateY(16px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes election-visual-in {
        from { opacity: 0; transform: translateX(14px) scale(.985); }
        to { opacity: 1; transform: translateX(0) scale(1); }
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

    @media (max-width: 720px) {
        body.election-index-page .election-hero {
            grid-template-columns: minmax(0, 1fr);
            gap: 0;
        }

        body.election-index-page .election-hero-copy {
            padding: 34px 34px 24px;
        }

        body.election-index-page .election-hero-visual {
            min-height: 240px;
            margin: 0 12px 12px;
        }

        body.election-index-page .election-hero-visual img {
            min-height: 240px;
            max-height: 320px;
        }
    }

    @media (max-width: 480px) {
        body.election-index-page .page-content {
            width: min(100% - 32px, 420px);
            padding-block: 32px 44px;
        }

        body.election-index-page .election-hero {
            grid-template-columns: minmax(0, 1fr);
            gap: 0;
            border-radius: 22px;
        }

        body.election-index-page .election-hero-copy {
            padding: 28px 24px 24px;
        }

        body.election-index-page .election-eyebrow {
            margin-bottom: 13px;
            font-size: .68rem;
        }

        body.election-index-page .election-hero h1 {
            max-width: 10ch;
            font-size: clamp(2.55rem, 12vw, 3.45rem);
        }

        body.election-index-page .election-hero-copy > p {
            margin-top: 12px;
        }

        body.election-index-page .election-hero-actions {
            margin-top: 19px;
        }

        body.election-index-page .election-hero-visual {
            min-height: 190px;
            margin: 0 10px 10px;
            border-radius: 15px;
        }

        body.election-index-page .election-hero-visual img {
            min-height: 190px;
            max-height: 230px;
        }

        body.election-index-page .election-hero-mark {
            width: 44px;
            height: 44px;
        }

        body.election-index-page .section-heading {
            gap: 10px;
        }

        body.election-index-page .section-heading p {
            max-width: 34ch;
            font-size: .86rem;
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
        body.election-index-page #main-content::after {
            animation: none;
        }

        body.election-index-page .election-hero,
        body.election-index-page .election-hero-visual,
        body.election-index-page .election-card {
            animation: none;
            transition: none;
        }

        body.election-index-page .election-hero-visual img,
        body.election-index-page .election-card img {
            transition: none;
        }

        body.election-index-page .carousel-controls .icon-button,
        body.election-index-page .registration-history li,
        body.election-index-page .election-hero-actions .button {
            transition: none;
        }

        body.election-index-page .election-card:hover,
        body.election-index-page .registration-history li:hover,
        body.election-index-page .election-hero-actions .button:hover,
        body.election-index-page .election-hero-visual:hover img,
        body.election-index-page .election-card:hover img,
        body.election-index-page .carousel-controls .icon-button:hover {
            transform: none;
        }

        body.election-index-page .election-card {
            translate: none;
            scale: none;
        }
    }
</style>
@endpush

@section('content')
<div class="page-content">
    <header class="page-intro election-hero">
        <div class="election-hero-copy">
            <span class="election-eyebrow">
                <svg class="icon" aria-hidden="true"><use href="#pcu-grad"/></svg>
                Philippine Christian University
            </span>
            <h1>Make your voice <span>count.</span></h1>
            <p>Explore the elections shaping our student community. Get informed, meet the opportunities, and take part in the future of PCU.</p>
            <div class="election-hero-actions">
                <a class="button button-primary" href="#current-elections">
                    Explore elections
                    <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
                </a>
                <a class="button button-secondary" href="#previous-elections">Past elections</a>
            </div>
        </div>
        <div class="election-hero-visual">
            <img src="{{ asset('images/background1.jpg') }}" alt="" fetchpriority="high">
            <div class="election-hero-caption">
                <div class="election-hero-caption-copy">
                    <span>Student voices. Shared future.</span>
                    <strong>Your choice helps shape what comes next.</strong>
                </div>
                <span class="election-hero-mark">
                    <svg class="icon" aria-hidden="true"><use href="#pcu-vote"/></svg>
                </span>
            </div>
        </div>
    </header>

    <section class="election-list-section">
        <div class="section-heading">
            <div class="section-heading-copy">
                <span class="section-eyebrow">Take part</span>
                <h2>Current elections</h2>
                <p>Explore the elections happening in our university community.</p>
            </div>
            <div class="carousel-controls" aria-label="Current election carousel controls">
                <button class="icon-button" type="button" id="prev-btn-current" aria-label="Previous current elections"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
                <button class="icon-button" type="button" id="next-btn-current" aria-label="Next current elections"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
            </div>
        </div>
        <div class="surface-grid election-grid" id="current-elections" aria-live="polite"></div>
    </section>

    <section class="election-list-section">
        <div class="section-heading">
            <div class="section-heading-copy">
                <span class="section-eyebrow">Look back</span>
                <h2>Previous elections</h2>
                <p>Revisit past student elections and their milestones.</p>
            </div>
            <div class="carousel-controls" aria-label="Previous election carousel controls">
                <button class="icon-button" type="button" id="prev-btn-previous" aria-label="Earlier previous elections"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
                <button class="icon-button" type="button" id="next-btn-previous" aria-label="Later previous elections"><svg class="icon" aria-hidden="true" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
            </div>
        </div>
        <div class="surface-grid election-grid" id="previous-elections" aria-live="polite"></div>
    </section>

    <section class="surface-card election-history-card">
        <div class="election-history-heading">
            <span class="section-eyebrow">Your participation</span>
            <h2>Registration history</h2>
            <p>A record of elections you have registered for.</p>
        </div>
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
                const media = document.createElement('div');
                media.className = 'election-card-media';
                const image = document.createElement('img');
                image.src = campusImage;
                image.alt = 'PCU campus';
                image.loading = 'lazy';
                image.decoding = 'async';
                const label = document.createElement('span');
                label.className = `election-card-label ${containerId === 'current-elections' ? 'is-current' : 'is-past'}`;
                label.textContent = containerId === 'current-elections' ? 'Current election' : 'Past election';
                const title = document.createElement('h3');
                title.textContent = item.title;
                const description = document.createElement('p');
                description.textContent = item.description;
                media.append(image, label);
                card.append(media, title, description);
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

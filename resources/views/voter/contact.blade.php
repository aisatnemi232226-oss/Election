@extends('layouts.voter')

@section('title', 'Contact Us')
@section('body-class', 'contact-view')

@push('styles')
<style>
    body.contact-view #main-content {
        --contact-bg-start: #eaf2ff;
        --contact-bg-mid: #d7e6ff;
        --contact-bg-end: #f0f6ff;
        position: relative;
        isolation: isolate;
        overflow: hidden;
        background:
            radial-gradient(ellipse at 8% 12%, rgba(56, 111, 229, .2), transparent 38%),
            radial-gradient(ellipse at 100% 46%, rgba(83, 145, 255, .17), transparent 42%),
            linear-gradient(135deg, var(--contact-bg-start), var(--contact-bg-mid) 48%, var(--contact-bg-end));
    }

    body.contact-view #main-content::before,
    body.contact-view #main-content::after {
        position: absolute;
        z-index: 0;
        pointer-events: none;
        content: "";
    }

    body.contact-view #main-content::before {
        inset: 0;
        background-image: radial-gradient(rgba(40, 87, 158, .16) .8px, transparent .8px);
        background-size: 26px 26px;
        opacity: .34;
        mask-image: linear-gradient(125deg, #000, transparent 68%);
    }

    body.contact-view #main-content::after {
        top: 330px;
        right: -210px;
        width: min(62vw, 760px);
        aspect-ratio: 1;
        border: 1px solid rgba(255, 255, 255, .52);
        border-radius: 50%;
        box-shadow: 0 0 0 42px rgba(255, 255, 255, .16), 0 0 0 84px rgba(255, 255, 255, .1);
        animation: contact-background-orbit 32s ease-in-out infinite alternate;
    }

    body.contact-view .contact-page {
        position: relative;
        z-index: 1;
    }

    :root[data-theme="dark"] body.contact-view #main-content {
        --contact-bg-start: #17396b;
        --contact-bg-mid: #24508c;
        --contact-bg-end: #162f58;
    }

    :root[data-theme="dark"] body.contact-view #main-content::before {
        background-image: radial-gradient(rgba(215, 230, 255, .18) .8px, transparent .8px);
        opacity: .23;
    }

    :root[data-theme="dark"] body.contact-view #main-content::after {
        border-color: rgba(220, 234, 255, .2);
        box-shadow: 0 0 0 42px rgba(220, 234, 255, .055), 0 0 0 84px rgba(220, 234, 255, .035);
    }

    @keyframes contact-background-orbit {
        from { transform: translate3d(0, 0, 0); }
        to { transform: translate3d(-18px, 14px, 0); }
    }

    body.contact-view .contact-page {
        width: min(1240px, 100% - 2 * clamp(18px, 5vw, 48px));
        padding-block: clamp(28px, 5vw, 60px) clamp(48px, 7vw, 80px);
    }

    body.contact-view .contact-page > .page-intro {
        position: relative;
        isolation: isolate;
        display: flex;
        min-height: clamp(220px, 28vw, 330px);
        flex-direction: column;
        justify-content: flex-end;
        overflow: hidden;
        margin: 0 0 clamp(30px, 4vw, 42px);
        padding: clamp(26px, 5vw, 56px);
        border: 1px solid rgba(255, 255, 255, .16);
        border-radius: 26px;
        background:
            linear-gradient(100deg, rgba(5, 17, 42, .9), rgba(5, 20, 48, .65) 64%, rgba(5, 20, 48, .35)),
            linear-gradient(0deg, rgba(4, 14, 32, .45), transparent 78%),
            url("{{ asset('images/background1.jpg') }}") center 42% / cover no-repeat;
        box-shadow: 0 18px 42px rgba(15, 27, 51, .14);
        color: #f7faff;
        text-align: left;
        animation: contact-intro-in .55s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.contact-view .contact-page > .page-intro::after {
        position: absolute;
        z-index: -1;
        top: -190px;
        right: -85px;
        width: 410px;
        aspect-ratio: 1;
        border: 1px solid rgba(224, 237, 255, .27);
        border-radius: 50%;
        box-shadow: 0 0 0 32px rgba(224, 237, 255, .08), 0 0 0 64px rgba(224, 237, 255, .05);
        content: "";
        pointer-events: none;
    }

    body.contact-view .contact-hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        align-self: flex-start;
        margin-bottom: 9px;
        padding: 7px 10px;
        border: 1px solid rgba(226, 238, 255, .3);
        border-radius: 999px;
        background: rgba(239, 246, 255, .1);
        color: #e4eeff;
        font-size: .69rem;
        font-weight: 800;
        letter-spacing: .12em;
    }

    body.contact-view .contact-hero-kicker .icon {
        width: 15px;
        height: 15px;
    }

    body.contact-view .contact-page > .page-intro h1 {
        color: #fff;
        font-size: clamp(2rem, 5vw, 3.25rem);
        letter-spacing: -.045em;
        text-wrap: balance;
    }

    body.contact-view .contact-page > .page-intro p {
        max-width: 56ch;
        margin: 12px 0 0;
        color: rgba(239, 245, 255, .88);
        font-size: clamp(1rem, 1.8vw, 1.15rem);
    }

    body.contact-view .contact-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 22px;
    }

    body.contact-view .contact-hero-actions .button {
        min-height: 44px;
        border-radius: 10px;
        transition: transform .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    body.contact-view .contact-hero-actions .button:hover {
        transform: translateY(-2px);
    }

    body.contact-view .contact-hero-actions .button-secondary {
        border-color: rgba(255, 255, 255, .7);
        color: #fff;
    }

    body.contact-view .contact-hero-actions .button-secondary:hover {
        background: rgba(255, 255, 255, .12);
    }

    body.contact-view .contact-hero-actions .button .icon {
        width: 17px;
        height: 17px;
    }

    body.contact-view .contact-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.02fr) minmax(0, .98fr);
        align-items: stretch;
        gap: clamp(16px, 2.4vw, 26px);
    }

    body.contact-view .contact-layout > .surface-card {
        min-width: 0;
        margin: 0;
        padding: clamp(20px, 3vw, 30px);
        border-color: color-mix(in srgb, var(--line), transparent 8%);
        border-radius: 22px;
        background: linear-gradient(145deg, var(--card), color-mix(in srgb, var(--tint), var(--card) 62%));
        box-shadow: 0 5px 14px rgba(15, 27, 51, .045), 0 18px 38px rgba(15, 27, 51, .07);
        animation: contact-panel-in .58s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.contact-view .contact-location {
        animation-delay: 70ms;
    }

    body.contact-view .contact-layout > .contact-info {
        animation-delay: 150ms;
    }

    body.contact-view .contact-panel-heading {
        margin-bottom: 20px;
    }

    body.contact-view .contact-panel-kicker {
        margin: 0 0 5px;
        color: var(--brand);
        font-size: .73rem;
        font-weight: 800;
        letter-spacing: .13em;
    }

    body.contact-view .contact-panel-heading h2 {
        margin: 0;
        font-size: clamp(1.35rem, 2.5vw, 1.75rem);
        letter-spacing: -.03em;
    }

    body.contact-view .contact-panel-description {
        margin: 6px 0 0;
        color: var(--mut);
        font-size: .92rem;
    }

    body.contact-view .contact-panel-heading > p:last-child {
        margin: 8px 0 0;
        color: var(--mut);
        font-size: .95rem;
    }

    body.contact-view .contact-map-frame {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--line);
        border-radius: 18px;
        background: var(--tint);
        box-shadow: inset 0 1px 3px rgba(15, 27, 51, .05);
    }

    body.contact-view .contact-location iframe {
        display: block;
        width: 100%;
        height: auto;
        min-height: 300px;
        aspect-ratio: 4 / 3;
        border: 0;
        border-radius: 0;
        transition: filter .25s ease;
    }

    body.contact-view .contact-map-frame:focus-within {
        outline: 3px solid color-mix(in srgb, var(--brand), transparent 35%);
        outline-offset: 3px;
    }

    body.contact-view .contact-info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    body.contact-view .contact-info-item {
        display: flex;
        min-width: 0;
        min-height: 158px;
        flex-direction: column;
        align-items: flex-start;
        margin: 0;
        padding: clamp(15px, 2vw, 20px);
        border: 1px solid var(--line);
        border-radius: 16px;
        background: linear-gradient(145deg, var(--card), color-mix(in srgb, var(--tint), var(--card) 52%));
        box-shadow: 0 4px 12px rgba(15, 27, 51, .035);
        transition: transform .22s cubic-bezier(.2, .75, .25, 1), border-color .22s ease, box-shadow .22s ease, background-color .22s ease;
    }

    body.contact-view .contact-info-item:hover {
        transform: translateY(-4px);
        border-color: color-mix(in srgb, var(--brand), transparent 60%);
        box-shadow: 0 10px 22px rgba(15, 27, 51, .07), 0 16px 30px color-mix(in srgb, var(--brand), transparent 93%);
    }

    body.contact-view .contact-info-icon {
        display: grid;
        width: 42px;
        height: 42px;
        flex: none;
        place-items: center;
        margin-bottom: 13px;
        border: 1px solid color-mix(in srgb, var(--brand), transparent 86%);
        border-radius: 13px;
        background: var(--tint);
        color: var(--brand);
        transition: transform .22s ease, background-color .22s ease, color .22s ease;
    }

    body.contact-view .contact-info-item:hover .contact-info-icon {
        transform: translateY(-2px) rotate(-3deg);
        background: var(--brand);
        color: var(--brand-contrast);
    }

    body.contact-view .contact-info-icon svg {
        width: 21px;
        height: 21px;
        fill: none;
        stroke: currentColor;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-width: 1.8;
    }

    body.contact-view .contact-info-item h3 {
        margin: 0 0 5px;
        font-size: 1rem;
    }

    body.contact-view .contact-info-item p {
        margin: 0;
        color: var(--mut);
        font-size: .9rem;
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    body.contact-view .contact-info-item a {
        color: var(--brand);
        font-weight: 650;
        text-decoration-thickness: 1px;
        text-underline-offset: 3px;
    }

    body.contact-view .contact-info-item a:hover {
        text-decoration: underline;
    }

    body.contact-view .contact-info-item a:focus-visible {
        border-radius: 3px;
        outline-offset: 3px;
    }

    @keyframes contact-intro-in {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes contact-panel-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 820px) {
        body.contact-view .contact-layout {
            grid-template-columns: minmax(0, 1fr);
        }

        body.contact-view .contact-location iframe {
            min-height: 300px;
            aspect-ratio: 16 / 8;
        }
    }

    @media (max-width: 560px) {
        body.contact-view .contact-page {
            width: min(100% - 32px, 1240px);
            padding-block: 24px 48px;
        }

        body.contact-view .contact-page > .page-intro {
            min-height: 220px;
            border-radius: 20px;
            background-position: 56% center;
        }

        body.contact-view .contact-hero-actions {
            gap: 8px;
            margin-top: 18px;
        }

        body.contact-view .contact-hero-actions .button {
            min-height: 42px;
            padding-inline: 13px;
            font-size: .9rem;
        }

        body.contact-view .contact-layout > .surface-card {
            padding: 19px;
            border-radius: 18px;
        }

        body.contact-view .contact-info-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 10px;
        }

        body.contact-view .contact-info-item {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            align-items: center;
            column-gap: 13px;
            min-height: 0;
            padding: 14px;
        }

        body.contact-view .contact-info-icon {
            grid-row: span 2;
            margin: 0;
        }

        body.contact-view .contact-info-item h3 {
            align-self: end;
            margin-bottom: 2px;
        }

        body.contact-view .contact-info-item p {
            align-self: start;
        }

        body.contact-view .contact-location iframe {
            min-height: 240px;
            aspect-ratio: 4 / 3;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        body.contact-view #main-content::after,
        body.contact-view .contact-page > .page-intro::after {
            animation: none;
        }

        body.contact-view *,
        body.contact-view *::before,
        body.contact-view *::after {
            scroll-behavior: auto !important;
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: .01ms !important;
        }
    }
</style>
@endpush

@section('content')
<div class="page-content contact-page">
    <header class="page-intro">
        <span class="contact-hero-kicker">
            <svg class="icon" aria-hidden="true"><use href="#pcu-grad"></use></svg>
            PHILIPPINE CHRISTIAN UNIVERSITY
        </span>
        <h1>Let’s connect.</h1>
        <p>We’d love to hear from you. Find our campus or reach the university using the contact information below.</p>
        <div class="contact-hero-actions">
            <a class="button button-primary" href="#contact-location">
                Visit campus
                <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg>
            </a>
            <a class="button button-secondary" href="#contact-information">Get in touch</a>
        </div>
    </header>

    <div class="contact-layout">
        <section class="surface-card contact-location" id="contact-location" aria-labelledby="contact-location-title">
            <div class="contact-panel-heading">
                <p class="contact-panel-kicker">VISIT CAMPUS</p>
                <h2 id="contact-location-title">Our location</h2>
                <p class="contact-panel-description">Find us and plan your visit to campus.</p>
            </div>
            <div class="contact-map-frame">
                <iframe
                    title="Map showing the university location"
                    loading="lazy"
                    allowfullscreen
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.086509486048!2d144.9630579153167!3d-37.81410797975195!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad642af0f11fd81%3A0xf577d1f9f1b1a1b1!2sUniversity%20of%20Melbourne!5e0!3m2!1sen!2sau!4v1633071234567!5m2!1sen!2sau"
                ></iframe>
            </div>
        </section>

        <section class="surface-card contact-info" id="contact-information" aria-labelledby="contact-info-title">
            <div class="contact-panel-heading">
                <p class="contact-panel-kicker">GET IN TOUCH</p>
                <h2 id="contact-info-title">Contact information</h2>
                <p class="contact-panel-description">Choose the best way to reach our team.</p>
            </div>
            <div class="surface-grid contact-info-grid">
                <article class="contact-info-item">
                    <span class="contact-info-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>
                    </span>
                    <h3>Address</h3>
                    <p>123 University Ave, City, Country</p>
                </article>
                <article class="contact-info-item">
                    <span class="contact-info-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M7 3h3l1.5 4-2 1.5a15 15 0 0 0 6 6l1.5-2 4 1.5v3c0 1.1-.9 2-2 2C10.7 19 5 13.3 5 6c0-1.7.9-3 2-3Z"></path></svg>
                    </span>
                    <h3>Phone</h3>
                    <p><a href="tel:+1234567890">+123 456 7890</a></p>
                </article>
                <article class="contact-info-item">
                    <span class="contact-info-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m4 7 8 6 8-6"></path></svg>
                    </span>
                    <h3>Email</h3>
                    <p><a href="mailto:contact@university.edu">contact@university.edu</a></p>
                </article>
                <article class="contact-info-item">
                    <span class="contact-info-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3.5 2"></path></svg>
                    </span>
                    <h3>Office hours</h3>
                    <p>Mon - Fri: 9:00 AM - 5:00 PM</p>
                </article>
            </div>
        </section>
    </div>
</div>
@endsection

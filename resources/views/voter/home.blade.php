@extends('layouts.voter')

@section('title', 'PCU Online Election System')
@section('body-class', 'voter-home-page')

@push('styles')
<style>
    body.voter-home-page .candidate-showcase {
        --candidate-section-start: #eaf2ff;
        --candidate-section-mid: #d8e7ff;
        --candidate-section-end: #f3f7ff;
        --candidate-section-text: #142746;
        --candidate-section-muted: #405776;
        --candidate-section-kicker: #2457a6;
        --candidate-badge-text: #24466f;
        --candidate-badge-bg: rgba(255, 255, 255, .78);
        --candidate-badge-border: rgba(36, 70, 111, .18);
        --candidate-control-text: #173b69;
        --candidate-control-bg: rgba(255, 255, 255, .86);
        --candidate-control-border: rgba(23, 59, 105, .2);
        --candidate-dot: rgba(23, 59, 105, .35);
        --candidate-dot-active: #2457a6;
        position: relative;
        isolation: isolate;
        overflow: hidden;
        padding-block: clamp(28px, 4.5vw, 52px);
        background:
            radial-gradient(ellipse at 0% 100%, rgba(67, 119, 230, .2), transparent 44%),
            linear-gradient(135deg, var(--candidate-section-start), var(--candidate-section-mid) 58%, var(--candidate-section-end));
        color: var(--candidate-section-text);
        transition: background-color .25s ease, color .25s ease;
    }

    :root[data-theme="dark"] body.voter-home-page .candidate-showcase {
        --candidate-section-start: #07162f;
        --candidate-section-mid: #102b58;
        --candidate-section-end: #173a73;
        --candidate-section-text: #f7faff;
        --candidate-section-muted: rgba(235, 242, 255, .76);
        --candidate-section-kicker: #a9c8ff;
        --candidate-badge-text: #e7f0ff;
        --candidate-badge-bg: rgba(255, 255, 255, .08);
        --candidate-badge-border: rgba(255, 255, 255, .2);
        --candidate-control-text: #f7faff;
        --candidate-control-bg: rgba(255, 255, 255, .08);
        --candidate-control-border: rgba(255, 255, 255, .24);
        --candidate-dot: rgba(255, 255, 255, .38);
        --candidate-dot-active: #a8c9ff;
    }

    body.voter-home-page .candidate-showcase::before,
    body.voter-home-page .candidate-showcase::after {
        position: absolute;
        z-index: -1;
        border: 1px solid color-mix(in srgb, var(--candidate-section-text), transparent 88%);
        border-radius: 50%;
        content: "";
        pointer-events: none;
    }

    body.voter-home-page .candidate-showcase::before {
        top: -17rem;
        right: -9rem;
        width: 38rem;
        aspect-ratio: 1;
        box-shadow: 0 0 0 3rem color-mix(in srgb, var(--candidate-section-text), transparent 96%), 0 0 0 6rem color-mix(in srgb, var(--candidate-section-text), transparent 97%);
    }

    body.voter-home-page .candidate-showcase::after {
        bottom: -20rem;
        left: 16%;
        width: 30rem;
        aspect-ratio: 1;
        border-color: color-mix(in srgb, var(--candidate-section-text), transparent 92%);
    }

    body.voter-home-page .candidate-showcase-inner {
        width: min(1180px, 100% - 2 * clamp(20px, 5vw, 44px));
    }

    body.voter-home-page .candidate-showcase-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    body.voter-home-page .candidate-showcase-kicker {
        margin: 0 0 7px;
        color: var(--candidate-section-kicker);
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .15em;
    }

    body.voter-home-page .candidate-showcase-heading h1 {
        margin: 0;
        color: var(--candidate-section-text);
        font-size: clamp(1.65rem, 3.4vw, 2.45rem);
        letter-spacing: -.04em;
        line-height: 1.15;
        text-wrap: balance;
    }

    body.voter-home-page .candidate-showcase-heading p:not(.candidate-showcase-kicker) {
        max-width: 58ch;
        margin: 9px 0 0;
        color: var(--candidate-section-muted);
        font-size: .97rem;
    }

    body.voter-home-page .candidate-live-badge {
        display: inline-flex;
        flex: none;
        align-items: center;
        gap: 9px;
        padding: 9px 13px;
        border: 1px solid var(--candidate-badge-border);
        border-radius: 999px;
        background: var(--candidate-badge-bg);
        color: var(--candidate-badge-text);
        font-size: .79rem;
        font-weight: 700;
        backdrop-filter: blur(10px);
    }

    body.voter-home-page .candidate-live-badge::before {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #6ee7a0;
        box-shadow: 0 0 0 4px rgba(110, 231, 160, .13);
        content: "";
    }

    body.voter-home-page .candidate-carousel-stage {
        --candidate-slide-stage: #e3edfc;
        --candidate-slide-border: #c3d5ef;
        --candidate-slide-text: #142746;
        --candidate-slide-muted: #405776;
        --candidate-slide-accent: #315f9f;
        --candidate-slide-track: rgba(34, 68, 118, .13);
        --candidate-slide-overlay: linear-gradient(100deg, rgba(247, 250, 255, .97) 0%, rgba(241, 247, 255, .9) 48%, rgba(230, 240, 255, .68) 100%);
        --candidate-slide-backdrop-opacity: .12;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--candidate-slide-border);
        border-radius: 28px;
        background: var(--candidate-slide-stage);
        box-shadow: 0 20px 48px rgba(22, 55, 105, .18), inset 0 1px rgba(255, 255, 255, .76);
    }

    :root[data-theme="dark"] body.voter-home-page .candidate-carousel-stage {
        --candidate-slide-stage: #0b1a35;
        --candidate-slide-border: rgba(255, 255, 255, .19);
        --candidate-slide-text: #fff;
        --candidate-slide-muted: rgba(235, 242, 255, .82);
        --candidate-slide-accent: #bdd4ff;
        --candidate-slide-track: rgba(255, 255, 255, .12);
        --candidate-slide-overlay: linear-gradient(100deg, rgba(5, 16, 37, .96) 0%, rgba(5, 18, 42, .88) 48%, rgba(5, 18, 42, .52) 100%);
        --candidate-slide-backdrop-opacity: .34;
        box-shadow: 0 24px 60px rgba(0, 7, 22, .32), inset 0 1px rgba(255, 255, 255, .1);
    }

    body.voter-home-page .candidate-carousel-stage:focus-visible {
        outline-color: #b9d2ff;
        outline-offset: 5px;
    }

    body.voter-home-page .candidate-slide {
        position: relative;
        isolation: isolate;
        display: grid;
        min-height: clamp(390px, 42vw, 500px);
        grid-template-columns: minmax(0, 1.1fr) minmax(230px, .9fr);
        align-items: center;
        gap: clamp(20px, 5vw, 64px);
        overflow: hidden;
        padding: clamp(28px, 6vw, 68px);
        animation: candidate-slide-in .72s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .candidate-slide[hidden] {
        display: none;
    }

    body.voter-home-page .candidate-slide::before,
    body.voter-home-page .candidate-slide::after {
        position: absolute;
        z-index: -1;
        inset: 0;
        content: "";
        pointer-events: none;
    }

    body.voter-home-page .candidate-slide::before {
        background: var(--candidate-slide-overlay);
    }

    body.voter-home-page .candidate-slide::after {
        background: radial-gradient(ellipse at 78% 50%, rgba(84, 139, 255, .22), transparent 40%);
    }

    body.voter-home-page .candidate-slide-backdrop {
        position: absolute;
        z-index: -2;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: var(--candidate-slide-backdrop-opacity);
        filter: saturate(.8);
        animation: candidate-backdrop-in 1.1s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .candidate-slide-copy {
        min-width: 0;
    }

    body.voter-home-page .candidate-slide-overline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
        color: var(--candidate-slide-accent);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .13em;
        text-transform: uppercase;
        animation: candidate-copy-in .55s .08s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .candidate-slide-overline::before {
        width: 22px;
        height: 2px;
        background: var(--candidate-slide-accent);
        content: "";
    }

    body.voter-home-page .candidate-slide-position {
        display: block;
        margin-bottom: 7px;
        color: var(--candidate-slide-text);
        font-size: clamp(1rem, 1.7vw, 1.2rem);
        font-weight: 700;
        animation: candidate-copy-in .58s .14s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .candidate-slide h3 {
        max-width: 16ch;
        margin: 0;
        color: var(--candidate-slide-text);
        font-size: clamp(2rem, 5.2vw, 3.8rem);
        letter-spacing: -.05em;
        line-height: 1.02;
        overflow-wrap: anywhere;
        text-wrap: balance;
        animation: candidate-copy-in .62s .2s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .candidate-vote-share {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 16px;
        margin-top: clamp(26px, 4vw, 38px);
        color: var(--candidate-slide-muted);
        font-size: .9rem;
        font-weight: 650;
    }

    body.voter-home-page .candidate-vote-share strong {
        color: var(--candidate-slide-text);
        font-size: clamp(1.8rem, 4vw, 2.55rem);
        font-variant-numeric: tabular-nums;
        letter-spacing: -.04em;
        line-height: 1;
        animation: candidate-number-in .62s .3s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .candidate-vote-track {
        height: 10px;
        overflow: hidden;
        margin-top: 13px;
        border: 1px solid color-mix(in srgb, var(--candidate-slide-text), transparent 88%);
        border-radius: 999px;
        background: var(--candidate-slide-track);
    }

    body.voter-home-page .candidate-vote-fill {
        display: block;
        width: var(--vote-share, 0%);
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #5f99ff, #a8d2ff);
        box-shadow: 0 0 18px rgba(101, 157, 255, .45);
        transition: width .7s cubic-bezier(.2, .75, .25, 1);
        animation: candidate-progress-in .8s .32s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .candidate-vote-note {
        margin: 10px 0 0;
        color: var(--candidate-slide-muted);
        font-size: .83rem;
        animation: candidate-copy-in .58s .36s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .candidate-slide-portrait {
        position: relative;
        display: grid;
        min-width: 0;
        min-height: 300px;
        place-items: center;
    }

    body.voter-home-page .candidate-slide-portrait::before {
        position: absolute;
        width: min(100%, 340px);
        aspect-ratio: 1;
        border: 1px solid color-mix(in srgb, var(--candidate-slide-text), transparent 82%);
        border-radius: 50%;
        background: radial-gradient(circle, rgba(111, 160, 255, .2), color-mix(in srgb, var(--candidate-slide-text), transparent 97%) 66%, transparent 67%);
        box-shadow: 0 0 0 22px color-mix(in srgb, var(--candidate-slide-text), transparent 97%), 0 0 0 46px color-mix(in srgb, var(--candidate-slide-text), transparent 98%);
        content: "";
    }

    body.voter-home-page .candidate-slide-portrait img {
        position: relative;
        width: clamp(210px, 25vw, 310px);
        aspect-ratio: 1;
        border: 8px solid rgba(255, 255, 255, .88);
        border-radius: 50%;
        background: #eaf1ff;
        box-shadow: 0 24px 50px rgba(0, 6, 22, .38), 0 0 0 1px rgba(255, 255, 255, .2);
        object-fit: cover;
        animation: candidate-portrait-in .7s .08s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .candidate-carousel-controls {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 17px;
    }

    body.voter-home-page .candidate-carousel-controls[hidden] {
        display: none;
    }

    body.voter-home-page .candidate-carousel-actions,
    body.voter-home-page .candidate-carousel-pagination {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    body.voter-home-page .candidate-carousel-button {
        display: inline-flex;
        min-width: 42px;
        min-height: 42px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 0 12px;
        border: 1px solid var(--candidate-control-border);
        border-radius: 13px;
        background: var(--candidate-control-bg);
        color: var(--candidate-control-text);
        font: inherit;
        font-size: .82rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform .18s ease, background-color .18s ease, border-color .18s ease;
    }

    body.voter-home-page .candidate-carousel-button:hover {
        transform: translateY(-2px);
        border-color: color-mix(in srgb, var(--candidate-control-text), transparent 45%);
        background: color-mix(in srgb, var(--candidate-control-text), transparent 88%);
    }

    body.voter-home-page .candidate-carousel-button svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-width: 2;
    }

    body.voter-home-page .candidate-carousel-dot {
        width: 9px;
        height: 9px;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: var(--candidate-dot);
        cursor: pointer;
        transition: width .2s ease, background-color .2s ease;
    }

    body.voter-home-page .candidate-carousel-dot[aria-current="true"] {
        width: 25px;
        background: var(--candidate-dot-active);
    }

    body.voter-home-page .candidate-carousel-empty {
        padding: clamp(26px, 5vw, 44px);
        border: 1px dashed color-mix(in srgb, var(--candidate-slide-text), transparent 70%);
        border-radius: 20px;
        background: color-mix(in srgb, var(--candidate-slide-text), transparent 94%);
        color: var(--candidate-slide-muted);
        text-align: center;
    }

    body.voter-home-page .candidate-carousel-empty h3 {
        margin: 0 0 7px;
        color: var(--candidate-slide-text);
        font-size: 1.25rem;
    }

    body.voter-home-page .candidate-carousel-empty p {
        margin: 0;
    }

    @keyframes candidate-slide-in {
        from { opacity: 0; filter: blur(4px); transform: translate3d(34px, 0, 0) scale(.985); }
        to { opacity: 1; filter: blur(0); transform: translate3d(0, 0, 0) scale(1); }
    }

    @keyframes candidate-backdrop-in {
        from { transform: scale(1.08); }
        to { transform: scale(1); }
    }

    @keyframes candidate-copy-in {
        from { opacity: 0; transform: translate3d(0, 14px, 0); }
        to { opacity: 1; transform: translate3d(0, 0, 0); }
    }

    @keyframes candidate-number-in {
        from { opacity: 0; transform: translate3d(0, 12px, 0) scale(.92); }
        to { opacity: 1; transform: translate3d(0, 0, 0) scale(1); }
    }

    @keyframes candidate-progress-in {
        from { transform: scaleX(.05); transform-origin: left center; }
        to { transform: scaleX(1); transform-origin: left center; }
    }

    @keyframes candidate-portrait-in {
        from { opacity: .65; transform: scale(.96); }
        to { opacity: 1; transform: scale(1); }
    }

    body.voter-home-page .hero {
        --hero-text: #142746;
        --hero-muted: #405776;
        --hero-status-text: #24466f;
        --hero-secondary-text: #173b69;
        --hero-overlay-left: rgba(242, 247, 255, .94);
        --hero-overlay-mid: rgba(235, 243, 255, .83);
        --hero-overlay-right: rgba(223, 236, 255, .58);
        --hero-overlay-bottom: rgba(220, 233, 251, .52);
        --hero-overlay-top: rgba(240, 246, 255, .08);
        --hero-pattern: rgba(28, 70, 132, .42);
        position: relative;
        isolation: isolate;
        overflow: hidden;
        background-color: #d9e8ff;
        background:
            linear-gradient(90deg, var(--hero-overlay-left) 0%, var(--hero-overlay-mid) 48%, var(--hero-overlay-right) 100%),
            linear-gradient(0deg, var(--hero-overlay-bottom) 0%, transparent 58%, var(--hero-overlay-top) 100%),
            url("{{ asset('images/background1.jpg') }}") center 48% / cover no-repeat;
        color: var(--hero-text);
        transition: color .25s ease, background-color .25s ease;
    }

    :root[data-theme="dark"] body.voter-home-page .hero {
        --hero-text: #f7faff;
        --hero-muted: rgba(235, 242, 255, .78);
        --hero-status-text: #e3edff;
        --hero-secondary-text: #f7faff;
        --hero-overlay-left: rgba(5, 17, 42, .88);
        --hero-overlay-mid: rgba(5, 20, 48, .72);
        --hero-overlay-right: rgba(5, 20, 48, .46);
        --hero-overlay-bottom: rgba(4, 14, 32, .62);
        --hero-overlay-top: rgba(4, 14, 32, .24);
        --hero-pattern: rgba(255, 255, 255, .5);
        background-color: #07162f;
    }

    body.voter-home-page .hero::before,
    body.voter-home-page .hero::after {
        position: absolute;
        z-index: -1;
        inset: 0;
        pointer-events: none;
        content: "";
    }

    body.voter-home-page .hero::before {
        background: radial-gradient(circle, var(--hero-pattern) 1px, transparent 1.5px);
        background-size: 34px 34px;
        opacity: .12;
        mask-image: linear-gradient(110deg, #000, transparent 75%);
    }

    body.voter-home-page .hero::after {
        inset: auto -9rem -17rem auto;
        width: 34rem;
        aspect-ratio: 1;
        border: 1px solid color-mix(in srgb, var(--hero-text), transparent 88%);
        border-radius: 50%;
        box-shadow: 0 0 0 3rem color-mix(in srgb, var(--hero-text), transparent 96%), 0 0 0 6rem color-mix(in srgb, var(--hero-text), transparent 97%);
    }

    body.voter-home-page .hero-inner {
        position: relative;
        z-index: 0;
        width: min(1180px, 100% - 2 * clamp(20px, 5vw, 44px));
        gap: clamp(32px, 6vw, 76px);
        padding-block: clamp(48px, 8vw, 96px);
    }

    body.voter-home-page .hero-copy {
        animation: home-intro .7s cubic-bezier(.2, .75, .25, 1) both;
    }

    body.voter-home-page .hero-status {
        border: 1px solid color-mix(in srgb, var(--hero-text), transparent 76%);
        background: color-mix(in srgb, var(--hero-text), transparent 92%);
        color: var(--hero-status-text);
        backdrop-filter: blur(8px);
    }

    body.voter-home-page .hero-status.status-open {
        background: rgba(34, 197, 94, .16);
        color: #166534;
    }

    body.voter-home-page .hero-status.status-ended {
        background: rgba(71, 85, 105, .12);
        color: #334155;
    }

    :root[data-theme="dark"] body.voter-home-page .hero-status.status-open {
        color: #bbf7d0;
    }

    :root[data-theme="dark"] body.voter-home-page .hero-status.status-ended {
        background: rgba(255, 255, 255, .12);
        color: #e2e8f0;
    }

    body.voter-home-page .hero h2 {
        max-width: 13ch;
        margin-top: 22px;
        color: var(--hero-text);
        font-size: clamp(2.75rem, 6.5vw, 5.35rem);
        line-height: .99;
        letter-spacing: -.055em;
        text-wrap: balance;
    }

    body.voter-home-page .hero-lead {
        max-width: 43ch;
        margin-top: 20px;
        color: var(--hero-muted);
        font-size: clamp(1.05rem, 1.7vw, 1.22rem);
        line-height: 1.7;
    }

    body.voter-home-page .button-row {
        gap: 14px;
        margin-top: 30px;
    }

    body.voter-home-page .button {
        min-height: 54px;
        border-radius: 15px;
        transition: transform .22s ease, box-shadow .22s ease, background-color .22s ease;
    }

    body.voter-home-page .button-primary {
        border-color: #78a7ff;
        background: linear-gradient(135deg, #4c83f1, #2860dc);
        box-shadow: 0 10px 28px rgba(25, 82, 197, .38), inset 0 1px rgba(255, 255, 255, .22);
        color: #fff;
    }

    body.voter-home-page .button-primary:hover {
        box-shadow: 0 15px 34px rgba(25, 82, 197, .48), inset 0 1px rgba(255, 255, 255, .28);
    }

    body.voter-home-page .button-secondary {
        border-color: color-mix(in srgb, var(--hero-text), transparent 68%);
        background: color-mix(in srgb, var(--hero-text), transparent 92%);
        color: var(--hero-secondary-text);
    }

    body.voter-home-page .button-secondary:hover {
        background: color-mix(in srgb, var(--hero-text), transparent 86%);
    }

    body.voter-home-page .steps-card {
        --bg: #f5f8fd;
        --card: rgba(255, 255, 255, .97);
        --ink: #0f1b33;
        --mut: #4a5873;
        --line: #dfe6f1;
        --brand: #2563eb;
        --brand-contrast: #fff;
        --tint: #eaf1ff;
        --ok: #15803d;
        --ok-bg: #dcfce7;
        color: var(--ink);
        padding: clamp(20px, 3vw, 30px);
        border-color: rgba(255, 255, 255, .72);
        border-radius: 26px;
        box-shadow: 0 28px 72px rgba(1, 12, 34, .34), 0 2px 8px rgba(1, 12, 34, .08);
        animation: home-intro .8s .12s cubic-bezier(.2, .75, .25, 1) both;
    }

    :root[data-theme="dark"] body.voter-home-page .steps-card {
        --bg: #111c30;
        --card: #17253c;
        --ink: #e9effc;
        --mut: #b7c5dc;
        --line: #2c3d59;
        --brand: #78a7ff;
        --brand-contrast: #071225;
        --tint: #203657;
        --ok: #86efac;
        --ok-bg: #12351f;
        border-color: rgba(199, 218, 255, .2);
        box-shadow: 0 28px 72px rgba(1, 12, 34, .46), 0 2px 8px rgba(1, 12, 34, .18);
    }

    body.voter-home-page .steps-card h2 {
        font-size: 1.3rem;
        letter-spacing: -.025em;
    }

    body.voter-home-page .steps-list {
        gap: 11px;
    }

    body.voter-home-page .step-item {
        min-height: 72px;
        padding: 13px 15px;
        border-radius: 16px;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
    }

    body.voter-home-page .step-item:hover {
        transform: translateX(3px);
        box-shadow: 0 8px 18px rgba(15, 27, 51, .07);
    }

    body.voter-home-page .step-item.is-current {
        box-shadow: inset 3px 0 #2563eb, 0 8px 20px rgba(37, 99, 235, .09);
    }

    body.voter-home-page .content-section {
        position: relative;
        padding-block: clamp(42px, 6vw, 72px);
        border-bottom: 1px solid color-mix(in srgb, var(--line), transparent 25%);
    }

    body.voter-home-page #how-it-works,
    body.voter-home-page #events,
    body.voter-home-page #guidelines {
        background:
            radial-gradient(ellipse at 100% 0%, color-mix(in srgb, var(--tint), transparent 32%), transparent 50%),
            var(--tint);
    }

    body.voter-home-page #how-it-works {
        --action-section-start: #eaf2ff;
        --action-section-mid: #d7e6ff;
        --action-section-end: #f0f6ff;
        --action-section-text: #142746;
        --action-section-muted: #405776;
        --action-section-kicker: #2457a6;
        --action-card-start: #fff;
        --action-card-end: #f5f8ff;
        --action-card-text: #0f1b33;
        --action-card-muted: #4a5873;
        overflow: hidden;
        border-bottom: 0;
        background:
            radial-gradient(ellipse at 8% 100%, rgba(56, 111, 229, .2), transparent 42%),
            radial-gradient(ellipse at 100% 0%, rgba(83, 145, 255, .23), transparent 42%),
            linear-gradient(135deg, var(--action-section-start), var(--action-section-mid) 54%, var(--action-section-end));
        color: var(--action-section-text);
        transition: background-color .25s ease, color .25s ease;
    }

    :root[data-theme="dark"] body.voter-home-page #how-it-works {
        --action-section-start: #17396b;
        --action-section-mid: #24508c;
        --action-section-end: #162f58;
        --action-section-text: #f8fbff;
        --action-section-muted: #d0def4;
        --action-section-kicker: #c5d9ff;
        --action-card-start: #17253c;
        --action-card-end: #111c30;
        --action-card-text: #f0f5ff;
        --action-card-muted: #b7c5dc;
        --action-card-line: rgba(199, 218, 255, .2);
    }

    body.voter-home-page #how-it-works .page-wrap {
        position: relative;
        z-index: 1;
    }

    body.voter-home-page #how-it-works .section-heading {
        margin-bottom: clamp(26px, 4vw, 38px);
    }

    body.voter-home-page .action-kicker {
        margin: 0 0 8px;
        color: var(--action-section-kicker);
        font-size: .76rem;
        font-weight: 800;
        letter-spacing: .16em;
    }

    body.voter-home-page #how-it-works .section-heading h2 {
        color: var(--action-section-text);
        font-size: clamp(1.85rem, 4vw, 2.8rem);
        letter-spacing: -.045em;
    }

    body.voter-home-page #how-it-works .section-heading p {
        max-width: 62ch;
        margin-top: 10px;
        color: var(--action-section-muted);
    }

    body.voter-home-page #how-it-works .action-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: clamp(14px, 2vw, 22px);
    }

    body.voter-home-page .section-heading {
        align-items: flex-end;
        margin-bottom: 24px;
    }

    body.voter-home-page .section-heading h2 {
        letter-spacing: -.035em;
        text-wrap: balance;
    }

    body.voter-home-page .section-heading p {
        max-width: 58ch;
    }

    body.voter-home-page .action-card,
    body.voter-home-page .date-card,
    body.voter-home-page .election-banner,
    body.voter-home-page .event-card,
    body.voter-home-page .article-card,
    body.voter-home-page .faq-item,
    body.voter-home-page .guidelines-card,
    body.voter-home-page .empty-card {
        border-color: color-mix(in srgb, var(--line), transparent 12%);
        border-radius: 20px;
        box-shadow: 0 3px 8px rgba(15, 27, 51, .035), 0 16px 36px rgba(15, 27, 51, .065);
    }

    body.voter-home-page .action-card {
        --action-accent: #4f46e5;
        position: relative;
        min-height: 286px;
        overflow: hidden;
        padding: clamp(22px, 2.8vw, 32px);
        border: 1px solid var(--action-card-line, rgba(255, 255, 255, .84));
        border-radius: 24px;
        background: linear-gradient(150deg, var(--action-card-start), var(--action-card-end));
        color: var(--action-card-text);
        box-shadow: 0 8px 18px rgba(0, 7, 22, .12), 0 22px 42px rgba(0, 7, 22, .16);
        cursor: pointer;
        transition: transform .28s cubic-bezier(.2, .75, .25, 1), border-color .24s ease, box-shadow .28s ease, background-color .24s ease;
    }

    body.voter-home-page .action-card::after {
        position: absolute;
        right: -48px;
        bottom: -76px;
        width: 176px;
        aspect-ratio: 1;
        border: 1px solid color-mix(in srgb, var(--action-accent), transparent 82%);
        border-radius: 50%;
        box-shadow: 0 0 0 20px color-mix(in srgb, var(--action-accent), transparent 94%);
        content: "";
        pointer-events: none;
        transition: transform .45s cubic-bezier(.2, .75, .25, 1);
    }

    body.voter-home-page .action-card:nth-child(2) {
        --action-accent: #047857;
    }

    body.voter-home-page .action-card:nth-child(3) {
        --action-accent: #b45309;
    }

    body.voter-home-page .action-card::before {
        position: absolute;
        inset: 0 0 auto;
        height: 5px;
        background: linear-gradient(90deg, color-mix(in srgb, var(--action-accent), #fff 15%), var(--action-accent));
        content: "";
        pointer-events: none;
    }

    body.voter-home-page .action-card:hover {
        transform: translateY(-8px);
        border-color: #fff;
        box-shadow: 0 15px 28px rgba(0, 7, 22, .17), 0 30px 54px color-mix(in srgb, var(--action-accent), transparent 78%);
    }

    body.voter-home-page .action-card:active {
        transform: translateY(-2px) scale(.99);
    }

    body.voter-home-page .action-card:focus-visible {
        outline: 3px solid #a9c8ff;
        outline-offset: 5px;
        box-shadow: 0 0 0 8px rgba(169, 200, 255, .2), 0 18px 38px rgba(0, 7, 22, .2);
    }

    @media (hover: hover) and (pointer: fine) {
        body.voter-home-page .action-card:hover::after {
            transform: scale(1.16) translate(-8px, -8px);
        }

        body.voter-home-page .action-card:hover .action-icon {
            transform: translateY(-3px) rotate(-4deg) scale(1.05);
        }

        body.voter-home-page .action-card:hover .action-link .icon {
            transform: translateX(4px);
        }
    }

    body.voter-home-page .action-icon {
        width: 66px;
        height: 66px;
        border: 1px solid color-mix(in srgb, var(--action-accent), transparent 80%);
        border-radius: 20px;
        background: linear-gradient(145deg, color-mix(in srgb, var(--action-accent), #fff 88%), color-mix(in srgb, var(--action-accent), #fff 95%));
        color: var(--action-accent);
        transition: transform .28s cubic-bezier(.2, .75, .25, 1), box-shadow .24s ease;
    }

    body.voter-home-page .action-icon .icon {
        width: 30px;
        height: 30px;
    }

    body.voter-home-page .action-card h3 {
        margin-top: 12px;
        color: var(--action-card-text);
        font-size: clamp(1.45rem, 2.2vw, 1.8rem);
        font-weight: 800;
        letter-spacing: -.02em;
    }

    body.voter-home-page .action-card p {
        max-width: 34ch;
        color: var(--action-card-muted);
        line-height: 1.55;
    }

    body.voter-home-page .action-link {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 9px;
        margin-top: auto;
        padding: 11px 15px;
        border: 1px solid color-mix(in srgb, var(--action-accent), transparent 14%);
        border-radius: 13px;
        background: var(--action-accent);
        color: #fff;
        font-size: .92rem;
        font-weight: 800;
        box-shadow: 0 7px 16px color-mix(in srgb, var(--action-accent), transparent 78%);
        transition: gap .2s ease, box-shadow .2s ease;
    }

    body.voter-home-page .action-card:hover .action-link {
        gap: 13px;
        box-shadow: 0 10px 20px color-mix(in srgb, var(--action-accent), transparent 68%);
    }

    body.voter-home-page .action-link .icon {
        width: 18px;
        height: 18px;
        transition: transform .2s ease;
    }

    body.voter-home-page .date-card {
        --date-accent: #315fbd;
        position: relative;
        min-height: 174px;
        align-items: flex-start;
        gap: 18px;
        overflow: hidden;
        padding: clamp(20px, 3vw, 28px);
        border: 1px solid var(--date-card-border, #d7e2f2);
        border-radius: 22px;
        background:
            radial-gradient(circle at 100% 0%, color-mix(in srgb, var(--date-accent), transparent 92%), transparent 48%),
            linear-gradient(145deg, var(--date-card-start, #fff), var(--date-card-end, #f6f9ff));
        box-shadow: 0 8px 22px rgba(15, 27, 51, .055), 0 20px 38px rgba(15, 27, 51, .045);
        transition: transform .24s cubic-bezier(.2, .75, .25, 1), border-color .24s ease, box-shadow .24s ease;
    }

    body.voter-home-page #dates {
        --date-section-start: #f5f8fd;
        --date-section-end: #eaf1fb;
        --date-section-text: #142746;
        --date-section-muted: #536680;
        --date-card-start: #fff;
        --date-card-end: #f6f9ff;
        --date-card-border: #d7e2f2;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(67, 119, 230, .11), transparent 42%),
            linear-gradient(145deg, var(--date-section-start), var(--date-section-end));
    }

    :root[data-theme="dark"] body.voter-home-page #dates {
        --date-section-start: #101b2d;
        --date-section-end: #172743;
        --date-section-text: #edf4ff;
        --date-section-muted: #b7c5dc;
        --date-card-start: #1a2a43;
        --date-card-end: #142139;
        --date-card-border: #2c4161;
    }

    body.voter-home-page #dates .section-heading {
        align-items: flex-end;
        margin-bottom: clamp(22px, 3vw, 30px);
    }

    body.voter-home-page #dates .section-heading h2 {
        color: var(--date-section-text);
        font-size: clamp(1.8rem, 3.4vw, 2.4rem);
    }

    body.voter-home-page #dates .section-heading p {
        color: var(--date-section-muted);
    }

    body.voter-home-page .dates-kicker {
        margin: 0 0 7px;
        color: var(--brand);
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .15em;
    }

    body.voter-home-page .date-grid {
        margin-block: 0;
        padding: 0;
        width: min(920px, 100%);
        list-style: none;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: clamp(14px, 2vw, 22px);
        margin-inline: auto;
    }

    body.voter-home-page .date-card:nth-child(2) {
        --date-accent: #087b68;
    }

    body.voter-home-page .date-card::before {
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: var(--date-accent);
        content: "";
    }

    body.voter-home-page .date-card:hover {
        transform: translateY(-4px);
        border-color: color-mix(in srgb, var(--date-accent), transparent 62%);
        box-shadow: 0 14px 30px color-mix(in srgb, var(--date-accent), transparent 88%), 0 24px 42px rgba(15, 27, 51, .08);
    }

    body.voter-home-page .date-card-icon {
        display: grid;
        width: 54px;
        height: 54px;
        flex: none;
        place-items: center;
        border: 1px solid color-mix(in srgb, var(--date-accent), transparent 82%);
        border-radius: 17px;
        background: color-mix(in srgb, var(--date-accent), var(--date-card-start) 91%);
        color: var(--date-accent);
    }

    body.voter-home-page .date-card-icon .icon {
        width: 26px;
        height: 26px;
    }

    body.voter-home-page .date-card-copy {
        min-width: 0;
    }

    body.voter-home-page .date-card-phase {
        display: block;
        margin: 1px 0 9px;
        color: var(--date-accent);
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: .13em;
    }

    body.voter-home-page .date-card strong {
        margin-bottom: 8px;
        color: var(--date-section-text);
        font-size: clamp(1.08rem, 1.8vw, 1.28rem);
        font-weight: 800;
        letter-spacing: -.02em;
    }

    body.voter-home-page .date-card span:last-child {
        color: var(--date-section-muted);
        font-size: .94rem;
        font-variant-numeric: tabular-nums;
        line-height: 1.55;
        overflow-wrap: anywhere;
    }

    :root[data-theme="dark"] body.voter-home-page .date-card {
        box-shadow: 0 8px 22px rgba(0, 0, 0, .14), 0 20px 38px rgba(0, 0, 0, .12);
    }

    body.voter-home-page #dates .empty-card {
        border-color: var(--date-card-border);
        background: var(--date-card-start);
        color: var(--date-section-muted);
    }

    body.voter-home-page #events {
        --events-start: #f6f9ff;
        --events-end: #eaf1fb;
        --events-text: #142746;
        --events-muted: #536680;
        --events-card: #fff;
        --events-card-soft: #f5f8ff;
        --events-border: #d7e2f2;
        --events-brand: #315fbd;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(67, 119, 230, .1), transparent 42%),
            linear-gradient(145deg, var(--events-start), var(--events-end));
    }

    :root[data-theme="dark"] body.voter-home-page #events {
        --events-start: #101b2d;
        --events-end: #172743;
        --events-text: #edf4ff;
        --events-muted: #b7c5dc;
        --events-card: #1a2a43;
        --events-card-soft: #142139;
        --events-border: #2c4161;
        --events-brand: #8db4ff;
    }

    body.voter-home-page #events .section-heading {
        align-items: flex-end;
        margin-bottom: 24px;
    }

    body.voter-home-page .events-kicker {
        margin: 0 0 7px;
        color: var(--events-brand);
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .15em;
    }

    body.voter-home-page #events .section-heading h2 {
        color: var(--events-text);
        font-size: clamp(1.8rem, 3.4vw, 2.4rem);
    }

    body.voter-home-page #events .section-heading p:not(.events-kicker) {
        color: var(--events-muted);
    }

    body.voter-home-page #events .carousel-controls .icon-button {
        width: 46px;
        height: 46px;
        border: 1px solid color-mix(in srgb, var(--events-brand), transparent 75%);
        border-radius: 15px;
        background: var(--events-card);
        color: var(--events-brand);
        box-shadow: 0 5px 14px rgba(15, 27, 51, .08);
        transition: transform .2s ease, background-color .2s ease, color .2s ease, box-shadow .2s ease;
    }

    body.voter-home-page #events .carousel-controls .icon-button:hover {
        transform: translateY(-2px);
        background: var(--events-brand);
        color: #fff;
        box-shadow: 0 9px 20px color-mix(in srgb, var(--events-brand), transparent 76%);
    }

    body.voter-home-page .election-banner {
        min-height: 156px;
        gap: clamp(18px, 3vw, 32px);
        margin-bottom: clamp(18px, 2.5vw, 26px);
        padding: clamp(18px, 3vw, 28px);
        border: 1px solid var(--events-border);
        border-radius: 24px;
        background:
            radial-gradient(circle at 100% 0%, color-mix(in srgb, var(--events-brand), transparent 91%), transparent 44%),
            linear-gradient(125deg, var(--events-card), var(--events-card-soft));
        box-shadow: 0 9px 24px rgba(15, 27, 51, .055), 0 20px 42px rgba(15, 27, 51, .045);
    }

    body.voter-home-page .election-banner img {
        width: min(34%, 250px);
        max-height: 112px;
        flex: none;
        border-radius: 14px;
        object-fit: contain;
    }

    body.voter-home-page .election-banner-copy span {
        color: var(--events-brand);
        font-size: .73rem;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    body.voter-home-page .election-banner-copy strong {
        margin-top: 4px;
        color: var(--events-text);
        font-size: clamp(1.2rem, 2.5vw, 1.75rem);
        letter-spacing: -.035em;
        line-height: 1.2;
        overflow-wrap: anywhere;
    }

    body.voter-home-page .election-banner-copy p {
        margin: 8px 0 0;
        color: var(--events-muted);
        font-size: .94rem;
        font-variant-numeric: tabular-nums;
    }

    body.voter-home-page #events .event-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 250px), 1fr));
        gap: clamp(14px, 2vw, 22px);
    }

    body.voter-home-page .event-card {
        position: relative;
        display: flex;
        min-height: 340px;
        flex-direction: column;
        border: 1px solid var(--events-border);
        border-radius: 22px;
        background: var(--events-card);
        color: var(--events-text);
        text-align: left;
        box-shadow: 0 8px 20px rgba(15, 27, 51, .055), 0 18px 36px rgba(15, 27, 51, .05);
        cursor: pointer;
        transition: transform .24s cubic-bezier(.2, .75, .25, 1), border-color .24s ease, box-shadow .24s ease;
    }

    body.voter-home-page .event-card:hover {
        transform: translateY(-5px);
        border-color: color-mix(in srgb, var(--events-brand), transparent 52%);
        box-shadow: 0 16px 32px color-mix(in srgb, var(--events-brand), transparent 88%), 0 24px 42px rgba(15, 27, 51, .09);
    }

    body.voter-home-page .event-card:focus-visible {
        outline: 3px solid var(--events-brand);
        outline-offset: 4px;
    }

    body.voter-home-page .event-image,
    body.voter-home-page .event-placeholder {
        height: clamp(150px, 17vw, 205px);
        flex: none;
        overflow: hidden;
        border-radius: 21px 21px 0 0;
        background:
            radial-gradient(circle at 80% 0%, color-mix(in srgb, var(--events-brand), transparent 84%), transparent 46%),
            linear-gradient(145deg, var(--events-card-soft), color-mix(in srgb, var(--events-brand), var(--events-card-soft) 12%));
        color: var(--events-brand);
        object-fit: cover;
    }

    body.voter-home-page .event-image {
        transition: transform .45s cubic-bezier(.2, .75, .25, 1);
    }

    body.voter-home-page .event-card:hover .event-image {
        transform: scale(1.035);
    }

    body.voter-home-page .event-placeholder .icon {
        width: 48px;
        height: 48px;
        padding: 11px;
        border: 1px solid color-mix(in srgb, var(--events-brand), transparent 78%);
        border-radius: 16px;
        background: color-mix(in srgb, var(--events-card), transparent 18%);
    }

    body.voter-home-page .event-card-copy {
        display: flex;
        min-height: 0;
        flex: 1;
        flex-direction: column;
        align-items: flex-start;
        padding: 20px clamp(18px, 2vw, 24px) 22px;
    }

    body.voter-home-page .event-title {
        margin: 0 0 8px;
        color: var(--events-text);
        font-size: clamp(1.08rem, 1.5vw, 1.28rem);
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.28;
    }

    body.voter-home-page .event-card-copy time {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        color: var(--events-brand);
        font-size: .84rem;
        font-variant-numeric: tabular-nums;
        font-weight: 750;
    }

    body.voter-home-page .event-card-copy time::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
        content: "";
    }

    body.voter-home-page .event-description {
        display: -webkit-box;
        overflow: hidden;
        margin-top: 12px;
        color: var(--events-muted);
        font-size: .9rem;
        line-height: 1.55;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
    }

    body.voter-home-page .event-card-hint {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: auto;
        padding-top: 16px;
        color: var(--events-brand);
        font-size: .84rem;
        font-weight: 800;
    }

    body.voter-home-page .event-card-hint .icon {
        width: 17px;
        height: 17px;
        transition: transform .2s ease;
    }

    body.voter-home-page .event-card:hover .event-card-hint .icon {
        transform: translateX(4px);
    }

    body.voter-home-page #events .empty-card {
        border-color: var(--events-border);
        background: var(--events-card);
        color: var(--events-muted);
    }

    body.voter-home-page #articles {
        --articles-start: #f6f9ff;
        --articles-end: #eaf0fa;
        --articles-text: #142746;
        --articles-muted: #536680;
        --articles-card: #fff;
        --articles-card-soft: #f2f6ff;
        --articles-border: #d7e2f2;
        --articles-brand: #315fbd;
        background:
            radial-gradient(ellipse at 0% 100%, rgba(67, 119, 230, .1), transparent 44%),
            linear-gradient(145deg, var(--articles-start), var(--articles-end));
    }

    :root[data-theme="dark"] body.voter-home-page #articles {
        --articles-start: #101b2d;
        --articles-end: #172743;
        --articles-text: #edf4ff;
        --articles-muted: #b7c5dc;
        --articles-card: #1a2a43;
        --articles-card-soft: #142139;
        --articles-border: #2c4161;
        --articles-brand: #8db4ff;
    }

    body.voter-home-page #articles .section-heading {
        align-items: flex-end;
        margin-bottom: 24px;
    }

    body.voter-home-page .articles-kicker {
        margin: 0 0 7px;
        color: var(--articles-brand);
        font-size: .74rem;
        font-weight: 800;
        letter-spacing: .15em;
    }

    body.voter-home-page #articles .section-heading h2 {
        color: var(--articles-text);
        font-size: clamp(1.8rem, 3.4vw, 2.4rem);
    }

    body.voter-home-page #articles .section-heading p:not(.articles-kicker) {
        color: var(--articles-muted);
    }

    body.voter-home-page #articles .carousel-controls .icon-button {
        width: 46px;
        height: 46px;
        border: 1px solid color-mix(in srgb, var(--articles-brand), transparent 75%);
        border-radius: 15px;
        background: var(--articles-card);
        color: var(--articles-brand);
        box-shadow: 0 5px 14px rgba(15, 27, 51, .08);
        transition: transform .2s ease, background-color .2s ease, color .2s ease, box-shadow .2s ease;
    }

    body.voter-home-page #articles .carousel-controls .icon-button:hover {
        transform: translateY(-2px);
        background: var(--articles-brand);
        color: #fff;
        box-shadow: 0 9px 20px color-mix(in srgb, var(--articles-brand), transparent 76%);
    }

    body.voter-home-page #articles .carousel-controls .icon-button:focus-visible {
        outline: 3px solid var(--articles-brand);
        outline-offset: 3px;
    }

    body.voter-home-page #articles .article-grid {
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 250px), 1fr));
        gap: clamp(14px, 2vw, 22px);
    }

    body.voter-home-page .article-card {
        overflow: hidden;
        border: 1px solid var(--articles-border);
        border-radius: 22px;
        background: var(--articles-card);
        box-shadow: 0 8px 20px rgba(15, 27, 51, .055), 0 18px 36px rgba(15, 27, 51, .05);
        transition: transform .24s cubic-bezier(.2, .75, .25, 1), border-color .24s ease, box-shadow .24s ease;
    }

    body.voter-home-page .article-card[hidden] {
        display: none !important;
    }

    body.voter-home-page .article-card:hover {
        transform: translateY(-5px);
        border-color: color-mix(in srgb, var(--articles-brand), transparent 52%);
        box-shadow: 0 16px 32px color-mix(in srgb, var(--articles-brand), transparent 88%), 0 24px 42px rgba(15, 27, 51, .09);
    }

    body.voter-home-page .article-open {
        display: flex;
        min-height: 100%;
        flex-direction: column;
        color: var(--articles-text);
        cursor: pointer;
    }

    body.voter-home-page .article-open:focus-visible {
        outline: 3px solid var(--articles-brand);
        outline-offset: -4px;
    }

    body.voter-home-page .article-image {
        height: clamp(165px, 17vw, 205px);
        flex: none;
        background:
            radial-gradient(circle at 80% 0%, color-mix(in srgb, var(--articles-brand), transparent 84%), transparent 46%),
            linear-gradient(145deg, var(--articles-card-soft), color-mix(in srgb, var(--articles-brand), var(--articles-card-soft) 12%));
        transition: transform .45s cubic-bezier(.2, .75, .25, 1);
    }

    body.voter-home-page .article-card:hover .article-image {
        transform: scale(1.035);
    }

    body.voter-home-page .article-copy {
        display: flex;
        min-height: 226px;
        flex: 1;
        flex-direction: column;
        align-items: flex-start;
        padding: 20px clamp(18px, 2vw, 24px) 22px;
    }

    body.voter-home-page .article-title {
        margin: 0;
        color: var(--articles-text);
        font-size: clamp(1.08rem, 1.5vw, 1.28rem);
        font-weight: 800;
        letter-spacing: -.02em;
        line-height: 1.28;
    }

    body.voter-home-page .article-copy time {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 10px;
        color: var(--articles-brand);
        font-size: .84rem;
        font-variant-numeric: tabular-nums;
        font-weight: 750;
    }

    body.voter-home-page .article-copy time::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
        content: "";
    }

    body.voter-home-page .article-excerpt {
        display: -webkit-box;
        overflow: hidden;
        margin-top: 12px;
        color: var(--articles-muted);
        font-size: .9rem;
        line-height: 1.55;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
    }

    body.voter-home-page .article-read-more {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: auto;
        padding-top: 16px;
        color: var(--articles-brand);
        font-size: .84rem;
        font-weight: 800;
    }

    body.voter-home-page .article-read-more .icon {
        width: 17px;
        height: 17px;
        transition: transform .2s ease;
    }

    body.voter-home-page .article-card:hover .article-read-more .icon {
        transform: translateX(4px);
    }

    body.voter-home-page #articles .empty-card {
        border-color: var(--articles-border);
        background: var(--articles-card);
        color: var(--articles-muted);
    }

    body.voter-home-page .faq-item {
        overflow: hidden;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    body.voter-home-page .faq-item[open] {
        border-color: color-mix(in srgb, var(--brand), transparent 62%);
        box-shadow: var(--sh);
    }

    body.voter-home-page .faq-item summary {
        transition: color .18s ease;
    }

    body.voter-home-page .faq-item summary:hover {
        color: var(--brand);
    }

    @keyframes home-intro {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes home-reveal {
        from { opacity: 0; transform: translateY(22px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @supports (animation-timeline: view()) and (animation-range: entry 0% cover 25%) {
        @media (prefers-reduced-motion: no-preference) {
            body.voter-home-page .action-card,
            body.voter-home-page .date-card,
            body.voter-home-page .election-banner,
            body.voter-home-page .event-card,
            body.voter-home-page .article-card,
            body.voter-home-page .faq-item,
            body.voter-home-page .guidelines-card,
            body.voter-home-page .empty-card {
                animation: home-reveal auto linear both;
                animation-timeline: view();
                animation-range: entry 0% cover 25%;
            }
        }
    }

    @media (max-width: 639px) {
        body.voter-home-page #how-it-works .action-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        body.voter-home-page .election-banner {
            min-height: 0;
            align-items: flex-start;
            flex-direction: column;
            gap: 16px;
        }

        body.voter-home-page .election-banner img {
            width: min(100%, 260px);
            max-height: 96px;
            object-position: left center;
        }

        body.voter-home-page #events .section-heading {
            gap: 14px;
        }

        body.voter-home-page #events .carousel-controls {
            flex: none;
        }

        body.voter-home-page #events .carousel-controls .icon-button {
            width: 42px;
            height: 42px;
        }

        body.voter-home-page .event-card {
            min-height: 0;
        }

        body.voter-home-page .event-image,
        body.voter-home-page .event-placeholder {
            height: 176px;
        }

        body.voter-home-page #how-it-works .action-card:nth-child(3) {
            grid-column: auto;
            width: auto;
        }

        body.voter-home-page .candidate-showcase-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        body.voter-home-page .candidate-live-badge {
            font-size: .75rem;
        }

        body.voter-home-page .candidate-carousel-stage {
            border-radius: 22px;
        }

        body.voter-home-page .candidate-slide {
            min-height: 0;
            grid-template-columns: minmax(0, 1fr);
            gap: 18px;
            padding: 28px 22px 24px;
        }

        body.voter-home-page .candidate-slide-copy {
            order: 2;
        }

        body.voter-home-page .candidate-slide-portrait {
            min-height: 0;
            order: 1;
            padding: 12px 0 4px;
        }

        body.voter-home-page .candidate-slide-portrait::before {
            width: min(70vw, 250px);
        }

        body.voter-home-page .candidate-slide-portrait img {
            width: clamp(150px, 45vw, 220px);
            border-width: 6px;
        }

        body.voter-home-page .candidate-slide h3 {
            max-width: 100%;
            font-size: clamp(2rem, 10vw, 3rem);
        }

        body.voter-home-page .candidate-vote-share {
            margin-top: 24px;
        }

        body.voter-home-page .candidate-carousel-controls {
            align-items: flex-start;
            flex-wrap: wrap;
        }

        body.voter-home-page .candidate-carousel-actions {
            order: 2;
            width: 100%;
        }

        body.voter-home-page .candidate-carousel-pagination {
            min-height: 42px;
            margin-inline: auto;
        }

        body.voter-home-page .hero-inner {
            gap: 30px;
            padding-block: 44px 50px;
        }

        body.voter-home-page .hero h2 {
            max-width: 11ch;
            font-size: clamp(2.65rem, 12vw, 4rem);
        }

        body.voter-home-page .hero-lead {
            max-width: 38ch;
            margin-top: 16px;
        }

        body.voter-home-page .button-row {
            margin-top: 24px;
        }

        body.voter-home-page .button {
            min-height: 50px;
            padding-inline: 16px;
        }

        body.voter-home-page .steps-card {
            border-radius: 22px;
        }

        body.voter-home-page .section-heading {
            align-items: flex-start;
        }

        body.voter-home-page .action-card {
            min-height: 238px;
        }

        body.voter-home-page .date-card {
            min-height: 0;
            flex-direction: row;
            align-items: flex-start;
            gap: 14px;
            padding: 20px;
        }

        body.voter-home-page .date-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 12px;
        }

        body.voter-home-page .date-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 15px;
        }
    }

    @media (min-width: 640px) and (max-width: 859px) {
        body.voter-home-page #how-it-works .action-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        body.voter-home-page #how-it-works .action-card:nth-child(3) {
            grid-column: 1 / -1;
            width: calc(50% - 8px);
            justify-self: center;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        body.voter-home-page .candidate-slide {
            animation: candidate-slide-reduced .18s ease-out both !important;
        }

        body.voter-home-page .candidate-slide *,
        body.voter-home-page .candidate-slide::before,
        body.voter-home-page .candidate-slide::after {
            animation: none !important;
        }

        body.voter-home-page .candidate-vote-fill,
        body.voter-home-page .candidate-carousel-button,
        body.voter-home-page .candidate-carousel-dot,
        body.voter-home-page #events .carousel-controls .icon-button,
        body.voter-home-page #articles .carousel-controls .icon-button,
        body.voter-home-page .event-card,
        body.voter-home-page .event-image,
        body.voter-home-page .event-card-hint .icon,
        body.voter-home-page .article-card,
        body.voter-home-page .article-image,
        body.voter-home-page .article-read-more .icon,
        body.voter-home-page .action-card,
        body.voter-home-page .date-card,
        body.voter-home-page .action-card::after,
        body.voter-home-page .action-icon,
        body.voter-home-page .action-link,
        body.voter-home-page .action-link .icon {
            transition: none !important;
        }

        body.voter-home-page .hero-copy,
        body.voter-home-page .steps-card,
        body.voter-home-page .action-card,
        body.voter-home-page .date-card,
        body.voter-home-page .election-banner,
        body.voter-home-page .event-card,
        body.voter-home-page .article-card,
        body.voter-home-page .faq-item,
        body.voter-home-page .guidelines-card,
        body.voter-home-page .empty-card {
            animation: none !important;
        }
    }

    @keyframes candidate-slide-reduced {
        from { opacity: .82; }
        to { opacity: 1; }
    }
</style>
@endpush

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

<section class="candidate-showcase" aria-labelledby="candidate-showcase-title">
    <div class="page-wrap candidate-showcase-inner">
        <div class="candidate-showcase-heading">
            <div>
                <p class="candidate-showcase-kicker">MEET THE CANDIDATES</p>
                <h1 id="candidate-showcase-title">Your next student leaders</h1>
                <p>See who's running and how each candidate is doing. Vote shares refresh automatically as votes are recorded.</p>
            </div>
            <span class="candidate-live-badge">Live vote share</span>
        </div>

        <div class="candidate-carousel" data-candidate-carousel data-tallies-url="{{ route('candidate.tallies') }}" role="region" aria-roledescription="carousel" aria-label="Candidate vote results carousel">
            <div class="candidate-carousel-stage" data-candidate-stage tabindex="0" aria-label="Candidate slides">
                @forelse($candidates as $index => $candidate)
                    @php
                        $profileRelative = $candidate->profile_img
                            ? (\Illuminate\Support\Str::contains($candidate->profile_img, '/') ? $candidate->profile_img : ('profile-picture/' . $candidate->profile_img))
                            : null;
                        $bannerRelative = $candidate->banner_img
                            ? (\Illuminate\Support\Str::contains($candidate->banner_img, '/') ? $candidate->banner_img : ('banner/' . $candidate->banner_img))
                            : null;
                        $profileExists = $profileRelative && \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $profileRelative);
                        $bannerExists = $bannerRelative && \Illuminate\Support\Facades\Storage::disk('public')->exists('upload/' . $bannerRelative);
                        $profileUrl = $profileExists
                            ? route('storage.image', ['folder' => 'upload', 'filename' => $profileRelative])
                            : asset('images/default-profile.jpg');
                        $bannerUrl = $bannerExists
                            ? route('storage.image', ['folder' => 'upload', 'filename' => $bannerRelative])
                            : asset('images/default-banner.png');
                        $candidateVotes = (int) ($candidate->votes_count ?? 0);
                        $positionVotes = (int) ($candidate->position_votes_total ?? 0);
                        $candidateShare = $positionVotes > 0
                            ? round(($candidateVotes / $positionVotes) * 100, 1)
                            : 0;
                    @endphp
                    <article
                        class="candidate-slide"
                        data-candidate-slide
                        data-candidate-id="{{ $candidate->id }}"
                        role="group"
                        aria-roledescription="slide"
                        aria-label="{{ $index + 1 }} of {{ $candidates->count() }}"
                        @if($index > 0) hidden aria-hidden="true" @else aria-hidden="false" @endif
                    >
                        <img
                            class="candidate-slide-backdrop"
                            src="{{ $bannerUrl }}"
                            alt=""
                            aria-hidden="true"
                            loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                            onerror="this.onerror=null;this.src='{{ asset('images/default-banner.png') }}';"
                        >
                        <div class="candidate-slide-copy">
                            <span class="candidate-slide-overline">Candidate {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="candidate-slide-position">Running for {{ optional($candidate->position)->name ?? 'Student representative' }}</span>
                            <h3>{{ $candidate->name }}</h3>
                            <div class="candidate-vote-share">
                                <span>Share of votes for this position</span>
                                <strong>{{ number_format($candidateShare, 1) }}%</strong>
                            </div>
                            <div
                                class="candidate-vote-track"
                                role="progressbar"
                                aria-label="Vote share for {{ $candidate->name }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                                aria-valuenow="{{ $candidateShare }}"
                                style="--vote-share: {{ $candidateShare }}%;"
                            >
                                <span class="candidate-vote-fill"></span>
                            </div>
                            <p class="candidate-vote-note">
                                {{ $positionVotes > 0 ? number_format($candidateVotes) . ' of ' . number_format($positionVotes) . ' votes for this position' : 'No votes recorded for this position yet.' }}
                            </p>
                        </div>
                        <div class="candidate-slide-portrait">
                            <img
                                src="{{ $profileUrl }}"
                                alt="{{ $candidate->name }}"
                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                onerror="this.onerror=null;this.src='{{ asset('images/default-profile.jpg') }}';"
                            >
                        </div>
                    </article>
                @empty
                    <div class="candidate-carousel-empty">
                        <h3>Candidate results will appear here</h3>
                        <p>There are no active candidates to display right now.</p>
                    </div>
                @endforelse
            </div>

            @if($candidates->count() > 1)
                <div class="candidate-carousel-controls" data-candidate-controls>
                    <div class="candidate-carousel-actions">
                        <button class="candidate-carousel-button" type="button" data-candidate-previous aria-label="Previous candidate">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
                            <span>Previous</span>
                        </button>
                        <button class="candidate-carousel-button" type="button" data-candidate-pause aria-pressed="false">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5v14M15 5v14"></path></svg>
                            <span data-candidate-pause-label>Pause</span>
                        </button>
                        <button class="candidate-carousel-button" type="button" data-candidate-next aria-label="Next candidate">
                            <span>Next</span>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
                        </button>
                    </div>
                    <div class="candidate-carousel-pagination" aria-label="Choose a candidate">
                        @foreach($candidates as $index => $candidate)
                            <button
                                class="candidate-carousel-dot"
                                type="button"
                                data-candidate-goto="{{ $index }}"
                                aria-label="Show {{ $candidate->name }}"
                                aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                            ></button>
                        @endforeach
                    </div>
                </div>
                <p class="visually-hidden" data-candidate-status aria-live="polite" aria-atomic="true"></p>
            @endif
        </div>
    </div>
</section>

<section class="hero">
    <div class="page-wrap hero-inner">
        <div class="hero-copy">
            <span class="hero-status {{ $eventStatusClass }}">{{ $eventStatusLabel }}</span>
            <h2>Vote for your student leaders</h2>
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
            <div>
                <p class="action-kicker">TAKE PART IN YOUR ELECTION</p>
                <h2>Choose your next step</h2>
                <p>Register to participate, cast your vote, or follow the results. Select an action to get started.</p>
            </div>
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
                <p class="dates-kicker">DATES TO SAVE</p>
                <h2>Election timeline</h2>
                @if($homeElection)
                    <p>Important dates for {{ $homeElection->name }}.</p>
                @else
                    <p>Keep track of the upcoming election schedule.</p>
                @endif
            </div>
        </div>
        @if(count($dateRows))
            <ol class="date-grid" aria-label="Election schedule">
                @foreach($dateRows as $dateIndex => [$dateTitle, $dateValue, $dateIcon])
                    <li class="date-card">
                        <span class="date-card-icon"><svg class="icon" aria-hidden="true"><use href="#{{ $dateIcon }}"/></svg></span>
                        <div class="date-card-copy">
                            <span class="date-card-phase">PHASE {{ str_pad((string) ($dateIndex + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <strong>{{ $dateTitle }}</strong>
                            <span>{{ $dateValue }}</span>
                        </div>
                    </li>
                @endforeach
            </ol>
        @else
            <div class="empty-card">Election dates have not been published yet.</div>
        @endif
    </div>
</section>

<section class="content-section" id="events">
    <div class="page-wrap">
        <div class="section-heading">
            <div>
                <p class="events-kicker">WHAT'S HAPPENING</p>
                <h2>News and events</h2>
                <p>{{ $eventsLabel }}</p>
            </div>
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
            <div class="event-grid" id="events-container" aria-label="Election news and events">
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
                            <span class="event-card-hint">View event details <svg class="icon" aria-hidden="true"><use href="#pcu-arrow"/></svg></span>
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
            <div><p class="articles-kicker">FROM THE ELECTION DESK</p><h2>Latest articles</h2><p>Election updates and announcements.</p></div>
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
        const candidateCarousel = document.querySelector('[data-candidate-carousel]');
        let eventIndex = 0;
        let articleIndex = 0;
        let previouslyFocused = null;

        if (candidateCarousel) {
            const candidateStage = candidateCarousel.querySelector('[data-candidate-stage]');
            const candidateSlides = [...candidateCarousel.querySelectorAll('[data-candidate-slide]')];
            const candidateDots = [...candidateCarousel.querySelectorAll('[data-candidate-goto]')];
            const candidateStatus = candidateCarousel.querySelector('[data-candidate-status]');
            const previousCandidate = candidateCarousel.querySelector('[data-candidate-previous]');
            const nextCandidate = candidateCarousel.querySelector('[data-candidate-next]');
            const pauseCandidate = candidateCarousel.querySelector('[data-candidate-pause]');
            const pauseCandidateLabel = candidateCarousel.querySelector('[data-candidate-pause-label]');
            const pauseCandidateIcon = pauseCandidate?.querySelector('path');
            let candidateIndex = 0;
            let candidateTimer = null;
            let pointerStartX = null;
            let pointerStartY = null;
            let pointerType = '';
            let userPaused = false;
            let tallyRefreshInFlight = false;

            async function refreshCandidateTallies() {
                if (!candidateSlides.length || document.hidden || tallyRefreshInFlight) return;
                tallyRefreshInFlight = true;
                try {
                    const response = await fetch(candidateCarousel.dataset.talliesUrl, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                        cache: 'no-store'
                    });
                    if (!response.ok) throw new Error(`Candidate tally request failed: ${response.status}`);
                    const result = await response.json();
                    if (!Array.isArray(result.candidates)) throw new Error('Candidate tally response is invalid');

                    const talliesById = new Map(result.candidates.map((candidate) => [String(candidate.id), candidate]));
                    const numberFormat = new Intl.NumberFormat();
                    candidateSlides.forEach((slide) => {
                        const tally = talliesById.get(slide.dataset.candidateId);
                        if (!tally) return;
                        const votes = Math.max(0, Number(tally.votes_count) || 0);
                        const total = Math.max(0, Number(tally.position_votes_total) || 0);
                        const share = total > 0 ? Math.min(100, Math.round((votes / total) * 1000) / 10) : 0;
                        const shareLabel = slide.querySelector('.candidate-vote-share strong');
                        const progress = slide.querySelector('[role="progressbar"]');
                        const note = slide.querySelector('.candidate-vote-note');
                        if (shareLabel) shareLabel.textContent = `${share.toFixed(1)}%`;
                        if (progress) {
                            progress.setAttribute('aria-valuenow', String(share));
                            progress.style.setProperty('--vote-share', `${share}%`);
                        }
                        if (note) {
                            note.textContent = total > 0
                                ? `${numberFormat.format(votes)} of ${numberFormat.format(total)} votes for this position`
                                : 'No votes recorded for this position yet.';
                        }
                    });
                } catch (error) {
                    console.error('Unable to refresh candidate vote tallies.', error);
                } finally {
                    tallyRefreshInFlight = false;
                }
            }

            function stopCandidateTimer() {
                if (candidateTimer) {
                    window.clearInterval(candidateTimer);
                    candidateTimer = null;
                }
            }

            function canAutoAdvance() {
                return candidateSlides.length > 1
                    && !userPaused
                    && !document.hidden;
            }

            function syncCandidateTimer() {
                stopCandidateTimer();
                if (!canAutoAdvance()) return;
                candidateTimer = window.setInterval(() => {
                    showCandidate(candidateIndex + 1, false);
                }, 5000);
            }

            function showCandidate(index, announce) {
                if (!candidateSlides.length) return;
                candidateIndex = (index + candidateSlides.length) % candidateSlides.length;
                candidateSlides.forEach((slide, slideIndex) => {
                    const isActive = slideIndex === candidateIndex;
                    slide.hidden = !isActive;
                    slide.setAttribute('aria-hidden', String(!isActive));
                });
                candidateDots.forEach((dot, dotIndex) => {
                    dot.setAttribute('aria-current', String(dotIndex === candidateIndex));
                });
                if (announce && candidateStatus) {
                    const activeSlide = candidateSlides[candidateIndex];
                    const name = activeSlide.querySelector('h3')?.textContent.trim() || 'Candidate';
                    const position = activeSlide.querySelector('.candidate-slide-position')?.textContent.trim() || '';
                    const share = activeSlide.querySelector('.candidate-vote-share strong')?.textContent.trim() || '';
                    candidateStatus.textContent = `${name}, ${position}, ${share} vote share`;
                }
            }

            function moveCandidate(direction) {
                showCandidate(candidateIndex + direction, true);
                syncCandidateTimer();
            }

            previousCandidate?.addEventListener('click', () => moveCandidate(-1));
            nextCandidate?.addEventListener('click', () => moveCandidate(1));
            candidateDots.forEach((dot) => {
                dot.addEventListener('click', () => {
                    showCandidate(Number(dot.dataset.candidateGoto), true);
                    syncCandidateTimer();
                });
            });
            pauseCandidate?.addEventListener('click', () => {
                userPaused = !userPaused;
                pauseCandidate.setAttribute('aria-pressed', String(userPaused));
                pauseCandidate.setAttribute('aria-label', userPaused ? 'Resume automatic carousel' : 'Pause automatic carousel');
                if (pauseCandidateIcon) {
                    pauseCandidateIcon.setAttribute('d', userPaused ? 'm8 5 12 7-12 7z' : 'M9 5v14M15 5v14');
                }
                if (pauseCandidateLabel) pauseCandidateLabel.textContent = userPaused ? 'Play' : 'Pause';
                syncCandidateTimer();
            });
            candidateStage?.addEventListener('keydown', (event) => {
                if (event.target !== candidateStage) return;
                if (event.key === 'ArrowLeft') {
                    event.preventDefault();
                    moveCandidate(-1);
                } else if (event.key === 'ArrowRight') {
                    event.preventDefault();
                    moveCandidate(1);
                }
            });
            candidateStage?.addEventListener('pointerdown', (event) => {
                pointerType = event.pointerType;
                pointerStartX = event.clientX;
                pointerStartY = event.clientY;
            });
            candidateStage?.addEventListener('pointerup', (event) => {
                if (pointerType !== 'touch' || pointerStartX === null || pointerStartY === null) return;
                const deltaX = event.clientX - pointerStartX;
                const deltaY = event.clientY - pointerStartY;
                if (Math.abs(deltaX) > 48 && Math.abs(deltaX) > Math.abs(deltaY)) {
                    moveCandidate(deltaX < 0 ? 1 : -1);
                }
                pointerStartX = null;
                pointerStartY = null;
                pointerType = '';
            });
            candidateStage?.addEventListener('pointercancel', () => {
                pointerStartX = null;
                pointerStartY = null;
                pointerType = '';
            });
            document.addEventListener('visibilitychange', syncCandidateTimer);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) refreshCandidateTallies();
            });
            showCandidate(0, false);
            syncCandidateTimer();
            window.setInterval(refreshCandidateTallies, 30000);
        }

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
            const target = event.target instanceof Element ? event.target : null;
            if (!target) return;

            const eventCard = target.closest('[data-event-card]');
            const articleCard = target.closest('[data-article-card]');
            if (eventCard) openEvent(eventCard);
            if (articleCard) openArticle(articleCard);
            if (target.closest('[data-close-article]')) setModalOpen(articleModal, false);
            if (target.closest('[data-close-event]')) setModalOpen(eventModal, false);
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

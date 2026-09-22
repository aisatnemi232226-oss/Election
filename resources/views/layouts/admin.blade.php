<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>University Online Election System - Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet"/>
    <link rel="icon" href="{{ URL::to('/images/pculogo.png') }}">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .tile { transition: transform 0.3s, box-shadow 0.3s; }
        .tile:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1); }
        .tile:active { transform: translateY(-2px); box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1); }

        /* Quill font mappings */
        .ql-font-roboto { font-family: 'Roboto', sans-serif; }
        .ql-font-arial { font-family: 'Arial', sans-serif; }
        .ql-font-georgia { font-family: 'Georgia', serif; }
        .ql-font-times { font-family: 'Times New Roman', serif; }
        .ql-font-verdana { font-family: 'Verdana', sans-serif; }
        .ql-font-courier { font-family: 'Courier New', monospace; }

        /* Quill size mappings */
        .ql-size-10px { font-size: 10px; }
        .ql-size-12px { font-size: 12px; }
        .ql-size-14px { font-size: 14px; }
        .ql-size-16px { font-size: 16px; }
        .ql-size-18px { font-size: 18px; }
        .ql-size-20px { font-size: 20px; }
        .ql-size-24px { font-size: 24px; }
        .ql-size-28px { font-size: 28px; }
        .ql-size-32px { font-size: 32px; }
        .ql-size-36px { font-size: 36px; }

        /* Quill toolbar labels */
        .ql-toolbar .ql-font .ql-picker-item[data-value="roboto"]::before { font-family: 'Roboto', sans-serif; content: "Roboto"; }
        .ql-toolbar .ql-font .ql-picker-item[data-value="arial"]::before { font-family: 'Arial', sans-serif; content: "Arial"; }
        .ql-toolbar .ql-font .ql-picker-item[data-value="georgia"]::before { font-family: 'Georgia', serif; content: "Georgia"; }
        .ql-toolbar .ql-font .ql-picker-item[data-value="times"]::before { font-family: 'Times New Roman', serif; content: "Times"; }
        .ql-toolbar .ql-font .ql-picker-item[data-value="verdana"]::before { font-family: 'Verdana', sans-serif; content: "Verdana"; }
        .ql-toolbar .ql-font .ql-picker-item[data-value="courier"]::before { font-family: 'Courier New', monospace; content: "Courier"; }

        .ql-toolbar .ql-size .ql-picker-item[data-value="10px"]::before { content: "10px"; }
        .ql-toolbar .ql-size .ql-picker-item[data-value="12px"]::before { content: "12px"; }
        .ql-toolbar .ql-size .ql-picker-item[data-value="14px"]::before { content: "14px"; }
        .ql-toolbar .ql-size .ql-picker-item[data-value="16px"]::before { content: "16px"; }
        .ql-toolbar .ql-size .ql-picker-item[data-value="18px"]::before { content: "18px"; }
        .ql-toolbar .ql-size .ql-picker-item[data-value="20px"]::before { content: "20px"; }
        .ql-toolbar .ql-size .ql-picker-item[data-value="24px"]::before { content: "24px"; }
        .ql-toolbar .ql-size .ql-picker-item[data-value="28px"]::before { content: "28px"; }
        .ql-toolbar .ql-size .ql-picker-item[data-value="32px"]::before { content: "32px"; }
        .ql-toolbar .ql-size .ql-picker-item[data-value="36px"]::before { content: "36px"; }

        .no-wrap { white-space: nowrap; }
        .ql-font-courier-new { font-family: 'Courier New', Courier, monospace; }
    </style>
</head>
<body class="relative flex flex-col min-h-screen bg-gray-100 pt-16">

    @include('layouts.admin-header')

   

    <div class="pointer-events-none fixed inset-0 flex items-center justify-center opacity-30 z-0" aria-hidden="true">
        <img src="{{ asset('images/pculogo.png') }}" alt="" class="w-[62vw] max-w-[820px] h-auto" />
    </div>

    <main class="relative z-10 flex-1 container mx-auto p-4">
        @yield('content')
    </main>

    @include('layouts.admin-footer')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (window.Quill && !window.__adminQuillRegistered) {
                try {
                    const Font = Quill.import('formats/font');
                    Font.whitelist = ['roboto', 'arial', 'georgia', 'times', 'verdana', 'courier'];
                    Quill.register(Font, true);

                    const SizeStyle = Quill.import('attributors/style/size');
                    SizeStyle.whitelist = ['10px', '12px', '14px', '16px', '18px', '20px', '24px', '28px', '32px', '36px'];
                    Quill.register(SizeStyle, true);
                } catch (e) {
                    console.error('Admin Quill setup failed', e);
                }
                window.__adminQuillRegistered = true;
            }

            function initAdminQuillTextareas() {
                if (!window.Quill) return;

                const toolbar = [
                    [{ 'font': ['roboto', 'arial', 'georgia', 'times', 'verdana', 'courier'] }, { 'size': ['10px', '12px', '14px', '16px', '18px', '20px', '24px', '28px', '32px', '36px'] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
                    ['link', 'blockquote', 'code-block'],
                    ['clean']
                ];

                // Standard textarea pattern
                document.querySelectorAll('textarea[data-quill="1"]:not([data-quill-bound])').forEach(textarea => {
                    textarea.setAttribute('data-quill-bound', '1');

                    const editorEl = document.createElement('div');
                    editorEl.className = textarea.className;
                    editorEl.style.minHeight = textarea.getAttribute('rows') ? '140px' : '160px';
                    editorEl.innerHTML = textarea.value || '';

                    textarea.style.display = 'none';
                    textarea.parentNode.insertBefore(editorEl, textarea.nextSibling);

                    const quill = new Quill(editorEl, {
                        theme: 'snow',
                        modules: { toolbar }
                    });

                    if (textarea.value) {
                        quill.root.innerHTML = textarea.value;
                    }

                    quill.on('text-change', () => {
                        textarea.value = quill.root.innerHTML;
                    });

                    const form = textarea.closest('form');
                    if (form && !form.hasAttribute('data-quill-form-bound')) {
                        form.setAttribute('data-quill-form-bound', '1');
                        form.addEventListener('submit', () => {
                            form.querySelectorAll('textarea[data-quill-bound="1"]').forEach(t => {
                                const sibling = t.nextSibling;
                                if (sibling && sibling.classList && sibling.classList.contains('ql-container')) {
                                    t.value = sibling.querySelector('.ql-editor')?.innerHTML || t.value;
                                }
                            });
                        });
                    }
                });

                // Candidates pattern: visible editor div + hidden input with data-quill="1"
                document.querySelectorAll('input[type="hidden"][data-quill="1"]:not([data-quill-bound])').forEach(hiddenInput => {
                    hiddenInput.setAttribute('data-quill-bound', '1');
                    const editorId = hiddenInput.id.replace(/-candidate-(description|platforms)/, '-$1-editor');
                    const editorEl = document.getElementById(editorId);
                    if (!editorEl) return;

                    const quill = new Quill(editorEl, {
                        theme: 'snow',
                        modules: { toolbar }
                    });

                    if (hiddenInput.value) {
                        quill.root.innerHTML = hiddenInput.value;
                    }

                    quill.on('text-change', () => {
                        hiddenInput.value = quill.root.innerHTML;
                    });

                    const form = hiddenInput.closest('form');
                    if (form && !form.hasAttribute('data-quill-form-bound')) {
                        form.setAttribute('data-quill-form-bound', '1');
                        form.addEventListener('submit', () => {
                            form.querySelectorAll('input[type="hidden"][data-quill-bound="1"]').forEach(t => {
                                const editorId = t.id.replace(/-candidate-(description|platforms)/, '-$1-editor');
                                const editorEl = document.getElementById(editorId);
                                if (editorEl && editorEl.__quill) {
                                    t.value = editorEl.__quill.root.innerHTML;
                                }
                            });
                        });
                    }

                    editorEl.__quill = quill;
                });
            }

            initAdminQuillTextareas();
            const observer = new MutationObserver(() => initAdminQuillTextareas());
            observer.observe(document.body, { childList: true, subtree: true });

            function setupDropdown(toggleId, menuId) {
                const toggle = document.getElementById(toggleId);
                const menu = document.getElementById(menuId);
                if (!toggle || !menu) return;

                let hoverTimeout;
                toggle.addEventListener("mouseenter", function () {
                    clearTimeout(hoverTimeout);
                    menu.classList.remove("hidden");
                });
                menu.addEventListener("mouseenter", function () {
                    clearTimeout(hoverTimeout);
                    menu.classList.remove("hidden");
                });
                toggle.addEventListener("mouseleave", function () {
                    hoverTimeout = setTimeout(() => {
                        if (!menu.matches(":hover")) {
                            menu.classList.add("hidden");
                        }
                    }, 300);
                });
                menu.addEventListener("mouseleave", function () {
                    menu.classList.add("hidden");
                });
            }

            setupDropdown("election-management-toggle", "election-management-menu");
            setupDropdown("settings-toggle", "settings-menu");

            const menuToggle = document.getElementById("menu-toggle");
            const menu = document.getElementById("menu");
            if (menuToggle && menu) {
                menuToggle.addEventListener("click", () => {
                    menu.classList.toggle("hidden");
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

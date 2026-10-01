(() => {
    const root = document.documentElement;
    const themeButton = document.querySelector('[data-theme-toggle]');

    function setTheme(theme, persist) {
        const normalizedTheme = theme === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-theme', normalizedTheme);
        themeButton?.setAttribute('aria-label', normalizedTheme === 'dark' ? 'Switch to light theme' : 'Switch to dark theme');
        if (persist) {
            try {
                localStorage.setItem('pcu-theme', normalizedTheme);
            } catch (error) {
                console.warn('Unable to save the selected theme.', error);
            }
        }
    }

    setTheme(root.getAttribute('data-theme'), false);
    themeButton?.addEventListener('click', () => {
        setTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark', true);
    });

    const profileToggle = document.getElementById('profile-toggle');
    const profileMenu = document.getElementById('profile-menu');
    const mobileToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const helpToggle = document.getElementById('chatbot-toggle');
    const helpPanel = document.getElementById('chatbot-panel');
    const helpClose = document.getElementById('chatbot-close');

    function setProfileOpen(open) {
        if (!profileToggle || !profileMenu) return;
        profileMenu.classList.toggle('is-open', open);
        profileToggle.setAttribute('aria-expanded', String(open));
        profileToggle.setAttribute('aria-label', open ? 'Close profile menu' : 'Open profile menu');
    }

    function setMobileOpen(open) {
        if (!mobileToggle || !mobileMenu) return;
        mobileMenu.classList.toggle('is-open', open);
        mobileToggle.setAttribute('aria-expanded', String(open));
        mobileToggle.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
    }

    function setHelpOpen(open) {
        if (!helpToggle || !helpPanel) return;
        helpPanel.hidden = !open;
        helpToggle.setAttribute('aria-expanded', String(open));
        if (open && document.getElementById('chatbot-input')) {
            document.getElementById('chatbot-input').focus();
        }
    }

    profileToggle?.addEventListener('click', (event) => {
        event.stopPropagation();
        setProfileOpen(profileToggle.getAttribute('aria-expanded') !== 'true');
    });
    profileMenu?.addEventListener('click', (event) => event.stopPropagation());
    mobileToggle?.addEventListener('click', () => setMobileOpen(mobileToggle.getAttribute('aria-expanded') !== 'true'));
    helpToggle?.addEventListener('click', (event) => {
        if (document.getElementById('chatbot-form')) return;
        event.stopPropagation();
        setHelpOpen(helpPanel.hidden);
    });
    helpPanel?.addEventListener('click', (event) => event.stopPropagation());
    helpClose?.addEventListener('click', () => setHelpOpen(false));

    document.addEventListener('click', (event) => {
        if (profileMenu && !profileMenu.contains(event.target) && !profileToggle?.contains(event.target)) {
            setProfileOpen(false);
        }
        if (helpPanel && !helpPanel.contains(event.target) && !helpToggle?.contains(event.target)) {
            setHelpOpen(false);
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const profileWasOpen = profileToggle?.getAttribute('aria-expanded') === 'true';
        const helpWasOpen = helpToggle?.getAttribute('aria-expanded') === 'true';
        setProfileOpen(false);
        setMobileOpen(false);
        setHelpOpen(false);
        if (profileWasOpen) profileToggle.focus();
        else if (helpWasOpen) helpToggle.focus();
    });
})();

(() => {
    const root = document.documentElement;
    const themeButtons = Array.from(document.querySelectorAll('[data-theme-toggle]'));
    let themeTransitionTimeout;

    function setTheme(theme, persist) {
        const normalizedTheme = theme === 'dark' ? 'dark' : 'light';
        root.setAttribute('data-theme', normalizedTheme);
        themeButtons.forEach((themeButton) => {
            themeButton.setAttribute('aria-label', normalizedTheme === 'dark' ? 'Switch to light theme' : 'Switch to dark theme');
            themeButton.setAttribute('aria-pressed', String(normalizedTheme === 'dark'));
        });
        if (persist) {
            try {
                localStorage.setItem('pcu-theme', normalizedTheme);
            } catch (error) {
                console.warn('Unable to save the selected theme.', error);
            }
        }
    }

    setTheme(root.getAttribute('data-theme'), false);
    themeButtons.forEach((themeButton) => {
        themeButton.addEventListener('click', () => {
            root.classList.add('theme-transitioning');
            themeButtons.forEach((button) => button.classList.remove('is-switching'));
            void themeButton.offsetWidth;
            themeButton.classList.add('is-switching');

            window.clearTimeout(themeTransitionTimeout);
            themeTransitionTimeout = window.setTimeout(() => {
                root.classList.remove('theme-transitioning');
                themeButtons.forEach((button) => button.classList.remove('is-switching'));
            }, 650);

            setTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark', true);
        });
    });

    const profileToggle = document.getElementById('profile-toggle');
    const profileMenu = document.getElementById('profile-menu');
    const mobileToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const helpToggle = document.getElementById('chatbot-toggle');
    const helpPanel = document.getElementById('chatbot-panel');
    const helpClose = document.getElementById('chatbot-close');
    const chatbotForm = document.getElementById('chatbot-form');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const chatbotQuickReplies = document.getElementById('chatbot-quick-replies');
    const chatbotSuggestionsToggle = document.getElementById('chatbot-suggestions-toggle');
    const chatbotSend = document.getElementById('chatbot-send');
    const chatbotCsrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let quickReplies = [
        'How do I log in?',
        'How do I vote?',
        'How do I register as a candidate?',
        'Where can I see results?'
    ];
    let chatbotInitialized = false;
    let chatbotSending = false;

    function setSuggestionsExpanded(expanded) {
        if (!chatbotQuickReplies || !chatbotSuggestionsToggle) return;
        chatbotQuickReplies.hidden = !expanded;
        chatbotSuggestionsToggle.setAttribute('aria-expanded', String(expanded));
        chatbotSuggestionsToggle.textContent = expanded ? 'Hide suggestions' : 'Show suggestions';
    }

    chatbotSuggestionsToggle?.addEventListener('click', () => {
        setSuggestionsExpanded(chatbotQuickReplies?.hidden ?? true);
    });

    function appendChatMessage(role, text, extraClass = '') {
        if (!chatbotMessages) return null;
        const message = document.createElement('div');
        message.className = `chat-message chat-message-${role}${extraClass ? ` ${extraClass}` : ''}`;
        message.dataset.chatRole = role;
        message.textContent = text;
        chatbotMessages.append(message);
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        return message;
    }

    function renderQuickReplies(replies = quickReplies) {
        if (!chatbotQuickReplies) return;
        const validReplies = Array.isArray(replies)
            ? replies.filter((reply) => typeof reply === 'string' && reply.trim()).slice(0, 6)
            : [];
        if (validReplies.length) quickReplies = validReplies;
        chatbotQuickReplies.replaceChildren();
        quickReplies.forEach((reply) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'chat-quick';
            button.textContent = reply;
            button.disabled = chatbotSending;
            button.addEventListener('click', () => {
                if (chatbotSending || !chatbotInput || !chatbotForm) return;
                chatbotInput.value = reply;
                chatbotForm.requestSubmit();
            });
            chatbotQuickReplies.append(button);
        });
    }

    function initializeChatbot() {
        if (chatbotInitialized || !chatbotForm || !chatbotMessages || !chatbotQuickReplies) return;
        chatbotInitialized = true;
        const welcome = chatbotForm.dataset.aiEnabled === 'true'
            ? "Hello! I'm the PCU Assistant. Ask me about signing in, registration, voter verification, voting, or election results."
            : "Hello! I'm the PCU Assistant. AI replies aren't configured yet, but I can answer common PCU election questions.";
        appendChatMessage('bot', welcome);
        renderQuickReplies();
    }

    function getChatbotHistory() {
        if (!chatbotMessages) return [];
        return [...chatbotMessages.querySelectorAll('[data-chat-role]')]
            .filter((item) => !item.classList.contains('chat-message-pending'))
            .slice(-8)
            .map((item) => ({
                role: item.dataset.chatRole === 'bot' ? 'assistant' : item.dataset.chatRole,
                content: item.textContent.trim()
            }));
    }

    function chatbotRequestError(status, data, malformedResponse = false) {
        if (malformedResponse) return 'The assistant returned an unreadable response. Please try again.';
        if (status === 419) return 'Your session expired. Refresh the page, then send your message again.';
        if (status === 401) return 'Your session is no longer active. Please sign in again.';
        if (status === 403) return 'You are not allowed to send this request. Refresh the page and try again.';
        if (status === 422) return data?.errors?.message?.[0] || 'Please check your message and try again.';
        if (status === 429) return 'Too many messages were sent too quickly. Please wait a moment before trying again.';
        if (status >= 500) return 'The help service is temporarily unavailable. Please try again in a moment.';
        return 'The assistant could not process that message. Please try again.';
    }

    async function sendChatbotMessage(message, history) {
        const response = await fetch(chatbotForm.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': chatbotCsrf
            },
            credentials: 'same-origin',
            body: JSON.stringify({ message, history })
        });

        const contentType = response.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            throw Object.assign(new Error(chatbotRequestError(response.status, null, response.ok)), { status: response.status });
        }
        const data = await response.json().catch(() => null);
        if (!data || typeof data !== 'object') {
            throw Object.assign(new Error(chatbotRequestError(response.status, null, true)), { status: response.status });
        }
        if (!response.ok) {
            throw Object.assign(new Error(chatbotRequestError(response.status, data)), { status: response.status });
        }
        if (typeof data?.reply !== 'string' || !data.reply.trim()) {
            throw Object.assign(new Error(chatbotRequestError(response.status, null, true)), { status: response.status });
        }

        return data;
    }

    chatbotForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (chatbotSending || !chatbotInput) return;

        const message = chatbotInput.value.trim();
        if (!message) {
            chatbotInput.focus();
            return;
        }

        initializeChatbot();
        const history = getChatbotHistory();
        chatbotSending = true;
        chatbotForm.setAttribute('aria-busy', 'true');
        if (chatbotSend) chatbotSend.disabled = true;
        chatbotInput.disabled = true;
        renderQuickReplies();
        const sentMessage = appendChatMessage('user', message);
        const pendingMessage = appendChatMessage('bot', 'Thinking...', 'chat-message-pending');
        chatbotInput.value = '';

        try {
            const data = await sendChatbotMessage(message, history);
            pendingMessage?.remove();
            appendChatMessage('bot', data.reply);
            if (typeof data.notice === 'string' && data.notice.trim()) {
                appendChatMessage('bot', data.notice, 'chat-message-notice');
            }
            renderQuickReplies(data.quick_replies);
        } catch (error) {
            const userMessage = error instanceof TypeError
                ? 'Unable to connect to the help service. Check your connection and try again.'
                : error instanceof Error
                    ? error.message
                    : 'The help service is temporarily unavailable. Please try again.';
            console.warn('Unable to send a message to the PCU Assistant.', {
                status: Number.isInteger(error?.status) ? error.status : null,
                errorType: error?.name || 'UnknownError'
            });
            pendingMessage?.remove();
            sentMessage?.remove();
            appendChatMessage('bot', userMessage);
            chatbotInput.value = message;
        } finally {
            chatbotSending = false;
            chatbotForm.removeAttribute('aria-busy');
            chatbotInput.disabled = false;
            if (chatbotSend) chatbotSend.disabled = false;
            renderQuickReplies();
            if (!helpPanel?.hidden) chatbotInput.focus();
        }
    });

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
        if (open && chatbotForm) {
            initializeChatbot();
            chatbotInput?.focus();
        }
    }

    profileToggle?.addEventListener('click', (event) => {
        event.stopPropagation();
        setProfileOpen(profileToggle.getAttribute('aria-expanded') !== 'true');
    });
    profileMenu?.addEventListener('click', (event) => event.stopPropagation());
    mobileToggle?.addEventListener('click', () => setMobileOpen(mobileToggle.getAttribute('aria-expanded') !== 'true'));
    mobileMenu?.addEventListener('click', (event) => {
        if (event.target.closest('a')) setMobileOpen(false);
    });
    window.matchMedia('(min-width: 900px)').addEventListener('change', (event) => {
        if (event.matches) setMobileOpen(false);
    });

    const siteHeader = document.querySelector('.site-header');
    let scrollUpdateScheduled = false;
    function updateHeaderScrollState() {
        if (!siteHeader) return;
        siteHeader.classList.toggle('is-scrolled', window.scrollY > 8);
        scrollUpdateScheduled = false;
    }
    window.addEventListener('scroll', () => {
        if (scrollUpdateScheduled) return;
        scrollUpdateScheduled = true;
        window.requestAnimationFrame(updateHeaderScrollState);
    }, { passive: true });
    updateHeaderScrollState();
    helpToggle?.addEventListener('click', (event) => {
        event.stopPropagation();
        setHelpOpen(helpPanel.hidden);
    });
    helpPanel?.addEventListener('click', (event) => event.stopPropagation());
    helpClose?.addEventListener('click', () => {
        setHelpOpen(false);
        helpToggle?.focus();
    });

    document.addEventListener('click', (event) => {
        if (profileMenu && !profileMenu.contains(event.target) && !profileToggle?.contains(event.target)) {
            setProfileOpen(false);
        }
        if (mobileMenu && !mobileMenu.contains(event.target) && !mobileToggle?.contains(event.target)) {
            setMobileOpen(false);
        }
        if (helpPanel && !helpPanel.contains(event.target) && !helpToggle?.contains(event.target)) {
            setHelpOpen(false);
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const profileWasOpen = profileToggle?.getAttribute('aria-expanded') === 'true';
        const mobileWasOpen = mobileToggle?.getAttribute('aria-expanded') === 'true';
        const helpWasOpen = helpToggle?.getAttribute('aria-expanded') === 'true';
        setProfileOpen(false);
        setMobileOpen(false);
        setHelpOpen(false);
        if (profileWasOpen) profileToggle.focus();
        else if (mobileWasOpen) mobileToggle.focus();
        else if (helpWasOpen) helpToggle.focus();
    });
})();

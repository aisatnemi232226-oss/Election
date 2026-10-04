<div class="help-widget" id="chatbot-root">
    <div class="help-panel" id="chatbot-panel" role="dialog" aria-labelledby="chatbot-title" aria-modal="false" hidden>
        <div class="help-panel-head">
            <div>
                <strong id="chatbot-title">PCU Assistant</strong>
                <span>Ask how to register, vote, or view results</span>
            </div>
            <button class="icon-button" id="chatbot-close" type="button" aria-label="Close help">
                <svg class="icon" aria-hidden="true"><use href="#pcu-x"/></svg>
            </button>
        </div>
        <p class="chat-privacy-note">
            Never share passwords, verification codes, ballot selections, or sensitive student information.
        </p>
        <div class="chat-messages" id="chatbot-messages" role="log" aria-live="polite" aria-relevant="additions" aria-atomic="false"></div>
        <button class="chat-suggestions-toggle" id="chatbot-suggestions-toggle" type="button" aria-controls="chatbot-quick-replies" aria-expanded="false">Show suggestions</button>
        <div class="chat-quick-replies" id="chatbot-quick-replies" aria-label="Suggested questions" hidden></div>
        <form class="chat-form" id="chatbot-form" action="{{ route('chatbot.respond') }}" method="post" data-ai-enabled="{{ config('services.chatbot.ai_enabled') ? 'true' : 'false' }}">
            @csrf
            <label class="visually-hidden" for="chatbot-input">Ask the PCU Assistant</label>
            <input id="chatbot-input" name="message" type="text" placeholder="Type a question..." autocomplete="off" maxlength="1000" required>
            <button class="button button-primary" id="chatbot-send" type="submit">Send</button>
        </form>
    </div>
    <button class="help-fab" id="chatbot-toggle" type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="chatbot-panel" aria-label="Need help?">
        <svg class="icon" aria-hidden="true"><use href="#pcu-chat"/></svg>
        <span>Need help?</span>
    </button>
</div>

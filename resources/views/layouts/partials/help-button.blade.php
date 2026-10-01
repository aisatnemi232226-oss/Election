<div class="help-widget" id="chatbot-root">
    <div class="help-panel" id="chatbot-panel" role="dialog" aria-label="PCU Assistant" aria-modal="false" hidden>
        <div class="help-panel-head">
            <div>
                <strong>PCU Assistant</strong>
                <span>Ask how to register, vote, or view results</span>
            </div>
            <button class="icon-button" id="chatbot-close" type="button" aria-label="Close help">
                <svg class="icon" aria-hidden="true"><use href="#pcu-x"/></svg>
            </button>
        </div>
        @guest
            <div class="chat-messages" id="chatbot-messages" aria-live="polite"></div>
            <div class="chat-quick-replies" id="chatbot-quick-replies"></div>
            <form class="chat-form" id="chatbot-form">
                <label class="visually-hidden" for="chatbot-input">Ask the PCU Assistant</label>
                <input id="chatbot-input" type="text" placeholder="Type a question..." autocomplete="off">
                <button class="button button-primary" id="chatbot-send" type="submit">Send</button>
            </form>
        @else
            <p>Questions about registering or voting? Contact the election office.</p>
            <a class="text-link" href="{{ url('/contact') }}">Contact us</a>
        @endguest
    </div>
    <button class="help-fab" id="chatbot-toggle" type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="chatbot-panel" aria-label="Need help?">
        <svg class="icon" aria-hidden="true"><use href="#pcu-chat"/></svg>
        <span>Need help?</span>
    </button>
</div>

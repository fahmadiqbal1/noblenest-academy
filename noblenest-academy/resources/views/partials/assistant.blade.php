<div class="modal fade" id="assistantModal" tabindex="-1" aria-labelledby="assistantLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content assistant-shell border-0 overflow-hidden">
            <div class="assistant-shell__glow"></div>
            <div class="modal-header border-0 pb-0">
                <div>
                    <div class="assistant-eyebrow">
                        <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest logo" style="width:22px;height:22px;border-radius:0.45rem;">
                        AI Companion
                    </div>
                    <h5 class="modal-title fw-semibold" id="assistantLabel">{{ I18n::get('ai_onboarding_assistant') }}</h5>
                    <p class="text-muted mb-0 small">Ask for weekly plans, activity ideas, onboarding help, or curriculum suggestions.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="assistant-chat" id="assistant-chat"></div>
                <form id="assistant-form" class="assistant-form mt-3">
                    <div class="assistant-form__field">
                        <i class="bi bi-stars"></i>
                        <input type="text" id="assistant-input" class="form-control border-0 shadow-none" placeholder="{{ I18n::get('ask_ai_placeholder') }}" autocomplete="off" required>
                    </div>
                    <button type="submit" class="btn btn-primary assistant-send">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </form>
                <div class="assistant-suggestions mt-3" id="assistant-suggestions"></div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const chat = document.getElementById('assistant-chat');
    const form = document.getElementById('assistant-form');
    const input = document.getElementById('assistant-input');
    const suggestions = document.getElementById('assistant-suggestions');
    const typingIndicator = document.getElementById('ai-typing-indicator');

    if (!chat || !form || !input || !suggestions) {
        return;
    }

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value;
        return div.innerHTML;
    }

    function appendMessage(message, sender, provider) {
        const wrapper = document.createElement('div');
        wrapper.className = 'assistant-message ' + (sender === 'user' ? 'assistant-message--user' : 'assistant-message--ai');

        const providerBadge = provider && sender !== 'user'
            ? '<div class="assistant-message__meta">' + escapeHtml(provider) + '</div>'
            : '';

        wrapper.innerHTML = [
            '<div class="assistant-message__bubble">',
            providerBadge,
            '<div>' + escapeHtml(message) + '</div>',
            '</div>'
        ].join('');

        chat.appendChild(wrapper);
        chat.scrollTop = chat.scrollHeight;
    }

    function setSuggestions(items) {
        suggestions.innerHTML = (items || []).map((item) => {
            return '<button type="button" class="btn btn-sm btn-outline-secondary assistant-chip">' + escapeHtml(item) + '</button>';
        }).join('');

        suggestions.querySelectorAll('button').forEach((button) => {
            button.addEventListener('click', function () {
                input.value = this.textContent.trim();
                form.requestSubmit();
            });
        });
    }

    function toggleTyping(show) {
        if (!typingIndicator) {
            return;
        }

        typingIndicator.classList.toggle('d-none', !show);
    }

    appendMessage('Hello. Tell me your child\'s age, goals, or preferred language and I\'ll build something practical.', 'ai', 'Noble Nest');

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const message = input.value.trim();
        if (!message) {
            return;
        }

        appendMessage(message, 'user');
        input.value = '';
        toggleTyping(true);

        fetch('/ai/assistant/message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ message }),
        })
            .then((response) => response.json())
            .then((data) => {
                appendMessage(data.reply || 'I could not produce a response.', 'ai', data.provider || 'AI');
                setSuggestions(data.suggestions || []);
            })
            .catch(() => {
                appendMessage('The assistant is temporarily unavailable. Please try again in a moment.', 'ai', 'System');
            })
            .finally(() => toggleTyping(false));
    });

    window.openAIModal = function () {
        const modalElement = document.getElementById('assistantModal');
        if (!modalElement || !window.bootstrap) {
            return;
        }

        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
    };
})();
</script>
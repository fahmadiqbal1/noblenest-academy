<div class="fixed inset-0 z-50 hidden" id="assistantModal" tabindex="-1" aria-labelledby="assistantLabel" aria-hidden="true">
    <div class="relative w-full max-w-lg mx-auto mt-12 flex items-center min-h-full max-h-[90vh] overflow-y-auto max-w-2xl">
        <div class="bg-white rounded-xl shadow-xl border border-gray-200 assistant-shell border-0 overflow-hidden">
            <div class="assistant-shell__glow"></div>
            <div class="px-5 py-3 border-b border-gray-200 font-semibold flex items-center justify-between border-0 pb-0">
                <div>
                    <div class="assistant-eyebrow">
                        <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest logo" style="width:22px;height:22px;border-radius:0.45rem;">
                        AI Companion
                    </div>
                    <h5 class="text-lg font-bold font-semibold" id="assistantLabel">{{ I18n::get('ai_onboarding_assistant') }}</h5>
                    <p class="text-[var(--color-text-muted)] mb-0 text-sm">Ask for weekly plans, activity ideas, onboarding help, or curriculum suggestions.</p>
                </div>
                <button type="button" class="" aria-label="Close"></button>
            </div>
            <div class="p-5 pt-3">
                <div class="assistant-chat" id="assistant-chat"></div>
                <form id="assistant-form" class="assistant-form mt-3">
                    <div class="assistant-form__field">
                        <x-ui.icon name="sparkles" />
                        <input type="text" id="assistant-input" class="block w-full px-3 py-2 rounded-md border border-gray-300 bg-white focus:border-violet-500 focus:ring-2 focus:ring-violet-200 focus:outline-none disabled:bg-gray-50 disabled:text-gray-500 border-0 shadow-none" placeholder="{{ I18n::get('ask_ai_placeholder') }}" autocomplete="off" required>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed bg-violet-600 text-white hover:bg-violet-700 assistant-send">
                        <x-ui.icon name="send" />
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
            return '<button type="button" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg font-semibold transition focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 disabled:opacity-50 disabled:cursor-not-allowed px-3 py-1.5 text-sm border-2 border-gray-300 text-gray-700 hover:bg-gray-100 assistant-chip">' + escapeHtml(item) + '</button>';
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
@if(!request()->is('profile'))
<div id="ai-assistant-bubble" class="fixed bottom-24 right-5 z-50 sm:bottom-6">
    <div class="flex items-end gap-2">
        {{-- Speech bubble hint --}}
        <div
            id="ai-bubble-msg"
            class="hidden max-w-[260px] rounded-[var(--radius-card)] border-[3px] border-[var(--color-border)] bg-[var(--color-surface-strong)] shadow-[var(--shadow-clay)] p-3 text-sm font-semibold text-[var(--color-text)]"
            style="animation: bounceIn 0.5s ease;"
        >
            <img
                src="{{ asset('brand/noblenest-logo.svg') }}"
                alt="NobleNest logo"
                class="inline-block w-7 h-7 rounded-[0.5rem] mr-2 align-middle"
                loading="lazy"
            >
            {{ App\Helpers\I18n::get('ai_bubble_hello') ?? 'Hi! Need help?' }}
        </div>

        {{-- Launch button --}}
        <button
            type="button"
            onclick="openAIModal()"
            class="relative w-16 h-16 rounded-[var(--radius-sm)] flex items-center justify-center bg-gradient-to-br from-[var(--color-brand-600)] to-[var(--color-brand-400)] shadow-[var(--shadow-clay)] transition-transform duration-[var(--duration-base)] hover:scale-105 active:scale-95 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2"
            aria-label="Open AI assistant"
        >
            <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest assistant" class="w-9 h-9">
            <span
                id="ai-typing-indicator"
                class="absolute -top-1 -right-1 hidden w-5 h-5 rounded-full bg-[var(--color-info)] text-white text-[10px] font-bold flex items-center justify-center"
                style="animation: aiTyping 1.2s infinite;"
            >...</span>
        </button>
    </div>
</div>

<style>
@keyframes bounceIn { 0%{transform:scale(0.7);} 60%{transform:scale(1.1);} 100%{transform:scale(1);} }
@keyframes aiTyping  { 0%,100%{opacity:0.2;} 50%{opacity:1;} }
</style>

<script>
window.addEventListener('DOMContentLoaded', function() {
    const bubble = document.getElementById('ai-bubble-msg');
    if (bubble) {
        setTimeout(() => bubble.classList.remove('hidden'), 1200);
        setTimeout(() => bubble.classList.add('hidden'), 5200);
    }
});
</script>
@endif

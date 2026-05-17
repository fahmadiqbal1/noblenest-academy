{{-- PWA install banner + service worker registration --}}
<div
    id="pwa-install-banner"
    class="hidden fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[min(92vw,500px)]"
>
    <div class="flex items-center gap-3 bg-[var(--color-surface-strong)] border-[3px] border-[var(--color-border)] rounded-[var(--radius-card)] shadow-[var(--shadow-clay)] p-4">
        <img
            src="{{ asset('brand/icon-72.png') }}"
            width="40" height="40"
            alt="NobleNest"
            class="rounded-xl shrink-0"
            onerror="this.style.display='none'"
            loading="lazy"
        >
        <div class="flex-1 min-w-0">
            <p class="text-sm font-bold text-[var(--color-text)]">{{ __('common.pwa_title') }}</p>
            <p class="text-xs text-[var(--color-text-muted)]">{{ __('common.pwa_sub') }}</p>
        </div>
        <x-ui.button id="pwa-install-btn" variant="primary" size="sm">{{ __('common.pwa_install') }}</x-ui.button>
        <button
            id="pwa-dismiss-btn"
            type="button"
            aria-label="Dismiss install prompt"
            class="text-[var(--color-text-muted)] hover:text-[var(--color-text)] p-1 rounded focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-1"
        >
            <x-ui.icon name="x" class="w-4 h-4" />
        </button>
    </div>
</div>

<script>
(function() {
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(function() {});
        });
    }

    let deferredPrompt = null;
    const banner     = document.getElementById('pwa-install-banner');
    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');
    if (!banner) return;

    const dismissed = localStorage.getItem('pwa_dismissed');
    if (dismissed && (Date.now() - parseInt(dismissed)) < 7 * 24 * 60 * 60 * 1000) return;
    if (window.matchMedia('(display-mode: standalone)').matches) return;

    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        deferredPrompt = e;
        banner.classList.remove('hidden');
    });

    installBtn && installBtn.addEventListener('click', async function() {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        await deferredPrompt.userChoice;
        deferredPrompt = null;
        banner.classList.add('hidden');
    });

    dismissBtn && dismissBtn.addEventListener('click', function() {
        localStorage.setItem('pwa_dismissed', Date.now().toString());
        banner.classList.add('hidden');
    });
})();
</script>

{{-- PWA Install Prompt + Service Worker Registration --}}
<div id="pwa-install-banner" class="pwa-banner d-none">
    <div class="pwa-banner__inner">
        <img src="{{ asset('brand/icon-72.png') }}" width="40" height="40" alt="NobleNest" class="rounded-2" onerror="this.style.display='none'">
        <div class="flex-grow-1">
            <div class="pwa-banner__title">Add NobleNest to Home Screen</div>
            <div class="pwa-banner__sub">One tap access, works offline</div>
        </div>
        <button id="pwa-install-btn" class="btn btn-sm btn-dark rounded-pill px-3 fw-semibold">Install</button>
        <button id="pwa-dismiss-btn" class="btn btn-sm btn-outline-secondary rounded-pill px-2" aria-label="Dismiss">✕</button>
    </div>
</div>

<style>
.pwa-banner { position: fixed; bottom: 1rem; left: 50%; transform: translateX(-50%); z-index: 9999; width: min(92vw, 500px); }
.pwa-banner__inner { display: flex; align-items: center; gap: 0.75rem; background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 0.75rem 1rem; box-shadow: 0 8px 32px rgba(0,0,0,0.14); }
.pwa-banner__title { font-size: 0.85rem; font-weight: 700; color: #111827; }
.pwa-banner__sub { font-size: 0.72rem; color: #6b7280; }
</style>

<script>
(function() {
    // Register service worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .catch(() => {});
        });
    }

    // PWA install prompt
    let deferredPrompt = null;
    const banner = document.getElementById('pwa-install-banner');
    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');

    if (!banner) return;

    // Don't show if dismissed in last 7 days
    const dismissed = localStorage.getItem('pwa_dismissed');
    if (dismissed && (Date.now() - parseInt(dismissed)) < 7 * 24 * 60 * 60 * 1000) return;

    // Don't show if already installed (standalone mode)
    if (window.matchMedia('(display-mode: standalone)').matches) return;

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        banner.classList.remove('d-none');
    });

    installBtn && installBtn.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        deferredPrompt = null;
        banner.classList.add('d-none');
    });

    dismissBtn && dismissBtn.addEventListener('click', () => {
        localStorage.setItem('pwa_dismissed', Date.now().toString());
        banner.classList.add('d-none');
    });
})();
</script>

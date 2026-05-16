{{-- Phase 8 — EU-compliant cookie consent banner.

    Three opt-ins: performance, analytics, marketing. All default OFF.
    Choices written to a `nn-cookie-consent` cookie (365-day TTL) so the
    banner does not reappear until preferences change or the cookie expires.
--}}
<div
    x-data="cookieBanner()"
    x-init="init()"
    x-show="visible"
    x-cloak
    role="dialog"
    aria-modal="false"
    aria-labelledby="nn-cookie-title"
    class="fixed inset-x-0 bottom-0 z-50 px-4 pb-4"
>
    <div class="max-w-3xl mx-auto rounded-2xl border-[3px] border-[var(--color-border)] bg-[var(--color-surface-strong)] shadow-[var(--shadow-clay)] p-5 space-y-3">
        <div class="flex items-start gap-3">
            <x-ui.icon name="info" class="w-5 h-5 mt-0.5 text-[var(--color-primary)]" />
            <div class="flex-1 space-y-2">
                <h2 id="nn-cookie-title" class="font-display font-bold text-[var(--color-text)]">
                    Your privacy choices
                </h2>
                <p class="text-sm text-[var(--color-text-muted)]">
                    We use only the cookies you choose. Essentials always run (login + cart);
                    everything else is opt-in.
                </p>
            </div>
        </div>

        <div class="grid gap-2 sm:grid-cols-3" x-show="showDetails" x-cloak>
            <label class="flex items-center gap-2 rounded-lg border p-2 text-sm cursor-pointer hover:bg-gray-50">
                <input type="checkbox" x-model="prefs.performance" class="w-4 h-4 rounded">
                <span><strong>Performance</strong><br><span class="text-xs text-[var(--color-text-muted)]">Speed + error logging.</span></span>
            </label>
            <label class="flex items-center gap-2 rounded-lg border p-2 text-sm cursor-pointer hover:bg-gray-50">
                <input type="checkbox" x-model="prefs.analytics" class="w-4 h-4 rounded">
                <span><strong>Analytics</strong><br><span class="text-xs text-[var(--color-text-muted)]">Anonymous usage stats.</span></span>
            </label>
            <label class="flex items-center gap-2 rounded-lg border p-2 text-sm cursor-pointer hover:bg-gray-50">
                <input type="checkbox" x-model="prefs.marketing" class="w-4 h-4 rounded">
                <span><strong>Marketing</strong><br><span class="text-xs text-[var(--color-text-muted)]">Email + retargeting.</span></span>
            </label>
        </div>

        <div class="flex flex-wrap gap-2 justify-end">
            <button type="button" @click="acceptOnly()"
                    class="text-sm px-4 py-2 rounded-md text-[var(--color-text-muted)] hover:bg-gray-100">
                Essential only
            </button>
            <button type="button" @click="showDetails = !showDetails"
                    class="text-sm px-4 py-2 rounded-md text-violet-600 hover:bg-violet-50">
                <span x-text="showDetails ? 'Hide options' : 'Customise'"></span>
            </button>
            <button type="button" @click="saveAndClose()"
                    class="text-sm px-4 py-2 rounded-md bg-violet-600 text-white hover:bg-violet-700">
                Save preferences
            </button>
            <button type="button" @click="acceptAll()"
                    class="text-sm px-4 py-2 rounded-md bg-emerald-600 text-white hover:bg-emerald-700">
                Accept all
            </button>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function cookieBanner() {
    return {
        visible: false,
        showDetails: false,
        prefs: { performance: false, analytics: false, marketing: false },
        cookieKey: 'nn-cookie-consent',
        ttlDays: 365,
        init() {
            try {
                const m = document.cookie.match(new RegExp('(?:^|; )' + this.cookieKey + '=([^;]*)'));
                if (m) {
                    const parsed = JSON.parse(decodeURIComponent(m[1]));
                    Object.assign(this.prefs, parsed);
                    return;
                }
            } catch (e) {}
            this.visible = true;
        },
        write() {
            const exp = new Date(Date.now() + this.ttlDays * 86400000).toUTCString();
            document.cookie = `${this.cookieKey}=${encodeURIComponent(JSON.stringify(this.prefs))}; expires=${exp}; path=/; SameSite=Lax`;
        },
        acceptAll() { Object.keys(this.prefs).forEach(k => this.prefs[k] = true); this.write(); this.visible = false; },
        acceptOnly() { Object.keys(this.prefs).forEach(k => this.prefs[k] = false); this.write(); this.visible = false; },
        saveAndClose() { this.write(); this.visible = false; },
    };
}
</script>
@endpush
@endonce

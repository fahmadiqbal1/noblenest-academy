{{--
    Global Toast Viewport.
    Place this once in each layout (just before </body>).
    Usage from JS: window.toast.success('Done!') | window.toast.error('Oops') | window.toast.info('FYI')
--}}

<div
    x-data
    x-init="$store.toasts || Alpine.store('toasts', { items: [], add(t){ this.items.push({id: Date.now(), ...t}); setTimeout(()=>this.remove(this.items.at(-1)?.id), 4000); }, remove(id){ this.items = this.items.filter(i=>i.id!==id); } })"
    aria-live="polite"
    aria-atomic="false"
    class="fixed bottom-4 right-4 z-[9999] flex flex-col gap-2 w-80 pointer-events-none"
>
    <template x-for="toast in $store.toasts.items" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            :class="{ 'border-emerald-300 bg-emerald-50 text-emerald-800': toast.type === 'success', 'border-[var(--color-coral-300)] bg-[var(--color-coral-50)] text-[var(--color-coral-800)]': 'error', 'border-blue-300 bg-blue-50 text-blue-800': 'info', 'border-amber-300 bg-amber-50 text-amber-800': 'warn', }"
            class="pointer-events-auto flex items-start gap-3 rounded-[var(--radius-sm)] border-[2px] p-4 shadow-[var(--shadow-clay)]"
            role="status"
        >
            <span class="text-lg shrink-0" aria-hidden="true"
                  x-text="{ success: '✓', error: '✕', info: 'ℹ', warn: '⚠' }[toast.type] ?? 'ℹ'"></span>
            <p class="text-sm font-semibold flex-1" x-text="toast.message"></p>
            <button
                type="button"
                @click="$store.toasts.remove(toast.id)"
                class="shrink-0 rounded p-0.5 hover:bg-black/10 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-1 transition-colors"
                aria-label="Dismiss"
            >
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" x2="6" y1="6" y2="18"/><line x1="6" x2="18" y1="6" y2="18"/>
                </svg>
            </button>
        </div>
    </template>
</div>

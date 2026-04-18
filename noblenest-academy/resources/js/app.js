import './bootstrap';

// ─── Alpine.js Toast Store ────────────────────────────────────────────────────
// Provides window.toast.success(msg) | window.toast.error(msg) | window.toast.info(msg)
// The <x-ui.toast /> component renders the viewport using this store.
document.addEventListener('alpine:init', () => {
    Alpine.store('toasts', {
        items: [],

        add({ type = 'info', message = '', duration = 4000 } = {}) {
            const id = Date.now() + Math.random();
            this.items.push({ id, type, message });
            setTimeout(() => this.remove(id), duration);
        },

        remove(id) {
            this.items = this.items.filter(i => i.id !== id);
        },
    });
});

// Convenience global helpers
window.toast = {
    success: (message, duration) => Alpine.store('toasts').add({ type: 'success', message, duration }),
    error:   (message, duration) => Alpine.store('toasts').add({ type: 'error',   message, duration }),
    info:    (message, duration) => Alpine.store('toasts').add({ type: 'info',    message, duration }),
    warn:    (message, duration) => Alpine.store('toasts').add({ type: 'warn',    message, duration }),
};

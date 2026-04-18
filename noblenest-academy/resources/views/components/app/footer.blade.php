<footer class="border-t-[3px] border-[var(--color-border)] bg-[var(--color-surface)] py-10 mt-auto">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
            {{-- Brand --}}
            <div>
                <a href="{{ route('noble.home') }}" class="inline-flex items-center gap-3 mb-3 focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded">
                    <img src="{{ asset('brand/noblenest-logo.svg') }}" alt="NobleNest Global Academy logo" class="w-10 h-10 rounded-[var(--radius-sm)] shadow-[var(--shadow-clay)]" loading="lazy">
                    <div>
                        <span class="block font-bold text-[var(--color-text)]">NobleNest Global Academy</span>
                        <span class="block text-xs text-[var(--color-text-muted)]">Family-first learning, beautifully delivered.</span>
                    </div>
                </a>
                <p class="text-sm text-[var(--color-text-muted)]">Adaptive, multilingual early education for families worldwide.</p>
            </div>

            {{-- Quick links --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-[var(--color-primary)] mb-3">Quick Links</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('pricing') }}" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Pricing</a></li>
                    <li><a href="/privacy" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Privacy Policy</a></li>
                    <li><a href="/terms" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">Terms of Service</a></li>
                </ul>
            </div>

            {{-- Get in touch --}}
            <div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-[var(--color-primary)] mb-3">Get in Touch</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="mailto:support@noblenest.com" class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors">support@noblenest.com</a></li>
                    <li><span class="text-[var(--color-text-muted)]">8 Languages supported</span></li>
                    <li><span class="text-[var(--color-text-muted)]">COPPA Compliant</span></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-[var(--color-border)] mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs text-[var(--color-text-muted)]">&copy; {{ date('Y') }} NobleNest Global Academy. All rights reserved.</p>
            <div class="flex items-center gap-4">
                <x-app.locale-switcher />
                <a
                    href="https://wa.me/?text={{ urlencode('Check out NobleNest Academy – early learning in 8 languages! ' . url('/')) }}"
                    class="text-[var(--color-text-muted)] hover:text-[var(--color-primary)] transition-colors focus-visible:outline-2 focus-visible:outline-[var(--color-brand-600)] focus-visible:outline-offset-2 rounded"
                    target="_blank" rel="noopener" title="Share on WhatsApp"
                >
                    <x-ui.icon name="share-2" class="w-5 h-5" />
                </a>
            </div>
        </div>
    </div>
</footer>

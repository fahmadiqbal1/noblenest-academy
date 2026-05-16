{{--
    Shared body-tail contents — included by every layouts/*.blade.php right
    before </body>. Carries the toast viewport, AI bubble + assistant partial,
    PWA installer, flash messages, and the @yield/@stack hooks.
--}}

{{-- Phase 8: EU-compliant cookie consent banner — shows once until cookie set. --}}
<x-app.cookie-banner />

{{-- Phase 8: Plausible analytics — only fires after analytics opt-in. --}}
<x-seo.plausible />

{{-- Toast viewport --}}
<x-ui.toast />

{{-- AI assistant bubble + chat — only for authenticated users --}}
@if(auth()->check())
    <x-app.ai-bubble />
    @include('partials.assistant')
@endif

{{-- PWA installer --}}
<x-app.pwa-installer />

{{-- Flash messages via toast --}}
<x-app.flash-messages />

@yield('scripts')
@stack('scripts')

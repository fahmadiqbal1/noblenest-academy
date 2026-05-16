{{-- Phase 8 follow-up — Plausible analytics gated by the cookie banner.

    Only loads the Plausible script when:
      1. PLAUSIBLE_DOMAIN env is set (build-time gate)
      2. The user has opted into the `analytics` cookie via the banner.

    The cookie banner persists choices to a `nn-cookie-consent` cookie
    (see resources/views/components/app/cookie-banner.blade.php). This
    component reads that cookie inline so first-page-load doesn't fire
    before consent.
--}}
@php
    $plausibleDomain = env('PLAUSIBLE_DOMAIN');
    $plausibleSrc    = env('PLAUSIBLE_SCRIPT', 'https://plausible.io/js/script.js');
@endphp

@if($plausibleDomain)
<script>
(function () {
    try {
        var m = document.cookie.match(/(?:^|; )nn-cookie-consent=([^;]*)/);
        if (!m) return;
        var prefs = JSON.parse(decodeURIComponent(m[1]));
        if (!prefs || !prefs.analytics) return;
        var s = document.createElement('script');
        s.defer = true;
        s.src = {!! json_encode($plausibleSrc) !!};
        s.setAttribute('data-domain', {!! json_encode($plausibleDomain) !!});
        document.head.appendChild(s);
    } catch (e) {}
})();
</script>
@endif

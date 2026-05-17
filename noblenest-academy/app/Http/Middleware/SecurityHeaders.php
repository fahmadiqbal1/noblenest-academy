<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase 8 — production security headers.
 *
 * Sets HSTS, CSP (with per-request nonce), Permissions-Policy,
 * COOP/COEP, X-Frame-Options DENY, Referrer-Policy and friends.
 *
 * The per-request CSP nonce is shared with all Blade views as
 * $csp_nonce so inline scripts can render <script nonce="{{ $csp_nonce }}">.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // Generate a fresh nonce for each request and expose it to Blade.
        $nonce = base64_encode(random_bytes(16));
        View::share('csp_nonce', $nonce);

        $response = $next($request);

        // --- Static headers -------------------------------------------------
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(self), geolocation=(), '
            . 'payment=(self "https://js.stripe.com" "https://www.paypal.com"), '
            . 'interest-cohort=()'
        );
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        // credentialless lets us load Pyodide / cross-origin CDN scripts
        // without forcing every image/font/iframe to ship CORP headers.
        $response->headers->set('Cross-Origin-Embedder-Policy', 'credentialless');

        // --- HSTS (HTTPS only) ----------------------------------------------
        if ($request->isSecure() || app()->environment('production')) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=63072000; includeSubDomains; preload'
            );
        }

        // --- Content-Security-Policy ----------------------------------------
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' 'strict-dynamic' "
                . 'https://cdn.jsdelivr.net https://js.stripe.com https://www.paypal.com',
            "connect-src 'self' https://api.groq.com https://api.heygen.com "
                . 'https://api.synthesia.io https://api.openai.com '
                . 'https://api.stripe.com https://api-m.paypal.com',
            "img-src 'self' data: https:",
            "style-src 'self' 'unsafe-inline'",
            "font-src 'self' data:",
            'frame-src https://js.stripe.com https://www.paypal.com',
            "worker-src 'self' blob:",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}

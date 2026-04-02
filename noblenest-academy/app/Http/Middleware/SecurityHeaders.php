<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Strict CSP while still allowing Bootstrap CDN (if used), own assets,
     * and inline scripts/styles that Laravel Blade requires.
     * Tighten `script-src` once you move fully to a bundler/nonce strategy.
     */
    private array $headers = [
        'X-Content-Type-Options'  => 'nosniff',
        'X-Frame-Options'         => 'SAMEORIGIN',
        'X-XSS-Protection'        => '1; mode=block',
        'Referrer-Policy'         => 'strict-origin-when-cross-origin',
        'Permissions-Policy'      => 'camera=(), microphone=(), geolocation=()',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach ($this->headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        // HSTS — only send over HTTPS
        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Content-Security-Policy
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "frame-src 'self' https://*.daily.co",
            "media-src 'self' https://storage.googleapis.com https://heygen-videos.s3.amazonaws.com blob:",
            "worker-src 'self' blob:",
            "manifest-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "upgrade-insecure-requests",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}

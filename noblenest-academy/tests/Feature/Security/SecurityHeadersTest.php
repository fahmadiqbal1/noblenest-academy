<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 8 — verifies SecurityHeaders middleware emits the expected
 * production-grade headers, including a per-request CSP nonce.
 */
class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_security_headers_are_present_on_home(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->assertHeader('Cross-Origin-Embedder-Policy', 'credentialless');

        $permissions = (string) $response->headers->get('Permissions-Policy');
        $this->assertStringContainsString('microphone=(self)', $permissions);
        $this->assertStringContainsString('camera=()', $permissions);
        $this->assertStringContainsString('interest-cohort=()', $permissions);
        $this->assertStringContainsString('payment=(self', $permissions);
    }

    public function test_csp_header_includes_per_request_nonce(): void
    {
        $r1 = $this->get('/');
        $r2 = $this->get('/');

        $csp1 = (string) $r1->headers->get('Content-Security-Policy');
        $csp2 = (string) $r2->headers->get('Content-Security-Policy');

        $this->assertNotEmpty($csp1);
        $this->assertMatchesRegularExpression(
            "/'nonce-[A-Za-z0-9+\\/=]+'/",
            $csp1,
            'CSP must include a nonce-* token'
        );
        $this->assertStringContainsString("default-src 'self'", $csp1);
        $this->assertStringContainsString('frame-src https://js.stripe.com', $csp1);
        $this->assertStringContainsString("connect-src 'self' https://api.groq.com", $csp1);

        // Nonces should differ between requests.
        preg_match("/'nonce-([A-Za-z0-9+\\/=]+)'/", $csp1, $m1);
        preg_match("/'nonce-([A-Za-z0-9+\\/=]+)'/", $csp2, $m2);
        $this->assertNotSame($m1[1] ?? null, $m2[1] ?? null);
    }
}

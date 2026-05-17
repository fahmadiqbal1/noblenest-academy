<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 4 — Stripe webhook security tests.
 *
 * Coverage:
 *   - Missing Stripe-Signature header → 400
 *   - Invalid signature → 400
 *   - Webhook secret not configured → 500
 *
 * Live signature-valid testing requires `stripe-mock` or a test secret +
 * pre-signed payload (Phase 6 CI work). These three negative-path tests
 * are the security-critical floor.
 */
class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function rejects_webhook_without_signature_header(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test_pretend']);

        $response = $this->post('/webhook/stripe', ['type' => 'checkout.session.completed'], [
            'Content-Type' => 'application/json',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function rejects_webhook_with_invalid_signature(): void
    {
        config(['services.stripe.webhook_secret' => 'whsec_test_pretend']);

        $response = $this->withHeaders([
            'Stripe-Signature' => 'totally-fake-signature',
            'Content-Type' => 'application/json',
        ])->post('/webhook/stripe', ['type' => 'checkout.session.completed']);

        $response->assertStatus(400);
    }

    /** @test */
    public function rejects_webhook_when_secret_is_not_configured(): void
    {
        config(['services.stripe.webhook_secret' => null]);

        $response = $this->withHeaders([
            'Stripe-Signature' => 't=123,v1=abc',
            'Content-Type' => 'application/json',
        ])->post('/webhook/stripe', ['type' => 'checkout.session.completed']);

        $response->assertStatus(500);
    }
}

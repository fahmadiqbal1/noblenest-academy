<?php

namespace Tests\Feature\Billing;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 7 — Stripe webhook tests that exercise the *valid-signature* path
 * (the existing Phase 4 test only covers negative paths).
 *
 * We synthesize a real Stripe signature with the v1 HMAC scheme so we don't
 * need a live Stripe account.
 */
class StripeWebhookPhase7Test extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_phase7_test_secret_value_long_enough';

    /** @test */
    public function invalid_signature_returns_400(): void
    {
        config(['services.stripe.webhook_secret' => self::SECRET]);

        $response = $this->withHeaders([
            'Stripe-Signature' => 't=1,v1=deadbeef',
            'Content-Type'     => 'application/json',
        ])->post('/webhook/stripe', ['type' => 'customer.subscription.deleted']);

        $response->assertStatus(400);
    }

    /** @test */
    public function valid_signature_subscription_deleted_marks_local_subscription_canceled(): void
    {
        config(['services.stripe.webhook_secret' => self::SECRET]);

        $user = User::factory()->create();
        $sub = Subscription::factory()->create([
            'user_id'     => $user->id,
            'provider'    => 'stripe',
            'provider_id' => 'sub_phase7_test_001',
            'status'      => 'active',
        ]);

        [$payload, $sigHeader] = $this->signedStripePayload([
            'id'              => 'evt_phase7_canceled_001',
            'object'          => 'event',
            'type'            => 'customer.subscription.deleted',
            'data'            => ['object' => ['id' => 'sub_phase7_test_001', 'status' => 'canceled']],
        ]);

        $response = $this->call(
            'POST',
            '/webhook/stripe',
            [], [], [],
            ['HTTP_Stripe-Signature' => $sigHeader, 'CONTENT_TYPE' => 'application/json'],
            $payload,
        );

        $response->assertStatus(204);

        $sub->refresh();
        $this->assertSame('canceled', $sub->status);
        $this->assertFalse((bool) $sub->active);
    }

    /** @test */
    public function duplicate_event_id_is_ignored_on_replay(): void
    {
        config(['services.stripe.webhook_secret' => self::SECRET]);

        $user = User::factory()->create();
        Subscription::factory()->create([
            'user_id'     => $user->id,
            'provider'    => 'stripe',
            'provider_id' => 'sub_phase7_dedupe_001',
            'status'      => 'active',
        ]);

        [$payload, $sigHeader] = $this->signedStripePayload([
            'id'     => 'evt_phase7_dedupe_xyz',
            'object' => 'event',
            'type'   => 'customer.subscription.updated',
            'data'   => ['object' => [
                'id'                  => 'sub_phase7_dedupe_001',
                'status'              => 'past_due',
                'current_period_end'  => time() + 86400,
            ]],
        ]);

        $this->call('POST', '/webhook/stripe', [], [], [],
            ['HTTP_Stripe-Signature' => $sigHeader, 'CONTENT_TYPE' => 'application/json'],
            $payload,
        )->assertStatus(204);

        $first = DB::table('stripe_webhook_events')->count();

        // Replay — same payload + sig — should NOT create a duplicate row.
        $this->call('POST', '/webhook/stripe', [], [], [],
            ['HTTP_Stripe-Signature' => $sigHeader, 'CONTENT_TYPE' => 'application/json'],
            $payload,
        )->assertStatus(204);

        $this->assertSame($first, DB::table('stripe_webhook_events')->count());
    }

    /**
     * Build a Stripe-style payload + Stripe-Signature header so
     * \Stripe\Webhook::constructEvent accepts it.
     *
     * @return array{0:string,1:string}
     */
    private function signedStripePayload(array $event): array
    {
        $payload = json_encode($event, JSON_UNESCAPED_SLASHES);
        $timestamp = time();
        $signed = $timestamp . '.' . $payload;
        $sig = hash_hmac('sha256', $signed, self::SECRET);

        return [$payload, "t={$timestamp},v1={$sig}"];
    }
}

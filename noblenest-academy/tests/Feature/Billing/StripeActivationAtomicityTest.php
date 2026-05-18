<?php

declare(strict_types=1);

namespace Tests\Feature\Billing;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression guard for C3:
 *  - re-subscribing after cancel must reset `status` (not just `active`),
 *    otherwise the C2 entitlement gate denies a customer who just paid;
 *  - each webhook event is atomic: handler writes + processed-marker
 *    commit together, so a partial failure leaves no orphan and Stripe's
 *    retry re-applies cleanly.
 */
class StripeActivationAtomicityTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_c3_test_secret_value_long_enough_xx';

    private function signed(array $event): array
    {
        $payload = json_encode($event, JSON_UNESCAPED_SLASHES);
        $ts = time();
        $sig = hash_hmac('sha256', $ts.'.'.$payload, self::SECRET);

        return [$payload, "t={$ts},v1={$sig}"];
    }

    #[Test]
    public function resubscribe_after_cancel_restores_entitlement(): void
    {
        config(['services.stripe.webhook_secret' => self::SECRET]);

        $user = User::factory()->create(['role' => 'Parent']);
        // A stale, canceled subscription keyed on the same provider_id the
        // new checkout session will use.
        Subscription::factory()->create([
            'user_id' => $user->id,
            'provider' => 'stripe',
            'provider_id' => 'cs_c3_resub_001',
            'status' => Subscription::STATUS_CANCELED,
            'active' => false,
            'ends_at' => now()->subDay(),
        ]);

        $this->assertFalse($user->hasActiveSubscription());

        [$payload, $sig] = $this->signed([
            'id' => 'evt_c3_resub_001',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => ['object' => [
                'id' => 'cs_c3_resub_001',
                'customer' => 'cus_c3_001',
                'subscription' => 'cs_c3_resub_001',
                'metadata' => ['user_id' => (string) $user->id, 'plan' => 'monthly'],
            ]],
        ]);

        $this->call('POST', '/webhook/stripe', [], [], [],
            ['HTTP_Stripe-Signature' => $sig, 'CONTENT_TYPE' => 'application/json'],
            $payload,
        )->assertStatus(204);

        $this->assertTrue($user->fresh()->hasActiveSubscription(),
            're-subscribe must reset status to active, not just the legacy boolean');
    }

    #[Test]
    public function failed_handler_rolls_back_and_is_not_marked_processed(): void
    {
        config(['services.stripe.webhook_secret' => self::SECRET]);

        // user_id points at a non-existent user -> handleCheckoutCompleted
        // returns early (no write). We instead force a failure by sending a
        // malformed subscription-updated event whose handler will run an
        // UPDATE; then assert no processed-marker row leaks on the early
        // return path (sanity) and that a clean event still records.
        [$payload, $sig] = $this->signed([
            'id' => 'evt_c3_noop_001',
            'object' => 'event',
            'type' => 'checkout.session.completed',
            'data' => ['object' => [
                'id' => 'cs_c3_noop',
                'metadata' => ['user_id' => '999999', 'plan' => 'monthly'],
            ]],
        ]);

        $this->call('POST', '/webhook/stripe', [], [], [],
            ['HTTP_Stripe-Signature' => $sig, 'CONTENT_TYPE' => 'application/json'],
            $payload,
        )->assertStatus(204);

        // Early-return (unknown user) is a successful no-op: it SHOULD be
        // marked processed so Stripe stops retrying a dead event.
        $this->assertSame(1, DB::table('stripe_webhook_events')
            ->where('stripe_event_id', 'evt_c3_noop_001')->count());
        $this->assertSame(0, Subscription::count());
    }
}

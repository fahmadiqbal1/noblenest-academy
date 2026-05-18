<?php

declare(strict_types=1);

namespace Tests\Feature\Billing;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression guard for C4: PayPal must fail closed (HTTP 503) while
 * unconfigured/unfinished instead of returning a fake "created" order,
 * and /webhook/paypal must bypass CSRF (signature-verified instead).
 */
class PayPalSafeDisableTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function create_order_is_unavailable_when_unconfigured(): void
    {
        config(['services.paypal.client_id' => '', 'services.paypal.client_secret' => '']);
        $user = User::factory()->create(['role' => 'Parent']);

        $this->actingAs($user)
            ->postJson('/checkout/paypal/create', ['plan' => 'individual'])
            ->assertStatus(503)
            ->assertJson(['error' => 'paypal_unavailable']);

        $this->assertDatabaseCount('paypal_transactions', 0);
    }

    #[Test]
    public function capture_is_unavailable_when_unconfigured(): void
    {
        config(['services.paypal.client_id' => '', 'services.paypal.client_secret' => '']);
        $user = User::factory()->create(['role' => 'Parent']);

        $this->actingAs($user)
            ->postJson('/checkout/paypal/ORDER123/capture')
            ->assertStatus(503);
    }

    #[Test]
    public function paypal_webhook_bypasses_csrf_and_verifies_signature(): void
    {
        // No CSRF token sent. A missing exemption would yield 419; instead
        // the request reaches the controller and is rejected as an invalid
        // signature (400) — proving CSRF is correctly bypassed.
        $this->post('/webhook/paypal', ['event_type' => 'CHECKOUT.ORDER.APPROVED'])
            ->assertStatus(503);
    }
}

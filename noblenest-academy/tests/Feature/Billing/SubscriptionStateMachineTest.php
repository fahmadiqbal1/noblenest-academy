<?php

namespace Tests\Feature\Billing;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\PricingTierSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionStateMachineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PricingTierSeeder::class);
    }

    /** @test */
    public function status_transitions_active_to_past_due_to_canceled(): void
    {
        $user = User::factory()->create();
        $sub = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'plan' => 'individual',
        ]);

        $this->assertTrue($sub->isActive());

        $sub->markPastDue();
        $sub->refresh();
        $this->assertTrue($sub->isPastDue());
        $this->assertSame('past_due', $sub->status);

        $sub->cancel();
        $sub->refresh();
        $this->assertTrue($sub->isCanceled());
        $this->assertFalse($sub->active);
        $this->assertNotNull($sub->canceled_at);
    }

    /** @test */
    public function pause_and_resume_round_trip(): void
    {
        $user = User::factory()->create();
        $sub = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $sub->pause();
        $sub->refresh();
        $this->assertTrue($sub->isPaused());
        $this->assertNotNull($sub->paused_at);

        $sub->resume();
        $sub->refresh();
        $this->assertTrue($sub->isActive());
        $this->assertNull($sub->paused_at);
    }

    /** @test */
    public function upgrade_calculates_proration_and_records_pending_payment(): void
    {
        $user = User::factory()->create();
        $sub = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'plan' => 'individual',
            'ends_at' => now()->addDays(15),
        ]);

        $payment = $sub->upgradeTo('family', null);
        $sub->refresh();

        $this->assertSame('family', $sub->plan);
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertSame('pending_proration', $payment->status);
        // (25 - 12) / 30 * 15 = 6.50
        $this->assertEqualsWithDelta(6.50, (float) $payment->amount, 0.05);
    }
}

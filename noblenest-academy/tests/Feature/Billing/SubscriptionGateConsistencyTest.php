<?php

declare(strict_types=1);

namespace Tests\Feature\Billing;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression guard for C2: the subscription gate read the legacy
 * `active` boolean while the Phase 7 state machine only mutated
 * `status`. A paused / past_due subscription therefore still passed
 * EnsureSubscriptionActive (and User::hasActiveSubscription). Both must
 * now agree via Subscription::scopeEntitled().
 */
class SubscriptionGateConsistencyTest extends TestCase
{
    use RefreshDatabase;

    private function parentWithSub(string $status): User
    {
        $user = User::factory()->create(['role' => 'Parent']);
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => $status,
            'ends_at' => now()->addMonth(),
        ]);

        return $user;
    }

    #[Test]
    public function active_status_grants_access(): void
    {
        $user = $this->parentWithSub(Subscription::STATUS_ACTIVE);

        $this->assertTrue($user->hasActiveSubscription());
        $this->actingAs($user)->get('/activities')->assertOk();
    }

    #[Test]
    public function paused_subscription_is_denied(): void
    {
        $user = $this->parentWithSub(Subscription::STATUS_PAUSED);

        $this->assertFalse($user->hasActiveSubscription());
        $this->actingAs($user)->get('/activities')->assertRedirect('/');
    }

    #[Test]
    public function past_due_subscription_is_denied(): void
    {
        $user = $this->parentWithSub(Subscription::STATUS_PAST_DUE);

        $this->assertFalse($user->hasActiveSubscription());
        $this->actingAs($user)->get('/activities')->assertRedirect('/');
    }

    #[Test]
    public function canceled_subscription_is_denied(): void
    {
        $user = $this->parentWithSub(Subscription::STATUS_CANCELED);

        $this->assertFalse($user->hasActiveSubscription());
        $this->actingAs($user)->get('/activities')->assertRedirect('/');
    }

    #[Test]
    public function state_machine_transitions_keep_legacy_boolean_in_sync(): void
    {
        $sub = Subscription::factory()->create([
            'status' => Subscription::STATUS_ACTIVE,
            'active' => true,
            'ends_at' => now()->addMonth(),
        ]);

        $sub->pause();
        $this->assertFalse($sub->fresh()->active, 'pause() must clear legacy active');

        $sub->resume();
        $this->assertTrue($sub->fresh()->active);

        $sub->markPastDue();
        $this->assertFalse($sub->fresh()->active, 'markPastDue() must clear legacy active');
    }
}

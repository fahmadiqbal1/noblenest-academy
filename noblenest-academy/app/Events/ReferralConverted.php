<?php

namespace App\Events;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ReferralConverted Event
 *
 * Fired when a referred user completes their first subscription purchase,
 * converting the referral from signed_up → subscribed.
 * Listeners: issue referral reward (Phase 3), send congratulatory notification (Phase 5).
 */
class ReferralConverted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $referrer;
    public User $referred;
    public Subscription $subscription;

    public function __construct(
        User $referrer,
        User $referred,
        Subscription $subscription
    ) {
        $this->referrer     = $referrer;
        $this->referred     = $referred;
        $this->subscription = $subscription;
    }
}

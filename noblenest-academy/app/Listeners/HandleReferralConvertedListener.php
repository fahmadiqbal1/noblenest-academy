<?php

namespace App\Listeners;

use App\Events\ReferralConverted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Handles ReferralConverted events.
 *
 * Phase 3+ will fill the handle() body:
 *   - Calculate reward tier (ReferralRewardService::calculateTier)
 *   - Create ReferralReward record
 *   - Dispatch IssueReferralRewardJob
 */
class HandleReferralConvertedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'default';

    public int $tries = 5;

    public function handle(ReferralConverted $event): void
    {
        // Phase 3: implement reward issuance logic here
    }
}

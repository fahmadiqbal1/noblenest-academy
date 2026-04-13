<?php

namespace App\Listeners;

use App\Events\StreakAtRisk;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Handles StreakAtRisk events.
 *
 * Phase 5+ will fill the handle() body:
 *   - Check notification_preferences.streak_warning for parent
 *   - Send push/SMS notification (behind push_notifications / sms_notifications flags)
 *   - Log notification_event record with variant_key for A/B testing
 */
class HandleStreakAtRiskListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'high';

    public int $tries = 3;

    public function handle(StreakAtRisk $event): void
    {
        // Phase 5: implement streak-risk notification logic here
    }
}

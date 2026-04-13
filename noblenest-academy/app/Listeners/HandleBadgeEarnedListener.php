<?php

namespace App\Listeners;

use App\Events\BadgeEarned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Handles BadgeEarned events.
 *
 * Phase 5+ will fill the handle() body:
 *   - Send push notification to parent (behind notification_engine flag)
 *   - Log notification_event record
 *   - Trigger share card refresh (Phase 3)
 */
class HandleBadgeEarnedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public int $tries = 3;

    public function handle(BadgeEarned $event): void
    {
        // Phase 5: implement badge notification logic here
    }
}

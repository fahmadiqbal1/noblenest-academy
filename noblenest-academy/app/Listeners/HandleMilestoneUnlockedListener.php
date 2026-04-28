<?php

namespace App\Listeners;

use App\Events\MilestoneUnlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Handles MilestoneUnlocked events.
 *
 * Phase 5+ will fill the handle() body:
 *   - Send push notification to parent (behind notification_engine flag)
 *   - Log notification_event record
 *   - Trigger share card generation if first milestone
 */
class HandleMilestoneUnlockedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'notifications';

    public int $tries = 3;

    public function handle(MilestoneUnlocked $event): void
    {
        // Phase 5: implement parent notification logic here
    }
}

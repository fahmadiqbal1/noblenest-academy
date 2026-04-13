<?php

namespace App\Listeners;

use App\Events\StruggleDetected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Handles StruggleDetected events.
 *
 * Phase 7+ will fill the handle() body:
 *   - Alert parent (behind notification_engine + struggle_alert preference)
 *   - Update child's ELI weight (eli_weight) to reduce difficulty
 *   - Inject EI activity into next learning path slot
 *   - Log notification_event record
 */
class HandleStruggleDetectedListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'high';

    public int $tries = 3;

    public function handle(StruggleDetected $event): void
    {
        // Phase 7: implement struggle response logic here
    }
}

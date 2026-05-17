<?php

namespace App\Events;

use App\Models\ChildProfile;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * StreakAtRisk Event
 *
 * Fired when a child's learning streak is about to break.
 * Listeners: push/SMS notification to parent (Phase 5).
 */
class StreakAtRisk
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChildProfile $child;

    public int $hoursRemaining;

    public function __construct(
        ChildProfile $child,
        int $hoursRemaining
    ) {
        $this->child = $child;
        $this->hoursRemaining = $hoursRemaining;
    }
}

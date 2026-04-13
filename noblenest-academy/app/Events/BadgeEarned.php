<?php

namespace App\Events;

use App\Models\Badge;
use App\Models\ChildProfile;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * BadgeEarned Event
 *
 * Fired when a child earns a badge.
 * Listeners: notify parent (Phase 5), update share card (Phase 3).
 */
class BadgeEarned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChildProfile $child;
    public Badge $badge;

    public function __construct(
        ChildProfile $child,
        Badge $badge
    ) {
        $this->child = $child;
        $this->badge = $badge;
    }
}

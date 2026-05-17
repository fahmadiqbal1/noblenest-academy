<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ParentDisengaged Event
 *
 * Fired when a parent has not logged into the dashboard for N days.
 * Listeners: send re-engagement email/push sequence (Phase 5),
 * update engagement_score (Phase 6).
 */
class ParentDisengaged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $parent;

    public int $daysInactive;

    public function __construct(
        User $parent,
        int $daysInactive
    ) {
        $this->parent = $parent;
        $this->daysInactive = $daysInactive;
    }
}

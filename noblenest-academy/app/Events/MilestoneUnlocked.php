<?php

namespace App\Events;

use App\Models\ChildProfile;
use App\Models\Milestone;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * MilestoneUnlocked Event
 *
 * Fired when a child achieves a developmental milestone.
 * Listeners: notify parent (Phase 5), award XP/badge check (Phase 3).
 */
class MilestoneUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChildProfile $child;
    public Milestone $milestone;

    public function __construct(
        ChildProfile $child,
        Milestone $milestone
    ) {
        $this->child     = $child;
        $this->milestone = $milestone;
    }
}

<?php

namespace App\Events;

use App\Models\ChildProfile;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * StruggleDetected Event
 *
 * Fired when a child has a consecutive-failure streak in a cognitive domain.
 * Listeners: adjust learning path (Phase 7), alert parent (Phase 5),
 * update ELI weight (Phase 7).
 */
class StruggleDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChildProfile $child;

    public string $cognitiveDomain;

    public int $streakLength;

    public function __construct(
        ChildProfile $child,
        string $cognitiveDomain,
        int $streakLength
    ) {
        $this->child = $child;
        $this->cognitiveDomain = $cognitiveDomain;
        $this->streakLength = $streakLength;
    }
}

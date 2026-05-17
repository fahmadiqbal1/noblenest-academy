<?php

namespace App\Events;

use App\Models\Activity;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ActivityCompleted Event
 *
 * Fired whenever a child completes or attempts an activity.
 * Used by listeners to:
 *   1. Update ChildSkillState (mastery, EMA score, streaks)
 *   2. Trigger adaptation rules (if struggling, adjust next activity)
 *   3. Schedule emotional intelligence interventions
 */
class ActivityCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChildProfile $child;

    public Activity $activity;

    public ChildActivityProgress $progress;

    public float $masteryScore;

    /**
     * Create a new event instance.
     *
     * @param  ChildProfile  $child  The child who completed the activity
     * @param  Activity  $activity  The activity that was completed
     * @param  ChildActivityProgress  $progress  The progress record (includes score, completed_at, etc.)
     * @param  float  $masteryScore  Normalized mastery score (0.0 - 1.0), calculated from progress.score or completion status
     */
    public function __construct(
        ChildProfile $child,
        Activity $activity,
        ChildActivityProgress $progress,
        float $masteryScore
    ) {
        $this->child = $child;
        $this->activity = $activity;
        $this->progress = $progress;
        $this->masteryScore = max(0, min(1, $masteryScore)); // Ensure [0.0, 1.0]
    }

    /**
     * Was this attempt successful? (mastery >= 0.8)
     */
    public function wasSuccessful(): bool
    {
        return $this->masteryScore >= 0.8;
    }

    /**
     * Was this attempt a struggle? (mastery < 0.5)
     */
    public function wasStruggle(): bool
    {
        return $this->masteryScore < 0.5;
    }
}

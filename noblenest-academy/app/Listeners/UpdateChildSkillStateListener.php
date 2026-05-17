<?php

namespace App\Listeners;

use App\Events\ActivityCompleted;
use App\Models\ChildSkillState;
use App\Jobs\RecomputeLearningPathJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * UpdateChildSkillStateListener
 *
 * Listens for ActivityCompleted events and updates the child's ChildSkillState.
 *
 * Updates:
 *   - EMA score based on new mastery result
 *   - Confidence (increases with each attempt)
 *   - Streak tracking (success/struggle)
 *   - Last updated timestamp
 *
 * Queues: RecomputeLearningPathJob to recalculate the child's next activities
 */
class UpdateChildSkillStateListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the activity completed event.
     */
    public function handle(ActivityCompleted $event): void
    {
        // Skip if activity lacks cognitive domain info
        if (!$event->activity->cognitive_domain) {
            Log::warning('ActivityCompleted: activity lacks cognitive_domain, skipping skill state update', [
                'activity_id' => $event->activity->id,
                'child_id'    => $event->child->id,
            ]);
            return;
        }

        // For now, use first developmental domain if array; otherwise use a generic one
        $devDomain = 'cognitive';
        if ($event->activity->developmental_domains) {
            $domains = is_array($event->activity->developmental_domains)
                ? $event->activity->developmental_domains
                : json_decode($event->activity->developmental_domains, true) ?? [];
            $devDomain = $domains[0] ?? 'cognitive';
        }

        // Find or create ChildSkillState for this cognitive/developmental domain combo
        $skillState = ChildSkillState::firstOrCreate(
            [
                'child_profile_id'    => $event->child->id,
                'cognitive_domain'    => $event->activity->cognitive_domain,
                'developmental_domain' => $devDomain,
            ],
            [
                'ema_score'    => 0.5,  // Default: unknown
                'ema_confidence' => 0.0, // No confidence yet
            ]
        );

        // Update EMA score with the new mastery result
        $skillState->updateEMAScore($event->masteryScore);

        // Update streak tracking
        if ($event->wasSuccessful()) {
            $skillState->recordSuccess();
        } elseif ($event->wasStruggle()) {
            $skillState->recordStruggle();
        }

        Log::info('Updated ChildSkillState', [
            'child_id'               => $event->child->id,
            'cognitive_domain'       => $event->activity->cognitive_domain,
            'developmental_domain'   => $devDomain,
            'mastery_score'          => $event->masteryScore,
            'ema_score'              => $skillState->ema_score,
            'streak_success'         => $skillState->streak_success,
            'streak_struggle'        => $skillState->streak_struggle,
        ]);

        // Queue job to recalculate learning path based on updated skill state
        RecomputeLearningPathJob::dispatch($event->child);
    }
}

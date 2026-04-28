<?php

namespace Tests\Feature;

use App\Events\ActivityCompleted;
use App\Models\Activity;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use App\Models\ChildSkillState;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FeedbackLoopIntegrationTest extends TestCase
{
    protected User $parent;
    protected ChildProfile $child;
    protected Activity $activity;

    protected function setUp(): void
    {
        parent::setUp();

        // Create parent user
        $this->parent = User::factory()->create();

        // Create child profile
        $this->child = ChildProfile::factory()
            ->for($this->parent)
            ->create(['age_months' => 36]); // 3 years old

        // Create an activity with cognitive domain
        $this->activity = Activity::factory()
            ->create([
                'cognitive_domain' => 'math',
                'developmental_domains' => ['cognitive', 'language'],
                'age_tier' => 'toddler',
                'difficulty' => 'easy',
            ]);
    }

    /**
     * Test that completing an activity creates/updates ChildSkillState.
     */
    public function test_activity_completion_updates_skill_state()
    {
        Event::fake();

        // Mark activity as completed with a good score
        $progress = ChildActivityProgress::create([
            'child_profile_id' => $this->child->id,
            'activity_id'      => $this->activity->id,
            'score'            => 85,
            'completed_at'     => now(),
        ]);

        // Dispatch event manually (normally fired by controller)
        ActivityCompleted::dispatch($this->child, $this->activity, $progress, 0.85);

        // Verify ChildSkillState was created/updated
        $skillState = ChildSkillState::where('child_profile_id', $this->child->id)
            ->where('cognitive_domain', 'math')
            ->first();

        $this->assertNotNull($skillState);
        $this->assertGreaterThan(0, $skillState->ema_score);
    }

    /**
     * Test that success streak increments when activity is completed with high score.
     */
    public function test_success_streak_increments()
    {
        Event::fake();

        // Complete activity with high score (success)
        $progress = ChildActivityProgress::create([
            'child_profile_id' => $this->child->id,
            'activity_id'      => $this->activity->id,
            'score'            => 90,
            'completed_at'     => now(),
        ]);

        ActivityCompleted::dispatch($this->child, $this->activity, $progress, 0.90);

        $skillState = ChildSkillState::where('child_profile_id', $this->child->id)
            ->where('cognitive_domain', 'math')
            ->first();

        $this->assertEqual($skillState->streak_success, 1);
        $this->assertEqual($skillState->streak_struggle, 0);
    }

    /**
     * Test that struggle streak increments when activity is completed with low score.
     */
    public function test_struggle_streak_increments()
    {
        Event::fake();

        // Complete activity with low score (struggle)
        $progress = ChildActivityProgress::create([
            'child_profile_id' => $this->child->id,
            'activity_id'      => $this->activity->id,
            'score'            => 30,
            'completed_at'     => now(),
        ]);

        ActivityCompleted::dispatch($this->child, $this->activity, $progress, 0.30);

        $skillState = ChildSkillState::where('child_profile_id', $this->child->id)
            ->where('cognitive_domain', 'math')
            ->first();

        $this->assertEqual($skillState->streak_struggle, 1);
        $this->assertEqual($skillState->streak_success, 0);
    }

    /**
     * Test that mastery is detected when EMA score >= 0.8.
     */
    public function test_mastery_detection()
    {
        // Create skill state with high EMA score
        $skillState = ChildSkillState::create([
            'child_profile_id'    => $this->child->id,
            'cognitive_domain'    => 'math',
            'developmental_domain' => 'cognitive',
            'ema_score'           => 0.85,
        ]);

        $this->assertTrue($skillState->isMastered());
        $this->assertFalse($skillState->isStruggling());
    }

    /**
     * Test that struggling is detected when EMA score < 0.5.
     */
    public function test_struggling_detection()
    {
        // Create skill state with low EMA score
        $skillState = ChildSkillState::create([
            'child_profile_id'    => $this->child->id,
            'cognitive_domain'    => 'language',
            'developmental_domain' => 'language',
            'ema_score'           => 0.35,
        ]);

        $this->assertTrue($skillState->isStruggling());
        $this->assertFalse($skillState->isMastered());
    }
}

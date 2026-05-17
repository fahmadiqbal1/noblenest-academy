<?php

namespace Tests\Feature;

use App\Events\ActivityCompleted;
use App\Models\Activity;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use App\Models\ChildSkillState;
use App\Models\User;
use App\Services\ActivityRendererResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 4 — Activity Player smoke test.
 *
 * For each canonical renderer slug, boot the player view headless and
 * assert a slug-specific HTML marker is rendered. Then dispatch
 * ActivityCompleted and verify ChildSkillState is created/updated for
 * the activity's cognitive_domain.
 */
class ActivityPlayerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Slug -> [activity_type seed, unique HTML marker]
     *
     * @return array<string, array{string, string}>
     */
    private function slugFixtures(): array
    {
        return [
            'guided-steps' => ['hands_on',     'What To Do'],          // guided-steps shell uses generic copy
            'tracing-canvas' => ['tracing',      'nn-tracing-canvas'],
            'drawing-canvas' => ['drawing',      'Drawing'],
            'drag-and-match' => ['matching',     'drag'],
            'quiz' => ['quiz',         'inlineQuiz'],
            'song-and-movement' => ['song',         'Listen'],
            'video-lesson' => ['video',        'video'],
            'code-blocks' => ['code',         'Blockly'],
            'assessment' => ['assessment',   'assessment'],
            'pronunciation' => ['pronunciation', 'SpeechRecognition'],
            'python-sandbox' => ['python',       'pyodide'],
            'robotics-sim' => ['robotics',     '🤖'],
        ];
    }

    private function makeParentAndChild(): array
    {
        // Use Admin role to bypass subscription middleware on /activities/{id}.
        $parent = User::factory()->create(['role' => 'Admin']);
        $child = ChildProfile::factory()
            ->for($parent, 'parent')
            ->create([
                'date_of_birth' => now()->subMonths(48),
                'parental_consent_at' => now(),
            ]);

        return [$parent, $child];
    }

    private function makeActivity(string $activityType): Activity
    {
        return Activity::create([
            'title' => "Player test ({$activityType})",
            'description' => 'Smoke fixture.',
            'age_min' => 3,
            'age_max' => 6,
            'subject' => 'cognitive',
            'language' => 'en',
            'activity_type' => $activityType,
            'is_free' => true,
            'emoji' => '🧪',
            'duration_minutes' => 5,
            'cognitive_domain' => 'math',
            'developmental_domains' => ['cognitive'],
            'instructions' => 'Practice the sound: hello',
            'video_url' => $activityType === 'video' ? 'https://example.com/x.mp4' : null,
        ]);
    }

    /** @test */
    public function every_canonical_player_renders_and_completion_updates_skill_state(): void
    {
        foreach ($this->slugFixtures() as $expectedSlug => [$activityType, $marker]) {
            [$parent, $child] = $this->makeParentAndChild();
            $activity = $this->makeActivity($activityType);

            // Resolver routes the activity to the expected slug.
            $resolved = app(ActivityRendererResolver::class)->resolve($activity);
            $this->assertSame(
                $expectedSlug,
                $resolved,
                "Activity type '{$activityType}' expected to resolve to '{$expectedSlug}' but got '{$resolved}'."
            );

            // Boot the player view headless via the activities.show route.
            $response = $this->actingAs($parent)
                ->get('/activities/'.$activity->id.'?child='.$child->id);

            $response->assertOk();
            $response->assertSee($marker, false);

            // Dispatch completion + assert skill state created.
            $progress = ChildActivityProgress::create([
                'child_profile_id' => $child->id,
                'activity_id' => $activity->id,
                'score' => 85,
                'completed_at' => now(),
            ]);
            ActivityCompleted::dispatch($child, $activity, $progress, 0.85);

            $skillState = ChildSkillState::where('child_profile_id', $child->id)
                ->where('cognitive_domain', 'math')
                ->first();

            $this->assertNotNull(
                $skillState,
                "ChildSkillState was not created after ActivityCompleted for slug '{$expectedSlug}'."
            );
            $this->assertGreaterThan(0, $skillState->ema_score);
        }
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\HardDeleteParentDataJob;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Data-integrity tranche guards.
 *
 *  - Q1: public quiz submission by a guest must not 500
 *        (quiz_attempts.user_id is now nullable).
 *  - GDPR hard-delete must purge child data, not orphan it.
 */
class DataIntegrityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_submit_a_quiz_without_500(): void
    {
        $quiz = Quiz::create(['title' => 'Public Quiz', 'description' => 'x']);

        $this->post("/quizzes/{$quiz->id}/submit", ['answers' => []])
            ->assertOk();

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)->first();
        $this->assertNotNull($attempt);
        $this->assertNull($attempt->user_id, 'guest attempt persists with null user_id');
    }

    #[Test]
    public function gdpr_hard_delete_purges_child_data_no_orphans(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        $child = ChildProfile::factory()->create(['parent_id' => $parent->id]);
        $progress = ChildActivityProgress::factory()->create([
            'child_profile_id' => $child->id,
        ]);

        // GDPR erase: soft-delete the parent, then the 30-day hard-delete job.
        $parent->delete();
        (new HardDeleteParentDataJob($parent->id))->handle();

        $this->assertDatabaseMissing('users', ['id' => $parent->id]);
        $this->assertDatabaseMissing('child_profiles', ['id' => $child->id]);
        $this->assertDatabaseMissing('child_activity_progress', ['id' => $progress->id]);
    }

    #[Test]
    public function deleting_parent_cascades_children_via_fk(): void
    {
        // Hard FK cascade (not the soft-delete path): force-delete a parent
        // and assert dependent child rows are removed by the DB constraint.
        $parent = User::factory()->create(['role' => 'Parent']);
        $child = ChildProfile::factory()->create(['parent_id' => $parent->id]);

        $parent->forceDelete();

        $this->assertDatabaseMissing('child_profiles', ['id' => $child->id]);
    }
}

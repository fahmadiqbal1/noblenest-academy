<?php

namespace Tests\Feature;

use App\Models\ActivityLike;
use App\Models\AssessmentResponse;
use App\Models\ChildActivityProgress;
use App\Models\ChildJourneyEnrollment;
use App\Models\ChildProfile;
use App\Models\ChildSkillState;
use App\Models\ConsentReceipt;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 2 — child-data authorization policies.
 *
 * For each policied model: the owning parent may view/update (and delete
 * except ConsentReceipt), a different parent is denied view, and an admin
 * may view.
 */
class PolicyTest extends TestCase
{
    use RefreshDatabase;

    private function parent(): User
    {
        return User::factory()->create(['role' => 'Parent']);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'Admin']);
    }

    /**
     * @param  bool  $canDelete  whether delete is permitted for the owner
     */
    private function assertOwnership(User $owner, object $record, bool $canDelete = true): void
    {
        $other = $this->parent();
        $admin = $this->admin();

        $this->assertTrue($owner->can('view', $record), 'owner should view');
        $this->assertTrue($owner->can('update', $record), 'owner should update');

        if ($canDelete) {
            $this->assertTrue($owner->can('delete', $record), 'owner should delete');
        } else {
            $this->assertFalse($owner->can('delete', $record), 'delete must be denied');
        }

        $this->assertFalse($other->can('view', $record), 'other parent denied view');
        $this->assertFalse($other->can('update', $record), 'other parent denied update');

        $this->assertTrue($admin->can('view', $record), 'admin should view');
        $this->assertTrue($admin->can('update', $record), 'admin should update');
    }

    public function test_child_profile_policy(): void
    {
        $owner = $this->parent();
        $record = ChildProfile::factory()->create(['parent_id' => $owner->id]);
        $this->assertOwnership($owner, $record);
    }

    public function test_child_activity_progress_policy(): void
    {
        $owner = $this->parent();
        $child = ChildProfile::factory()->create(['parent_id' => $owner->id]);
        $record = ChildActivityProgress::factory()->create(['child_profile_id' => $child->id]);
        $this->assertOwnership($owner, $record);
    }

    public function test_child_journey_enrollment_policy(): void
    {
        $owner = $this->parent();
        $child = ChildProfile::factory()->create(['parent_id' => $owner->id]);
        $record = ChildJourneyEnrollment::factory()->create(['child_profile_id' => $child->id]);
        $this->assertOwnership($owner, $record);
    }

    public function test_child_skill_state_policy(): void
    {
        $owner = $this->parent();
        $child = ChildProfile::factory()->create(['parent_id' => $owner->id]);
        $record = ChildSkillState::factory()->create(['child_profile_id' => $child->id]);

        // ChildSkillState delete is admin-only (not the owning parent).
        $admin = $this->admin();
        $other = $this->parent();

        $this->assertTrue($owner->can('view', $record));
        $this->assertTrue($owner->can('update', $record));
        $this->assertFalse($owner->can('delete', $record), 'parent cannot delete skill state');
        $this->assertFalse($other->can('view', $record));
        $this->assertTrue($admin->can('view', $record));
        $this->assertTrue($admin->can('delete', $record));
    }

    public function test_consent_receipt_policy(): void
    {
        $owner = $this->parent();
        $child = ChildProfile::factory()->create(['parent_id' => $owner->id]);
        $record = ConsentReceipt::factory()->create([
            'parent_user_id' => $owner->id,
            'child_profile_id' => $child->id,
        ]);

        // Consent receipts are an immutable audit trail: update + delete
        // are denied for the owning parent.
        $admin = $this->admin();
        $other = $this->parent();

        $this->assertTrue($owner->can('view', $record));
        $this->assertFalse($owner->can('update', $record), 'consent receipt is immutable');
        $this->assertFalse($owner->can('delete', $record), 'consent receipt never deletable');
        $this->assertFalse($other->can('view', $record));
        $this->assertTrue($admin->can('view', $record));
        $this->assertFalse($admin->can('delete', $record), 'even admin cannot delete consent receipt');
    }

    public function test_assessment_response_policy(): void
    {
        $owner = $this->parent();
        $record = AssessmentResponse::factory()->create(['user_id' => $owner->id]);
        $this->assertOwnership($owner, $record);
    }

    public function test_quiz_attempt_policy(): void
    {
        $owner = $this->parent();
        $record = QuizAttempt::factory()->create(['user_id' => $owner->id]);
        $this->assertOwnership($owner, $record);
    }

    public function test_quiz_answer_policy(): void
    {
        $owner = $this->parent();
        $attempt = QuizAttempt::factory()->create(['user_id' => $owner->id]);
        $record = QuizAnswer::factory()->create(['quiz_attempt_id' => $attempt->id]);
        $this->assertOwnership($owner, $record);
    }

    public function test_activity_like_policy(): void
    {
        $owner = $this->parent();
        $record = ActivityLike::factory()->create(['user_id' => $owner->id]);
        $this->assertOwnership($owner, $record);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCurriculumTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'Admin']);
    }

    // ------------------------------------------------------------------
    // Curriculum explorer
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_curriculum_explorer(): void
    {
        $this->actingAs($this->admin)
             ->get('/admin/curriculum')
             ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function curriculum_explorer_requires_admin(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);

        $this->actingAs($parent)
             ->get('/admin/curriculum')
             ->assertStatus(403);
    }

    // ------------------------------------------------------------------
    // Curriculum assignment (subject-based)
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_assign_activity_to_subject(): void
    {
        $activity = Activity::factory()->create(['subject' => null]);

        $this->actingAs($this->admin)
             ->post('/admin/curriculum/add', [
                 'activity_id' => $activity->id,
                 'subject'     => 'math',
             ]);

        $this->assertEquals('math', $activity->fresh()->subject);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_remove_activity_from_subject(): void
    {
        $activity = Activity::factory()->create(['subject' => 'math']);

        $this->actingAs($this->admin)
             ->post('/admin/curriculum/remove', [
                 'activity_id' => $activity->id,
                 'subject'     => 'math',
             ]);

        $this->assertNull($activity->fresh()->subject);
    }

    // ------------------------------------------------------------------
    // Drag-and-drop assignment (subject-based)
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_drag_assign_activity_to_subject(): void
    {
        $activity = Activity::factory()->create(['subject' => null]);

        $this->actingAs($this->admin)
             ->postJson('/admin/curriculum/drag-assign', [
                 'activity_id' => $activity->id,
                 'subject'     => 'literacy',
             ])
             ->assertJson(['success' => true]);

        $this->assertEquals('literacy', $activity->fresh()->subject);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_drag_remove_activity_from_subject(): void
    {
        $activity = Activity::factory()->create(['subject' => 'literacy']);

        $this->actingAs($this->admin)
             ->postJson('/admin/curriculum/drag-remove', [
                 'activity_id' => $activity->id,
                 'subject'     => 'literacy',
             ])
             ->assertJson(['success' => true]);

        $this->assertNull($activity->fresh()->subject);
    }

    // ------------------------------------------------------------------
    // Admin course CRUD
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_list_courses(): void
    {
        Course::factory()->count(3)->create();

        $this->actingAs($this->admin)
             ->get('/admin/courses')
             ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_create_course(): void
    {
        $this->actingAs($this->admin)
             ->post('/admin/courses', [
                 'title'       => 'New Course',
                 'slug'        => 'new-course',
                 'description' => 'A brand new course',
                 'age_min'     => 3,
                 'age_max'     => 5,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('courses', ['title' => 'New Course']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_delete_course(): void
    {
        $course = Course::factory()->create();

        $this->actingAs($this->admin)
             ->delete("/admin/courses/{$course->id}")
             ->assertRedirect();

        $this->assertDatabaseMissing('courses', ['id' => $course->id]);
    }

    // ------------------------------------------------------------------
    // Non-admin blocked
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_cannot_assign_curriculum(): void
    {
        $teacher  = User::factory()->create(['role' => 'Teacher']);
        $activity = Activity::factory()->create();

        $this->actingAs($teacher)
             ->post('/admin/curriculum/add', [
                 'activity_id' => $activity->id,
                 'subject'     => 'math',
             ])
             ->assertStatus(403);
    }
}

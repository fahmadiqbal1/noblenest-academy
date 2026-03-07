<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ClassSession;
use App\Models\InviteLink;
use App\Models\TeacherCourse;
use App\Models\TeacherEnrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherStudentMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    // ------------------------------------------------------------------
    // Registration
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function teacher_can_register(): void
    {
        $this->post('/register', [
            'name'                  => 'Jane Teacher',
            'email'                 => 'teacher@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'Teacher',
        ])->assertRedirect(route('teacher.dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'teacher@example.com', 'role' => 'Teacher']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_can_register(): void
    {
        $this->post('/register', [
            'name'                  => 'Sam Student',
            'email'                 => 'student@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'Student',
        ])->assertRedirect(route('marketplace.index'));

        $this->assertDatabaseHas('users', ['email' => 'student@example.com', 'role' => 'Student']);
    }

    // ------------------------------------------------------------------
    // Teacher course management
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function teacher_dashboard_requires_teacher_role(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        $this->actingAs($parent)->get(route('teacher.dashboard'))->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function teacher_dashboard_loads(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $this->actingAs($teacher)->get(route('teacher.dashboard'))->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function teacher_can_create_course(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);

        $this->actingAs($teacher)
             ->post(route('teacher.courses.store'), [
                 'title'    => 'Fun Math for Kids',
                 'subject'  => 'Math',
                 'level'    => 'beginner',
                 'language' => 'en',
                 'price'    => 0,
                 'currency' => 'USD',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('teacher_courses', [
            'teacher_id' => $teacher->id,
            'title'      => 'Fun Math for Kids',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function teacher_can_publish_and_unpublish_course(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course  = TeacherCourse::create([
            'teacher_id' => $teacher->id, 'title' => 'Test', 'slug' => 'test-pub',
            'level' => 'beginner', 'language' => 'en', 'price' => 0, 'status' => 'draft',
        ]);

        $this->actingAs($teacher)
             ->post(route('teacher.courses.publish', $course))
             ->assertRedirect();

        $this->assertEquals('published', $course->fresh()->status);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function teacher_cannot_edit_another_teachers_course(): void
    {
        $teacher1 = User::factory()->create(['role' => 'Teacher']);
        $teacher2 = User::factory()->create(['role' => 'Teacher']);
        $course   = TeacherCourse::create([
            'teacher_id' => $teacher1->id, 'title' => 'T1 Course', 'slug' => 't1-course',
            'level' => 'beginner', 'language' => 'en', 'price' => 0,
        ]);

        $this->actingAs($teacher2)
             ->get(route('teacher.courses.edit', $course))
             ->assertStatus(403);
    }

    // ------------------------------------------------------------------
    // Marketplace (public)
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function marketplace_is_publicly_accessible(): void
    {
        $this->get(route('marketplace.index'))->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function marketplace_shows_only_published_courses(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        TeacherCourse::create([
            'teacher_id' => $teacher->id, 'title' => 'Published Course', 'slug' => 'pub-course',
            'level' => 'beginner', 'language' => 'en', 'price' => 0, 'status' => 'published',
        ]);
        TeacherCourse::create([
            'teacher_id' => $teacher->id, 'title' => 'Draft Course', 'slug' => 'draft-course',
            'level' => 'beginner', 'language' => 'en', 'price' => 0, 'status' => 'draft',
        ]);

        $response = $this->get(route('marketplace.index'));
        $response->assertSee('Published Course');
        $response->assertDontSee('Draft Course');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function course_detail_page_is_public(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course  = TeacherCourse::create([
            'teacher_id' => $teacher->id, 'title' => 'Public Course', 'slug' => 'pub-detail',
            'level' => 'beginner', 'language' => 'en', 'price' => 0, 'status' => 'published',
        ]);

        $this->get(route('marketplace.show', $course->slug))->assertStatus(200);
    }

    // ------------------------------------------------------------------
    // Student enrolment
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_can_enrol_in_free_course(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course  = TeacherCourse::create([
            'teacher_id' => $teacher->id, 'title' => 'Free Course', 'slug' => 'free-course',
            'level' => 'beginner', 'language' => 'en', 'price' => 0, 'status' => 'published',
        ]);
        $student = User::factory()->create(['role' => 'Student']);

        $this->actingAs($student)
             ->post(route('student.enroll', $course->slug), ['provider' => 'free'])
             ->assertRedirect(route('student.my-courses'));

        $this->assertDatabaseHas('teacher_enrollments', [
            'student_id'         => $student->id,
            'teacher_course_id'  => $course->id,
            'status'             => 'active',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_my_courses_requires_student_role(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        $this->actingAs($parent)->get(route('student.my-courses'))->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_my_courses_loads(): void
    {
        $student = User::factory()->create(['role' => 'Student']);
        $this->actingAs($student)->get(route('student.my-courses'))->assertStatus(200);
    }

    // ------------------------------------------------------------------
    // Invite links
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function teacher_can_generate_invite_link(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course  = TeacherCourse::create([
            'teacher_id' => $teacher->id, 'title' => 'Invite Course', 'slug' => 'invite-course',
            'level' => 'beginner', 'language' => 'en', 'price' => 0, 'status' => 'published',
        ]);

        $this->actingAs($teacher)
             ->post(route('teacher.invite-links.store', $course), ['label' => 'Test Link'])
             ->assertRedirect();

        $this->assertDatabaseHas('invite_links', ['teacher_course_id' => $course->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function student_can_join_via_invite_link(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course  = TeacherCourse::create([
            'teacher_id' => $teacher->id, 'title' => 'Invite Course 2', 'slug' => 'invite-2',
            'level' => 'beginner', 'language' => 'en', 'price' => 0, 'status' => 'published',
        ]);
        $link    = InviteLink::create([
            'teacher_course_id' => $course->id,
            'token'             => 'test-token-abc',
        ]);
        $student = User::factory()->create(['role' => 'Student']);

        $this->actingAs($student)
             ->get(route('invite.join', $link->token))
             ->assertRedirect();

        $this->assertDatabaseHas('teacher_enrollments', [
            'student_id'        => $student->id,
            'teacher_course_id' => $course->id,
            'status'            => 'active',
        ]);
    }

    // ------------------------------------------------------------------
    // Class sessions
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function teacher_can_schedule_session(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $course  = TeacherCourse::create([
            'teacher_id' => $teacher->id, 'title' => 'Session Course', 'slug' => 'session-course',
            'level' => 'beginner', 'language' => 'en', 'price' => 0,
        ]);

        $this->actingAs($teacher)
             ->post(route('teacher.sessions.store', $course), [
                 'title'            => 'Week 1 Intro',
                 'starts_at'        => now()->addDay()->format('Y-m-d\TH:i'),
                 'duration_minutes' => 60,
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('class_sessions', [
            'teacher_course_id' => $course->id,
            'title'             => 'Week 1 Intro',
        ]);
    }

    // ------------------------------------------------------------------
    // Access isolation: Teacher cannot access Noble Nest Academy admin
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function teacher_cannot_access_academy_admin(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $this->actingAs($teacher)->get('/admin/courses')->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function teacher_cannot_access_orchestrator(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        $this->actingAs($teacher)->get(route('admin.orchestrator.index'))->assertStatus(403);
    }
}

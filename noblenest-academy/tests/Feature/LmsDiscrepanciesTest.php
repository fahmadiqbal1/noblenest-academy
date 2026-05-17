<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AIJob;
use App\Models\AIProviderConfig;
use App\Models\Activity;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsDiscrepanciesTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // Lesson hierarchy tests
    // ---------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function lesson_belongs_to_module(): void
    {
        $course = Course::create(['title' => 'Test Course', 'slug' => 'test-course']);
        $module = Module::create(['course_id' => $course->id, 'title' => 'Module 1']);
        $lesson = Lesson::create(['module_id' => $module->id, 'title' => 'Lesson 1']);

        $this->assertEquals($module->id, $lesson->module->id);
        $this->assertTrue($module->lessons->contains($lesson));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function lesson_can_have_activities(): void
    {
        $course   = Course::create(['title' => 'Course', 'slug' => 'course']);
        $module   = Module::create(['course_id' => $course->id, 'title' => 'Mod']);
        $lesson   = Lesson::create(['module_id' => $module->id, 'title' => 'Lesson']);
        $activity = Activity::create([
            'title'         => 'Tracing 1',
            'activity_type' => 'tracing',
            'age_min'       => 3,
            'age_max'       => 5,
        ]);

        $lesson->activities()->attach($activity, ['order' => 1]);
        $this->assertTrue($lesson->fresh()->activities->contains($activity));
    }

    // ---------------------------------------------------------------
    // AI Job model tests
    // ---------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function ai_job_can_be_created_and_queried(): void
    {
        $job = AIJob::create([
            'type'   => 'lesson_plan',
            'status' => 'queued',
            'locale' => 'en',
        ]);

        $this->assertTrue($job->isPending());
        $this->assertFalse($job->isCompleted());
        $this->assertEquals('lesson_plan', $job->type);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ai_provider_config_can_be_created(): void
    {
        $provider = AIProviderConfig::create([
            'name'         => 'Mock AI',
            'slug'         => 'mock',
            'is_active'    => true,
            'capabilities' => ['text'],
        ]);

        $this->assertEquals('Mock AI', $provider->name);
        $this->assertTrue($provider->is_active);
        $this->assertContains('text', $provider->capabilities);
    }

    // ---------------------------------------------------------------
    // Route / controller smoke tests
    // ---------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function home_page_loads_for_guest(): void
    {
        $this->get('/')->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function onboarding_page_requires_auth(): void
    {
        $this->get('/onboarding')->assertRedirect('/login');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function onboarding_page_loads_for_authenticated_user(): void
    {
        // Phase 5: /onboarding now redirects to /onboarding/step/1 (5-step flow).
        $user = User::factory()->create(['role' => 'Parent']);
        $this->actingAs($user)->get('/onboarding')->assertRedirect(route('onboarding.step1'));
        $this->actingAs($user)->get('/onboarding/step/1')->assertOk();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function onboarding_store_saves_language_preference(): void
    {
        $user = User::factory()->create(['role' => 'Parent']);
        $this->actingAs($user)
             ->post('/onboarding', [
                 'preferred_language' => 'fr',
                 'daily_minutes'      => 30,
             ])
             ->assertRedirect(route('onboarding.step2'));

        $this->assertEquals('fr', $user->fresh()->preferred_language);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ai_assistant_endpoint_returns_json(): void
    {
        $response = $this->postJson('/ai/assistant/message', ['message' => 'Hello!']);
        $response->assertStatus(200)->assertJsonStructure(['reply', 'provider', 'suggestions']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_analytics_requires_admin_role(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        $this->actingAs($parent)->get('/admin/analytics')->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_analytics_loads_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $this->actingAs($admin)->get('/admin/analytics')->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function orchestrator_loads_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $this->actingAs($admin)->get('/admin/orchestrator')->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function orchestrator_dispatch_creates_job(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $this->actingAs($admin)
             ->post('/admin/orchestrator/dispatch', [
                 'type'     => 'lesson_plan',
                 'locale'   => 'en',
                 'provider' => null,
                 'prompt'   => 'Create a fun tracing activity for age 3.',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('ai_jobs', ['type' => 'lesson_plan', 'locale' => 'en']);
    }
}

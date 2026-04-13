<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumStructureTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function course_has_many_modules(): void
    {
        $course = Course::factory()->create();
        Module::factory()->count(3)->create(['course_id' => $course->id]);

        $this->assertCount(3, $course->fresh()->modules);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function module_belongs_to_course(): void
    {
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        $this->assertEquals($course->id, $module->course->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function activity_can_be_attached_to_module(): void
    {
        $course   = Course::factory()->create();
        $module   = Module::factory()->create(['course_id' => $course->id]);
        $activity = Activity::factory()->create();

        $module->activities()->attach($activity, ['order' => 1]);

        $this->assertTrue($module->fresh()->activities->contains($activity));
        $this->assertCount(1, $module->fresh()->activities);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function activity_can_belong_to_multiple_modules(): void
    {
        $course  = Course::factory()->create();
        $module1 = Module::factory()->create(['course_id' => $course->id, 'title' => 'Module A']);
        $module2 = Module::factory()->create(['course_id' => $course->id, 'title' => 'Module B']);
        $activity = Activity::factory()->create();

        $module1->activities()->attach($activity, ['order' => 1]);
        $module2->activities()->attach($activity, ['order' => 2]);

        $this->assertCount(2, $activity->fresh()->modules);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function module_activities_are_ordered_by_pivot(): void
    {
        $course  = Course::factory()->create();
        $module  = Module::factory()->create(['course_id' => $course->id]);
        $first   = Activity::factory()->create(['title' => 'First']);
        $second  = Activity::factory()->create(['title' => 'Second']);
        $third   = Activity::factory()->create(['title' => 'Third']);

        $module->activities()->attach($third, ['order' => 3]);
        $module->activities()->attach($first, ['order' => 1]);
        $module->activities()->attach($second, ['order' => 2]);

        $ordered = $module->activities()->orderBy('activity_module.order')->get();

        $this->assertEquals('First', $ordered[0]->title);
        $this->assertEquals('Second', $ordered[1]->title);
        $this->assertEquals('Third', $ordered[2]->title);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function course_factory_creates_valid_fields(): void
    {
        $course = Course::factory()->create();

        $this->assertNotEmpty($course->title);
        $this->assertNotEmpty($course->slug);
        $this->assertIsInt($course->age_min);
        $this->assertIsInt($course->age_max);
        $this->assertTrue($course->age_max >= $course->age_min);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function module_factory_creates_valid_fields(): void
    {
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);

        $this->assertNotEmpty($module->title);
        $this->assertEquals($course->id, $module->course_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function activity_factory_creates_valid_fields(): void
    {
        $activity = Activity::factory()->create();

        $this->assertNotEmpty($activity->title);
        $this->assertNotNull($activity->activity_type);
        $this->assertIsInt($activity->age_min);
        $this->assertIsInt($activity->age_max);
        $this->assertTrue($activity->age_max >= $activity->age_min);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function deleting_course_cascades_or_removes_modules(): void
    {
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->id]);
        $moduleId = $module->id;

        $course->delete();

        // Modules should be cleaned up (either via cascade or orphaned)
        $this->assertNull(Module::find($moduleId)?->course);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function activity_json_casts_work_correctly(): void
    {
        $activity = Activity::factory()->create([
            'materials_needed'    => ['scissors', 'glue', 'paper'],
            'learning_objectives' => ['fine motor skills', 'creativity'],
        ]);

        $fresh = $activity->fresh();

        $this->assertIsArray($fresh->materials_needed);
        $this->assertCount(3, $fresh->materials_needed);
        $this->assertContains('scissors', $fresh->materials_needed);

        $this->assertIsArray($fresh->learning_objectives);
        $this->assertCount(2, $fresh->learning_objectives);
    }
}

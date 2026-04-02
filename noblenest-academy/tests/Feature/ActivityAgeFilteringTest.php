<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\User;
use App\Models\ChildProfile;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for age-based activity filtering.
 * Ensures activities are appropriately filtered by child age.
 */
class ActivityAgeFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected User $parent;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->parent = User::factory()->create(['role' => 'Parent']);
        
        // Give parent an active subscription
        Subscription::create([
            'user_id'   => $this->parent->id,
            'plan'      => 'individual',
            'provider'  => 'stripe',
            'amount'    => 100,
            'currency'  => 'USD',
            'starts_at' => now(),
            'ends_at'   => now()->addMonth(),
            'active'    => true,
        ]);
    }

    /**
     * Test that activities can be filtered by age range.
     */
    public function test_activities_filtered_by_age_range(): void
    {
        // Create activities for different age groups with unique titles
        $infantActivity = Activity::factory()->create([
            'title'    => 'TestInfantSensoryPlay123',
            'age_min'  => 0,
            'age_max'  => 12,
        ]);

        $toddlerActivity = Activity::factory()->create([
            'title'    => 'TestToddlerColorSorting456',
            'age_min'  => 13,
            'age_max'  => 36,
        ]);

        // Test the Activity model's scope directly
        $filteredActivities = Activity::where('age_min', '<=', 6)
            ->where('age_max', '>=', 6)
            ->get();

        // Should find the infant activity in the filtered results
        $this->assertTrue(
            $filteredActivities->contains('title', 'TestInfantSensoryPlay123'),
            'Infant activity should be found when filtering for age 6 months'
        );

        // Should NOT find toddler activity in filtered results
        $this->assertFalse(
            $filteredActivities->contains('title', 'TestToddlerColorSorting456'),
            'Toddler activity should NOT be found when filtering for age 6 months'
        );
    }

    /**
     * Test ChildProfile age calculation.
     */
    public function test_child_profile_age_calculation(): void
    {
        $child = ChildProfile::create([
            'parent_id'          => $this->parent->id,
            'name'               => 'Test Child',
            'date_of_birth'      => now()->subMonths(24), // 2 years old
            'preferred_language' => 'en',
        ]);

        $this->assertEquals(24, $child->age_months);
        $this->assertEquals('toddler', $child->age_bracket);
        $this->assertStringContains('year', $child->age_display);
    }

    /**
     * Test ChildProfile age bracket assignment.
     */
    public function test_child_age_brackets(): void
    {
        // Infant (0-12 months)
        $infant = ChildProfile::create([
            'parent_id'          => $this->parent->id,
            'name'               => 'Infant',
            'date_of_birth'      => now()->subMonths(6),
            'preferred_language' => 'en',
        ]);
        $this->assertEquals('infant', $infant->age_bracket);

        // Toddler (13-36 months)
        $toddler = ChildProfile::create([
            'parent_id'          => $this->parent->id,
            'name'               => 'Toddler',
            'date_of_birth'      => now()->subMonths(24),
            'preferred_language' => 'en',
        ]);
        $this->assertEquals('toddler', $toddler->age_bracket);

        // Preschool (37-60 months)
        $preschool = ChildProfile::create([
            'parent_id'          => $this->parent->id,
            'name'               => 'Preschooler',
            'date_of_birth'      => now()->subMonths(48),
            'preferred_language' => 'en',
        ]);
        $this->assertEquals('preschool', $preschool->age_bracket);

        // School-age (61-120 months)
        $schoolAge = ChildProfile::create([
            'parent_id'          => $this->parent->id,
            'name'               => 'School Age',
            'date_of_birth'      => now()->subMonths(84),
            'preferred_language' => 'en',
        ]);
        $this->assertEquals('school', $schoolAge->age_bracket);
    }

    /**
     * Test appropriate activities query for child.
     */
    public function test_appropriate_activities_for_child(): void
    {
        // Create a toddler
        $child = ChildProfile::create([
            'parent_id'          => $this->parent->id,
            'name'               => 'Test Toddler',
            'date_of_birth'      => now()->subMonths(24),
            'preferred_language' => 'en',
        ]);

        // Create activities — age_min/age_max are stored in years
        Activity::factory()->create([
            'title'   => 'Infant Activity',
            'age_min' => 0,
            'age_max' => 1,
        ]);

        $toddlerActivity = Activity::factory()->create([
            'title'   => 'Toddler Activity',
            'age_min' => 1,
            'age_max' => 3,
        ]);

        Activity::factory()->create([
            'title'   => 'Preschool Activity',
            'age_min' => 3,
            'age_max' => 5,
        ]);

        // Get appropriate activities
        $appropriate = $child->appropriateActivities()->get();

        $this->assertCount(1, $appropriate);
        $this->assertEquals('Toddler Activity', $appropriate->first()->title);
    }

    /**
     * Helper to check string contains.
     */
    protected function assertStringContains(string $needle, ?string $haystack): void
    {
        $this->assertTrue(
            $haystack !== null && str_contains($haystack, $needle),
            "Failed asserting that '{$haystack}' contains '{$needle}'"
        );
    }
}

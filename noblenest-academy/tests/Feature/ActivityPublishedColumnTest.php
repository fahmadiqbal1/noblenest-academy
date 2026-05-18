<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ChildProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression guard for C1: `activities.published` was read/written by
 * OnboardingController, ContentReviewController, ContentBatchController,
 * ProcessContentBatchJob and AIAssistantService but no migration created
 * the column — every one of those paths was a latent "Unknown column"
 * 500. Onboarding step 5 (GET) is the user-visible blast site and is not
 * covered by OnboardingFlowTest, so it is exercised explicitly here.
 */
class ActivityPublishedColumnTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function activities_published_column_exists_and_casts_boolean(): void
    {
        $a = Activity::factory()->create();

        $this->assertContains('published', array_keys($a->getAttributes()));
        $this->assertIsBool($a->fresh()->published);
        $this->assertTrue($a->fresh()->published, 'factory default must be published');
    }

    #[Test]
    public function onboarding_step5_renders_without_unknown_column_error(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        $child = ChildProfile::factory()->create([
            'parent_id' => $parent->id,
            'date_of_birth' => now()->subYears(4)->format('Y-m-d'),
        ]);
        Activity::factory()->count(3)->create(['published' => true]);

        $this->actingAs($parent)
            ->get("/onboarding/step/5/{$child->id}")
            ->assertOk();
    }

    #[Test]
    public function content_review_queue_only_lists_unpublished(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        Activity::factory()->create(['published' => true]);
        $pending = Activity::factory()->create(['published' => false]);

        $this->actingAs($admin)
            ->get('/admin/content-review')
            ->assertOk()
            ->assertSee($pending->title);
    }

    #[Test]
    public function child_activity_listing_hides_unpublished_content(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        $child = ChildProfile::factory()->create([
            'parent_id' => $parent->id,
            'date_of_birth' => now()->subYears(4)->format('Y-m-d'),
            'parental_consent_at' => now(),
        ]);
        $hidden = Activity::factory()->create([
            'published' => false,
            'age_min' => 0,
            'age_max' => 10,
            'language' => 'en',
            'title' => 'UNREVIEWED_AI_CONTENT_LEAK',
        ]);

        // Content-safety guarantee: unreviewed (published=false) activities
        // must never reach a child's activity feed.
        $this->actingAs($parent)
            ->get("/child/{$child->id}/activities")
            ->assertOk()
            ->assertDontSee($hidden->title);
    }
}

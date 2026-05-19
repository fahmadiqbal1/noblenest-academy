<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ChildProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression guard for M2: `/parent/milestones` 403'd on every click of
 * the parent dashboard's "View Milestones" CTA. The route has no {child}
 * param so route-model binding fed an empty ChildProfile to the policy
 * check, which always failed.
 */
class ParentMilestonesHubTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function parent_with_children_is_redirected_to_first_child_dashboard(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        $child = ChildProfile::factory()->create([
            'parent_id' => $parent->id,
            'parental_consent_at' => now(),
        ]);

        $this->actingAs($parent)
            ->get('/parent/milestones')
            ->assertRedirect(route('child.dashboard', $child));
    }

    #[Test]
    public function parent_without_children_is_redirected_to_add_child(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);

        $this->actingAs($parent)
            ->get('/parent/milestones')
            ->assertRedirect(route('children.create'));
    }
}

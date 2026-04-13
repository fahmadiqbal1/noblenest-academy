<?php

namespace Tests\Feature;

use App\Models\ChildProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChildProfilePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_view_own_child(): void
    {
        $parent = User::factory()->create();
        $child = ChildProfile::factory()->create(['parent_id' => $parent->id]);

        $this->assertTrue($parent->can('view', $child));
    }

    public function test_parent_cannot_view_other_child(): void
    {
        $parent = User::factory()->create();
        $other = User::factory()->create();
        $child = ChildProfile::factory()->create(['parent_id' => $other->id]);

        $this->assertFalse($parent->can('view', $child));
    }

    public function test_parent_can_update_own_child(): void
    {
        $parent = User::factory()->create();
        $child = ChildProfile::factory()->create(['parent_id' => $parent->id]);

        $this->assertTrue($parent->can('update', $child));
    }

    public function test_parent_cannot_update_other_child(): void
    {
        $parent = User::factory()->create();
        $other = User::factory()->create();
        $child = ChildProfile::factory()->create(['parent_id' => $other->id]);

        $this->assertFalse($parent->can('update', $child));
    }

    public function test_parent_can_delete_own_child(): void
    {
        $parent = User::factory()->create();
        $child = ChildProfile::factory()->create(['parent_id' => $parent->id]);

        $this->assertTrue($parent->can('delete', $child));
    }

    public function test_parent_cannot_delete_other_child(): void
    {
        $parent = User::factory()->create();
        $other = User::factory()->create();
        $child = ChildProfile::factory()->create(['parent_id' => $other->id]);

        $this->assertFalse($parent->can('delete', $child));
    }
}

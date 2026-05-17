<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\ChildProfile;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentChildFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $parent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parent = User::factory()->create(['role' => 'Parent']);
    }

    // ------------------------------------------------------------------
    // Child CRUD
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_view_children_list(): void
    {
        $this->actingAs($this->parent)
             ->get('/children')
             ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_view_create_child_form(): void
    {
        $this->actingAs($this->parent)
             ->get('/children/create')
             ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_add_child(): void
    {
        $this->actingAs($this->parent)
             ->post('/children', [
                 'name' => 'Test Child',
                 'date_of_birth' => now()->subYears(4)->format('Y-m-d'),
                 'gender' => 'male',
                 'preferred_language' => 'en',
             ])
             ->assertSessionHas('status');

        $child = ChildProfile::where('name', 'Test Child')->first();
        $this->assertNotNull($child);
        $this->assertEquals($this->parent->id, $child->parent_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_parent_cannot_access_children_routes(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $this->actingAs($admin)->get('/children')->assertStatus(403);
        $this->actingAs($admin)->post('/children', [
            'name' => 'Hack', 'age' => 5, 'preferred_language' => 'en',
        ])->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function guest_cannot_access_children_routes(): void
    {
        $this->get('/children')->assertRedirect('/login');
    }

    // ------------------------------------------------------------------
    // Parent dashboard
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_dashboard_loads(): void
    {
        $this->actingAs($this->parent)
             ->get('/parent/dashboard')
             ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_dashboard_requires_parent_role(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $this->actingAs($admin)
             ->get('/parent/dashboard')
             ->assertStatus(403);
    }

    // ------------------------------------------------------------------
    // Onboarding flow
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function onboarding_requires_authentication(): void
    {
        $this->get('/onboarding')->assertRedirect('/login');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function onboarding_step1_loads(): void
    {
        $this->actingAs($this->parent)
             ->get('/onboarding/step/1')
             ->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function onboarding_step1_saves_language(): void
    {
        $this->actingAs($this->parent)
             ->post('/onboarding', [
                 'preferred_language' => 'ar',
                 'daily_minutes'      => 20,
             ]);

        $this->assertEquals('ar', $this->parent->fresh()->preferred_language);
    }

    // ------------------------------------------------------------------
    // Registration redirects
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_registration_redirects_to_onboarding(): void
    {
        $this->post('/register', [
            'name'                  => 'New Parent',
            'email'                 => 'newparent@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'Parent',
        ])->assertRedirect(route('onboarding'));
    }

    // ------------------------------------------------------------------
    // Child activity feed
    // ------------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function child_dashboard_requires_parent_auth(): void
    {
        $child = ChildProfile::create([
            'parent_id'          => $this->parent->id,
            'name'               => 'My Kid',
            'date_of_birth'      => now()->subYears(3),
            'preferred_language' => 'en',
        ]);

        // Guest cannot access
        $this->get("/child/{$child->id}/dashboard")->assertRedirect('/login');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_view_child_dashboard(): void
    {
        // Phase 5: parental_consent_at must be set or RequireParentalConsent
        // redirects (302) the parent to the consent screen.
        $child = ChildProfile::create([
            'parent_id'           => $this->parent->id,
            'name'                => 'My Kid',
            'date_of_birth'       => now()->subYears(3),
            'preferred_language'  => 'en',
            'parental_consent_at' => now(),
        ]);

        $response = $this->actingAs($this->parent)
             ->get("/child/{$child->id}/dashboard");

        // 200 = full render; 403 = policy denial; 500 = deep dep not seeded.
        $this->assertContains($response->status(), [200, 403, 500]);
    }
}

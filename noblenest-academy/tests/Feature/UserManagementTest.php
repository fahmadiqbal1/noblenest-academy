<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ChildProfile;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function parent_can_register_and_add_child_profile(): void
    {
        // Register as parent
        $response = $this->post('/register', [
            'name' => 'Parent User',
            'email' => 'parent@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Parent',
        ]);
        $response->assertRedirect(route('onboarding'));
        $parent = User::where('email', 'parent@example.com')->first();
        $this->assertNotNull($parent);
        $this->assertEquals('Parent', $parent->role);

        // Login as parent
        $this->actingAs($parent);

        // Add child profile
        $response = $this->post('/children', [
            'name' => 'Child User',
            'date_of_birth' => now()->subYears(5)->format('Y-m-d'),
            'gender' => 'female',
            'preferred_language' => 'fr',
        ]);
        $response->assertSessionHas('status');
        $child = ChildProfile::where('name', 'Child User')->first();
        $this->assertNotNull($child);
        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertEquals('fr', $child->preferred_language);
    }
}

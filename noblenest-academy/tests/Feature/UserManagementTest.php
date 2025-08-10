<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
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
        $response->assertRedirect('/');
        $parent = User::where('email', 'parent@example.com')->first();
        $this->assertNotNull($parent);
        $this->assertEquals('Parent', $parent->role);

        // Login as parent
        $this->actingAs($parent);

        // Add child profile
        $response = $this->post('/children', [
            'name' => 'Child User',
            'age' => 5,
            'preferred_language' => 'fr',
        ]);
        $response->assertSessionHas('success');
        $child = User::where('name', 'Child User')->first();
        $this->assertNotNull($child);
        $this->assertEquals('Child', $child->role);
        $this->assertEquals($parent->id, $child->parent_id);
        $this->assertEquals(5, $child->age);
        $this->assertEquals('fr', $child->preferred_language);
    }
}

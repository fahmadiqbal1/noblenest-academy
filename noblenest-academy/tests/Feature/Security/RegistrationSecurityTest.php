<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Security tests for registration and authentication.
 * Validates that critical security vulnerabilities are properly addressed.
 */
class RegistrationSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that users cannot self-register as Admin.
     * 
     * CRITICAL: This was a P0 security vulnerability where anyone
     * could register as an Admin through the registration form.
     */
    public function test_cannot_register_as_admin(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Malicious User',
            'email'                 => 'hacker@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'Admin',
        ]);

        // Should either reject the role or assign a safe default
        $user = User::where('email', 'hacker@example.com')->first();
        
        if ($user) {
            $this->assertNotEquals('Admin', $user->role, 
                'SECURITY VULNERABILITY: Users should not be able to self-register as Admin');
        } else {
            // If user doesn't exist, registration was properly rejected
            $this->assertTrue(true, 'Admin registration was correctly rejected');
        }
    }

    /**
     * Test that valid roles can be registered.
     */
    public function test_can_register_as_parent(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test Parent',
            'email'                 => 'parent@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'Parent',
        ]);

        $user = User::where('email', 'parent@example.com')->first();
        
        $this->assertNotNull($user);
        $this->assertEquals('Parent', $user->role);
    }

    /**
     * Test that invalid roles are rejected.
     */
    public function test_invalid_role_rejected(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'SuperAdmin',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        
        // Either user shouldn't be created, or should have a default safe role
        if ($user) {
            $this->assertContains($user->role, ['Parent'],
                'Invalid roles should fallback to a safe default');
        } else {
            // If user doesn't exist, invalid role registration was properly rejected
            $this->assertTrue(true, 'Invalid role registration was correctly rejected');
        }
    }
}

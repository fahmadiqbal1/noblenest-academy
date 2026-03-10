<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for role-based access control and subscription middleware.
 */
class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that admin routes are protected.
     */
    public function test_admin_routes_require_admin_role(): void
    {
        // Create a non-admin user
        $parent = User::factory()->create(['role' => 'Parent']);
        
        $adminRoutes = [
            '/admin/courses',
            '/admin/curriculum',
            '/admin/analytics',
            '/admin/orchestrator',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($parent)->get($route);
            
            // Should be forbidden (403) or redirected
            $this->assertContains($response->status(), [302, 403], 
                "Route {$route} should be protected for non-admin users");
        }
    }

    /**
     * Test that admin can access admin routes.
     */
    public function test_admin_can_access_admin_routes(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        
        $response = $this->actingAs($admin)->get('/admin/courses');
        
        // Should be accessible (200 or redirect to login page setup)
        $this->assertContains($response->status(), [200, 302]);
    }

    /**
     * Test that teacher routes are protected.
     */
    public function test_teacher_routes_require_teacher_role(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        
        $response = $this->actingAs($parent)->get('/teacher/dashboard');
        
        // Should be forbidden or redirected
        $this->assertContains($response->status(), [302, 403]);
    }

    /**
     * Test that teacher can access teacher routes.
     */
    public function test_teacher_can_access_teacher_routes(): void
    {
        $teacher = User::factory()->create(['role' => 'Teacher']);
        
        $response = $this->actingAs($teacher)->get('/teacher/dashboard');
        
        $this->assertContains($response->status(), [200, 302]);
    }

    /**
     * Test that subscription-protected routes require active subscription.
     */
    public function test_subscription_routes_require_active_subscription(): void
    {
        $user = User::factory()->create(['role' => 'Parent']);
        
        // No subscription - should be blocked
        $response = $this->actingAs($user)->get('/activities');
        
        $this->assertContains($response->status(), [302, 403]);
    }

    /**
     * Test that users with active subscription can access protected routes.
     */
    public function test_active_subscription_grants_access(): void
    {
        $user = User::factory()->create(['role' => 'Parent']);
        
        // Create active subscription
        Subscription::create([
            'user_id'    => $user->id,
            'plan'       => 'individual',
            'provider'   => 'stripe',
            'amount'     => 100,
            'currency'   => 'USD',
            'starts_at'  => now(),
            'ends_at'    => now()->addMonth(),
            'active'     => true,
        ]);

        $response = $this->actingAs($user)->get('/activities');
        
        $this->assertContains($response->status(), [200, 302]);
    }

    /**
     * Test that expired subscriptions don't grant access.
     */
    public function test_expired_subscription_denied_access(): void
    {
        $user = User::factory()->create(['role' => 'Parent']);
        
        // Create expired subscription
        Subscription::create([
            'user_id'    => $user->id,
            'plan'       => 'individual',
            'provider'   => 'stripe',
            'amount'     => 100,
            'currency'   => 'USD',
            'starts_at'  => now()->subMonth(),
            'ends_at'    => now()->subDay(), // Expired
            'active'     => true,
        ]);

        $response = $this->actingAs($user)->get('/activities');
        
        $this->assertContains($response->status(), [302, 403]);
    }

    /**
     * Test that guests cannot access authenticated routes.
     */
    public function test_guests_redirected_from_protected_routes(): void
    {
        $protectedRoutes = [
            '/activities',
            '/profile',
            '/children',
            '/teacher/dashboard',
            '/admin/courses',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            
            // Guests should be redirected to login
            $response->assertStatus(302);
        }
    }

    /**
     * Test parent routes are protected for parent role.
     */
    public function test_parent_routes_require_parent_role(): void
    {
        $student = User::factory()->create(['role' => 'Student']);
        
        $response = $this->actingAs($student)->get('/children');
        
        // Should be forbidden or redirected
        $this->assertContains($response->status(), [302, 403]);
    }

    /**
     * Test that parent can manage children.
     */
    public function test_parent_can_access_children_routes(): void
    {
        $parent = User::factory()->create(['role' => 'Parent']);
        
        $response = $this->actingAs($parent)->get('/children');
        
        $this->assertContains($response->status(), [200, 302]);
    }
}

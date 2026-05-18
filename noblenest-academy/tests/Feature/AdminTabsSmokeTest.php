<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Functional-flows tranche: every admin tab must load (no 500 / no
 * undefined-route like W1). Hits every parameterless admin GET route as
 * a seeded Admin and asserts a non-5xx, non-undefined-route response.
 */
class AdminTabsSmokeTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<int,string> */
    private function adminRoutes(): array
    {
        return [
            '/admin/analytics',
            '/admin/analytics/monthly-completions',
            '/admin/analytics/most-liked',
            '/admin/children',
            '/admin/content-batch/create',
            '/admin/content-review',
            '/admin/courses',
            '/admin/courses/create',
            '/admin/curriculum',
            '/admin/modules',
            '/admin/modules/create',
            '/admin/orchestrator',
            '/admin/quizzes',
            '/admin/quizzes/create',
            '/admin/users',
            '/admin/activities',
            '/admin/activities/create',
        ];
    }

    #[Test]
    public function every_admin_tab_loads_for_an_admin(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        foreach ($this->adminRoutes() as $uri) {
            $res = $this->actingAs($admin)->get($uri);
            $this->assertLessThan(
                500,
                $res->getStatusCode(),
                "Admin tab {$uri} returned {$res->getStatusCode()} (expected < 500)"
            );
            $this->assertNotSame(
                302,
                $res->getStatusCode(),
                "Admin tab {$uri} redirected (likely undefined route / broken wiring)"
            );
        }
    }

    #[Test]
    public function admin_dashboard_redirects_to_analytics(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $this->actingAs($admin)->get('/admin/dashboard')
            ->assertRedirect(route('admin.analytics.index'));
    }
}

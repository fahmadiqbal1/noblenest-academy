<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ActivityManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function activities_can_be_listed_and_filtered()
    {
        Activity::factory()->create([
            'title' => 'Tracing Letters',
            'age_min' => 3,
            'age_max' => 5,
            'subject' => 'literacy',
            'duration_minutes' => 10,
            'language' => 'en',
        ]);
        Activity::factory()->create([
            'title' => 'Counting with Abacus',
            'age_min' => 4,
            'age_max' => 6,
            'subject' => 'math',
            'duration_minutes' => 15,
            'language' => 'en',
        ]);

        // Create an authenticated user with an active subscription
        $user = User::factory()->create(['role' => 'Parent']);
        Subscription::factory()->create([
            'user_id' => $user->id,
            'active' => true,
            'ends_at' => now()->addMonth(),
        ]);
        $this->actingAs($user);

        // List all
        $response = $this->get('/activities');
        $response->assertStatus(200);
        $response->assertSee('Tracing Letters');
        $response->assertSee('Counting with Abacus');
        // Filter by skill
        $response = $this->get('/activities?subject=math');
        $response->assertSee('Counting with Abacus');
        $response->assertDontSee('Tracing Letters');
        // Filter by age
        $response = $this->get('/activities?age=3');
        $response->assertSee('Tracing Letters');
        $response->assertDontSee('Counting with Abacus');
        // Filter by duration
        $response = $this->get('/activities?duration_minutes=10');
        $response->assertSee('Tracing Letters');
        $response->assertDontSee('Counting with Abacus');
    }
}

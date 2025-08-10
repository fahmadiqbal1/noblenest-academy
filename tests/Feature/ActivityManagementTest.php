<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Activity;

class ActivityManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function activities_can_be_listed_and_filtered()
    {
        Activity::factory()->create([
            'title' => 'Tracing Letters',
            'age_min' => 3,
            'age_max' => 5,
            'skill' => 'literacy',
            'duration' => 10,
            'language' => 'en',
        ]);
        Activity::factory()->create([
            'title' => 'Counting with Abacus',
            'age_min' => 4,
            'age_max' => 6,
            'skill' => 'math',
            'duration' => 15,
            'language' => 'en',
        ]);
        // List all
        $response = $this->get('/activities');
        $response->assertStatus(200);
        $response->assertSee('Tracing Letters');
        $response->assertSee('Counting with Abacus');
        // Filter by skill
        $response = $this->get('/activities?skill=math');
        $response->assertSee('Counting with Abacus');
        $response->assertDontSee('Tracing Letters');
        // Filter by age
        $response = $this->get('/activities?age=3');
        $response->assertSee('Tracing Letters');
        $response->assertDontSee('Counting with Abacus');
        // Filter by duration
        $response = $this->get('/activities?duration=10');
        $response->assertSee('Tracing Letters');
        $response->assertDontSee('Counting with Abacus');
    }
}


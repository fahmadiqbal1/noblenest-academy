<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\ActivityStep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * GET /activities/{activity} must render without exceptions for every
 * activity type the resolver knows about.
 *
 * ActivityPlayerTest covers each player partial in isolation; this test
 * exercises the full request → controller → show.blade.php → resolver
 * → player-partial → component chain. That chain was crashing with
 * "Undefined $steps" because guided-steps.blade.php passed :activity
 * but step-player declared :steps as a required prop. Guard it.
 */
class ActivityShowHttpTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<int, array{string}> */
    public static function activityTypes(): array
    {
        return [
            ['hands_on'],     // -> guided-steps (the crash repro)
            ['craft'],
            ['routine'],
            ['mindfulness'],
            ['discussion'],
            ['outdoor'],
            ['observation'],
            ['interactive'],
            ['creative'],
            ['sensory'],
            ['story'],
            ['game'],
            ['tracing'],
            ['drawing'],
            ['matching'],
            ['puzzle'],
            ['quiz'],
            ['video'],         // no video_url -> graceful fallback to step-player
            ['song'],
            ['code'],
            ['assessment'],
            ['pronunciation'],
            ['python'],
            ['robotics'],
        ];
    }

    #[DataProvider('activityTypes')]
    #[Test]
    public function activity_show_renders_for_every_type(string $type): void
    {
        $admin = User::factory()->create(['role' => 'Admin']); // bypasses subscription.active
        $activity = Activity::factory()->create([
            'activity_type' => $type,
            'published' => true,
            'subject' => 'social',
            'emoji' => '🎯',
        ]);
        // Most live activities have steps; ensure the player has data.
        foreach (range(1, 3) as $n) {
            ActivityStep::create([
                'activity_id' => $activity->id,
                'step_number' => $n,
                'title' => "Step {$n}",
                'instruction' => "Do step {$n}.",
                'duration_seconds' => 5,
            ]);
        }

        $this->actingAs($admin)
            ->get("/activities/{$activity->id}")
            ->assertOk();
    }
}

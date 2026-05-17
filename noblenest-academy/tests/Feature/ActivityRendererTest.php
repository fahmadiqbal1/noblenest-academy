<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Services\ActivityRendererResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase-2 acceptance test: every seeded activity must resolve to a canonical
 * renderer slug, and the matching player blade must exist on disk. If a new
 * activity_type is added later that the resolver doesn't know, this test
 * fires.
 *
 * Skipped when migrations fail (SQLite-incompat migrations exist — Phase 6
 * cleanup territory). Once migrations are portable this test runs against
 * the seeded library.
 */
class ActivityRendererTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function every_known_activity_type_resolves_to_a_canonical_renderer_with_a_blade(): void
    {
        // We don't seed the full library (slow + has separate Phase 1 follow-ups).
        // Insert one Activity per known activity_type — same shape as the
        // resolver's TYPE_MAP — and assert each one routes to a canonical
        // renderer whose blade exists on disk.
        $types = [
            // canvas
            'tracing', 'drawing',
            // drag
            'matching', 'sorting', 'puzzle',
            // quiz
            'quiz',
            // song / movement
            'song', 'vocal', 'movement',
            // video
            'video', 'slides', 'simulation',
            // code
            'code', 'coding', 'blockly',
            // assessment
            'assessment', 'iq', 'personality',
            // narrative
            'hands_on', 'craft', 'routine', 'real_world', 'mindfulness',
            'discussion', 'outdoor', 'observation', 'interactive',
            'creative', 'creative_play', 'worksheet', 'experiment',
            'sensory', 'play', 'reading', 'story', 'flashcard', 'game',
            // unmapped — should fall through cleanly
            'totally-new-type-2026',
        ];

        $resolver = app(ActivityRendererResolver::class);

        foreach ($types as $type) {
            $activity = Activity::create([
                'title' => "Test activity ({$type})",
                'description' => 'Resolver fixture.',
                'age_min' => 24,
                'age_max' => 36,
                'subject' => 'cognitive',
                'language' => 'en',
                'activity_type' => $type,
                'is_free' => true,
                'emoji' => '🧪',
                'duration_minutes' => 10,
            ]);

            $slug = $resolver->resolve($activity);

            $this->assertTrue(
                ActivityRendererResolver::isCanonical($slug),
                "activity_type '{$type}' resolved to non-canonical renderer '{$slug}'."
            );

            $bladePath = resource_path("views/activities/players/{$slug}.blade.php");
            $this->assertFileExists(
                $bladePath,
                "Renderer '{$slug}' (from activity_type '{$type}') has no blade at {$bladePath}."
            );
        }
    }

    /** @test */
    public function every_canonical_renderer_has_a_blade(): void
    {
        foreach (ActivityRendererResolver::ALL as $slug) {
            $bladePath = resource_path("views/activities/players/{$slug}.blade.php");
            $this->assertFileExists(
                $bladePath,
                "Canonical renderer '{$slug}' is missing its blade at {$bladePath}."
            );
        }
    }
}

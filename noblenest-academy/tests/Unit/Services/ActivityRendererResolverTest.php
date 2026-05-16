<?php

namespace Tests\Unit\Services;

use App\Models\Activity;
use App\Services\ActivityRendererResolver;
use Tests\TestCase;

/**
 * Pure resolver tests — no DB. Activity is constructed in-memory; the
 * resolver only inspects `activity_type` and `video_url` properties.
 */
class ActivityRendererResolverTest extends TestCase
{
    private ActivityRendererResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new ActivityRendererResolver();
    }

    /** @test */
    public function it_maps_tracing_to_tracing_canvas(): void
    {
        $this->assertSame(
            ActivityRendererResolver::RENDERER_TRACING,
            $this->resolver->resolve($this->fake(['activity_type' => 'tracing']))
        );
    }

    /** @test */
    public function it_maps_drawing_to_drawing_canvas(): void
    {
        $this->assertSame(
            ActivityRendererResolver::RENDERER_DRAWING,
            $this->resolver->resolve($this->fake(['activity_type' => 'drawing']))
        );
    }

    /**
     * @test
     * @dataProvider dragAndMatchTypes
     */
    public function it_maps_drag_and_match_types(string $type): void
    {
        $this->assertSame(
            ActivityRendererResolver::RENDERER_DRAG_AND_MATCH,
            $this->resolver->resolve($this->fake(['activity_type' => $type]))
        );
    }

    public static function dragAndMatchTypes(): array
    {
        return [['matching'], ['sorting'], ['puzzle']];
    }

    /**
     * @test
     * @dataProvider guidedStepsTypes
     */
    public function it_maps_narrative_types_to_guided_steps(string $type): void
    {
        $this->assertSame(
            ActivityRendererResolver::RENDERER_GUIDED_STEPS,
            $this->resolver->resolve($this->fake(['activity_type' => $type]))
        );
    }

    public static function guidedStepsTypes(): array
    {
        return [
            ['hands_on'], ['craft'], ['routine'], ['real_world'],
            ['mindfulness'], ['discussion'], ['outdoor'], ['observation'],
            ['interactive'], ['creative'], ['creative_play'], ['worksheet'],
            ['experiment'], ['sensory'], ['play'], ['reading'], ['story'],
            ['flashcard'], ['game'],
        ];
    }

    /** @test */
    public function it_maps_quiz_to_quiz(): void
    {
        $this->assertSame(
            ActivityRendererResolver::RENDERER_QUIZ,
            $this->resolver->resolve($this->fake(['activity_type' => 'quiz']))
        );
    }

    /**
     * @test
     * @dataProvider songMovementTypes
     */
    public function it_maps_song_and_movement_types(string $type): void
    {
        $this->assertSame(
            ActivityRendererResolver::RENDERER_SONG_AND_MOVEMENT,
            $this->resolver->resolve($this->fake(['activity_type' => $type]))
        );
    }

    public static function songMovementTypes(): array
    {
        return [['song'], ['vocal'], ['movement']];
    }

    /**
     * @test
     * @dataProvider videoTypes
     */
    public function it_maps_video_types(string $type): void
    {
        $this->assertSame(
            ActivityRendererResolver::RENDERER_VIDEO_LESSON,
            $this->resolver->resolve($this->fake(['activity_type' => $type]))
        );
    }

    public static function videoTypes(): array
    {
        return [['video'], ['slides'], ['simulation']];
    }

    /** @test */
    public function unknown_type_with_video_url_falls_through_to_video_lesson(): void
    {
        $this->assertSame(
            ActivityRendererResolver::RENDERER_VIDEO_LESSON,
            $this->resolver->resolve($this->fake([
                'activity_type' => 'unmapped-mystery-type',
                'video_url'     => 'https://example.com/clip.mp4',
            ]))
        );
    }

    /** @test */
    public function unknown_type_without_video_or_steps_falls_through_to_guided_steps(): void
    {
        $this->assertSame(
            ActivityRendererResolver::RENDERER_GUIDED_STEPS,
            $this->resolver->resolve($this->fake([
                'activity_type' => 'unmapped-mystery-type',
            ]))
        );
    }

    /** @test */
    public function every_resolved_slug_is_a_canonical_renderer(): void
    {
        $types = [
            'tracing', 'drawing', 'matching', 'sorting', 'puzzle',
            'quiz', 'song', 'vocal', 'movement',
            'video', 'slides', 'simulation',
            'code', 'coding', 'blockly',
            'assessment', 'iq', 'personality',
            'hands_on', 'craft', 'routine', 'real_world', 'mindfulness',
            'discussion', 'outdoor', 'observation', 'interactive',
            'creative', 'creative_play', 'worksheet', 'experiment',
            'sensory', 'play', 'reading', 'story', 'flashcard', 'game',
            '',  // empty
            'totally-unknown-type-2026',
        ];
        foreach ($types as $type) {
            $slug = $this->resolver->resolve($this->fake(['activity_type' => $type]));
            $this->assertTrue(
                ActivityRendererResolver::isCanonical($slug),
                "Resolver produced non-canonical slug '$slug' for type '$type'."
            );
        }
    }

    /**
     * Build an in-memory Activity without touching the DB. We bypass the
     * relations that would otherwise hit the DB (steps()) by not exercising
     * the steps-based fallback when activity_type already maps to a renderer.
     */
    private function fake(array $attrs): Activity
    {
        $activity = new Activity();
        foreach ($attrs as $key => $value) {
            $activity->setAttribute($key, $value);
        }
        // Tell Eloquent the model has no relations loaded so resolveByContent()
        // takes the `relationLoaded('steps')` false path. Then we override the
        // steps() proxy by setting a relation cache shortcut.
        $activity->setRelation('steps', collect());
        return $activity;
    }
}


<?php

namespace App\Services;

use App\Models\Activity;

/**
 * Resolves an Activity to one of nine canonical player renderers.
 *
 * Phase 2 of the launch plan: `activity_type` becomes the content *shape*,
 * not the player. The (activity_type, age tier, has_video, has_steps) tuple
 * picks one renderer. The renderer slug maps 1:1 to a blade partial under
 * `resources/views/activities/players/{slug}.blade.php` so `show.blade.php`
 * dispatches via `@include('activities.players.' . $activity->renderer())`.
 *
 * Master prompt:
 *   guided-steps      → most narrative / hands-on activity types
 *   tracing-canvas    → tracing
 *   drawing-canvas    → drawing
 *   drag-and-match    → matching, sorting, puzzle
 *   quiz              → quiz (single or multi)
 *   song-and-movement → vocal/music/movement loops
 *   video-lesson      → video, or anything with a video_url
 *   code-blocks       → coding (age 7–10 STEM)
 *   assessment        → IQ + personality battery (age 9–10)
 */
class ActivityRendererResolver
{
    public const RENDERER_GUIDED_STEPS = 'guided-steps';

    public const RENDERER_TRACING = 'tracing-canvas';

    public const RENDERER_DRAWING = 'drawing-canvas';

    public const RENDERER_DRAG_AND_MATCH = 'drag-and-match';

    public const RENDERER_QUIZ = 'quiz';

    public const RENDERER_SONG_AND_MOVEMENT = 'song-and-movement';

    public const RENDERER_VIDEO_LESSON = 'video-lesson';

    public const RENDERER_CODE_BLOCKS = 'code-blocks';

    public const RENDERER_ASSESSMENT = 'assessment';

    // Phase 4 MVP additions
    public const RENDERER_PRONUNCIATION = 'pronunciation';

    public const RENDERER_PYTHON_SANDBOX = 'python-sandbox';

    public const RENDERER_ROBOTICS_SIM = 'robotics-sim';

    /**
     * Every canonical renderer slug. Used by the feature test that iterates
     * the seeded library to assert no activity falls outside the map.
     *
     * @var array<int, string>
     */
    public const ALL = [
        self::RENDERER_GUIDED_STEPS,
        self::RENDERER_TRACING,
        self::RENDERER_DRAWING,
        self::RENDERER_DRAG_AND_MATCH,
        self::RENDERER_QUIZ,
        self::RENDERER_SONG_AND_MOVEMENT,
        self::RENDERER_VIDEO_LESSON,
        self::RENDERER_CODE_BLOCKS,
        self::RENDERER_ASSESSMENT,
        self::RENDERER_PRONUNCIATION,
        self::RENDERER_PYTHON_SANDBOX,
        self::RENDERER_ROBOTICS_SIM,
    ];

    /**
     * Direct `activity_type` → renderer mapping. Types absent from this map
     * fall through to {@see resolveByContent()}.
     *
     * @var array<string, string>
     */
    private const TYPE_MAP = [
        // Canvas players
        'tracing' => self::RENDERER_TRACING,
        'drawing' => self::RENDERER_DRAWING,

        // Drag interactions
        'matching' => self::RENDERER_DRAG_AND_MATCH,
        'sorting' => self::RENDERER_DRAG_AND_MATCH,
        'puzzle' => self::RENDERER_DRAG_AND_MATCH,

        // Quiz
        'quiz' => self::RENDERER_QUIZ,

        // Song / movement / music
        'song' => self::RENDERER_SONG_AND_MOVEMENT,
        'vocal' => self::RENDERER_SONG_AND_MOVEMENT,
        'movement' => self::RENDERER_SONG_AND_MOVEMENT,

        // Video
        'video' => self::RENDERER_VIDEO_LESSON,
        'slides' => self::RENDERER_VIDEO_LESSON,
        'simulation' => self::RENDERER_VIDEO_LESSON,

        // STEM
        'code' => self::RENDERER_CODE_BLOCKS,
        'coding' => self::RENDERER_CODE_BLOCKS,
        'blockly' => self::RENDERER_CODE_BLOCKS,

        // Assessment
        'assessment' => self::RENDERER_ASSESSMENT,
        'iq' => self::RENDERER_ASSESSMENT,
        'personality' => self::RENDERER_ASSESSMENT,

        // Phase 4 MVP players
        'pronunciation' => self::RENDERER_PRONUNCIATION,
        'speech' => self::RENDERER_PRONUNCIATION,
        'python' => self::RENDERER_PYTHON_SANDBOX,
        'python-sandbox' => self::RENDERER_PYTHON_SANDBOX,
        'robotics' => self::RENDERER_ROBOTICS_SIM,
        'robotics-sim' => self::RENDERER_ROBOTICS_SIM,

        // Default narrative shapes → guided-steps
        'hands_on' => self::RENDERER_GUIDED_STEPS,
        'craft' => self::RENDERER_GUIDED_STEPS,
        'routine' => self::RENDERER_GUIDED_STEPS,
        'real_world' => self::RENDERER_GUIDED_STEPS,
        'mindfulness' => self::RENDERER_GUIDED_STEPS,
        'discussion' => self::RENDERER_GUIDED_STEPS,
        'outdoor' => self::RENDERER_GUIDED_STEPS,
        'observation' => self::RENDERER_GUIDED_STEPS,
        'interactive' => self::RENDERER_GUIDED_STEPS,
        'creative' => self::RENDERER_GUIDED_STEPS,
        'creative_play' => self::RENDERER_GUIDED_STEPS,
        'worksheet' => self::RENDERER_GUIDED_STEPS,
        'experiment' => self::RENDERER_GUIDED_STEPS,
        'sensory' => self::RENDERER_GUIDED_STEPS,
        'play' => self::RENDERER_GUIDED_STEPS,
        'reading' => self::RENDERER_GUIDED_STEPS,
        'story' => self::RENDERER_GUIDED_STEPS,
        'flashcard' => self::RENDERER_GUIDED_STEPS,
        'game' => self::RENDERER_GUIDED_STEPS,
    ];

    public function resolve(Activity $activity): string
    {
        $type = (string) ($activity->activity_type ?? '');

        // 1) Direct type → renderer mapping wins.
        if ($type !== '' && isset(self::TYPE_MAP[$type])) {
            return self::TYPE_MAP[$type];
        }

        // 2) Content-shape fallbacks: video URL beats unknown type.
        return $this->resolveByContent($activity);
    }

    /**
     * Fallback when `activity_type` is unknown / null. Picks based on what
     * media the activity actually carries.
     */
    private function resolveByContent(Activity $activity): string
    {
        if (! empty($activity->video_url)) {
            return self::RENDERER_VIDEO_LESSON;
        }

        // If the activity has steps, use the guided-steps player — it
        // gracefully degrades to text-only when audio/video are missing.
        if ($activity->relationLoaded('steps')) {
            $hasSteps = $activity->steps->isNotEmpty();
        } else {
            $hasSteps = $activity->steps()->exists();
        }
        if ($hasSteps) {
            return self::RENDERER_GUIDED_STEPS;
        }

        // No type, no video, no steps — still render a guided-steps shell
        // (emoji-scene fallback). Never the empty placeholder.
        return self::RENDERER_GUIDED_STEPS;
    }

    /**
     * Test helper: is the given slug a known canonical renderer?
     */
    public static function isCanonical(string $slug): bool
    {
        return in_array($slug, self::ALL, true);
    }
}

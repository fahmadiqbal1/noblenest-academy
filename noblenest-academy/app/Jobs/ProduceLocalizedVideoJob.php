<?php

namespace App\Jobs;

use App\Helpers\I18n;
use App\Models\Activity;
use App\Models\ActivityMedia;
use App\Models\ActivityTranslation;
use App\Services\ContentSafetyService;
use App\Services\Providers\AnthropicTranslator;
use App\Services\Providers\VideoAvatarProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Phase 6 — for one Activity, run the full 8-locale media pipeline:
 *   1. Safety-check the source script.
 *   2. Translate it to the 7 non-en locales.
 *   3. Dispatch the active VideoAvatarProvider per locale.
 *   4. For each locale, queue GenerateSubtitlesJob (Whisper).
 *
 * MVP behavior: with NullAdapter (default), every step produces a
 * deterministic placeholder URL, so v1 ships with structured media
 * rows even before HeyGen/Synthesia/OpenAI keys are wired.
 */
class ProduceLocalizedVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /** @var array<int,int> */
    public array $backoff = [30, 120, 300];

    public function __construct(public readonly int $activityId)
    {
    }

    public function handle(
        ContentSafetyService $safety,
        AnthropicTranslator $translator,
        VideoAvatarProvider $avatar,
    ): void {
        $activity = Activity::find($this->activityId);
        if (! $activity) {
            Log::warning('ProduceLocalizedVideoJob: activity missing', ['id' => $this->activityId]);

            return;
        }

        $script = trim((string) ($activity->instructions ?? $activity->description ?? $activity->title ?? ''));
        if ($script === '') {
            Log::info('ProduceLocalizedVideoJob: no source text, skipping', ['id' => $activity->id]);

            return;
        }

        if ($safety->containsUnsafeContent($script, 'en')) {
            Log::warning('ProduceLocalizedVideoJob: content blocked by safety filter', [
                'id'      => $activity->id,
                'reasons' => $safety->reasons(),
            ]);

            return;
        }

        $locales = array_keys(I18n::SUPPORTED_LANGUAGES);

        foreach ($locales as $locale) {
            $localizedScript = $locale === 'en'
                ? $script
                : $translator->translate('en', $locale, $script);

            if ($locale !== 'en') {
                ActivityTranslation::updateOrCreate(
                    ['activity_id' => $activity->id, 'locale' => $locale, 'field' => 'script'],
                    ['value' => $localizedScript],
                );
            }

            try {
                $result = $avatar->generate($localizedScript, $locale);
            } catch (\Throwable $e) {
                Log::warning('ProduceLocalizedVideoJob: avatar provider failed', [
                    'id'    => $activity->id,
                    'locale' => $locale,
                    'error' => $e->getMessage(),
                ]);

                continue;
            }

            if ($result->videoUrl === null) {
                continue;
            }

            $media = ActivityMedia::updateOrCreate(
                [
                    'activity_id' => $activity->id,
                    'modality'    => "video:{$locale}",
                ],
                [
                    'media_type' => 'video',
                    'url'        => $result->videoUrl,
                    'label'      => "Video ({$locale})",
                    'is_primary' => $locale === 'en',
                ],
            );

            GenerateSubtitlesJob::dispatch($media->id, $locale);
        }
    }
}

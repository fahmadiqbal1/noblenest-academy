<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\AIJob;
use App\Services\VideoGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateActivityVideoJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 300; // 5 minutes for video generation

    public function __construct(
        private readonly int $activityId,
        private readonly string $script,
        private readonly string $language = 'en',
        private readonly ?int $aiJobId = null
    ) {}

    public function handle(VideoGenerationService $videoService): void
    {
        $activity = Activity::find($this->activityId);
        if (! $activity) {
            return;
        }

        // Generate TTS audio first
        $audioPath = $videoService->generateSpeech($this->script, $this->language);

        if ($audioPath) {
            $activity->update(['audio_url' => asset('storage/'.$audioPath)]);
        }

        // Attempt HeyGen avatar video if configured
        $avatarId = config('services.heygen.default_avatar_id', 'default_avatar');
        $voiceId = config('services.heygen.default_voice_id', 'default_voice');

        $videoId = $videoService->generateHeyGenVideo(
            $this->script,
            $avatarId,
            $voiceId,
            $this->language
        );

        if ($videoId && $this->aiJobId) {
            AIJob::where('id', $this->aiJobId)->update([
                'status' => 'video_pending',
                'payload' => array_merge(
                    AIJob::find($this->aiJobId)?->payload ?? [],
                    ['heygen_video_id' => $videoId, 'activity_id' => $this->activityId]
                ),
            ]);

            // Dispatch a polling job to check when ready
            PollHeyGenVideoJob::dispatch($videoId, $this->activityId, $this->aiJobId)
                ->delay(now()->addMinutes(3));
        }
    }
}

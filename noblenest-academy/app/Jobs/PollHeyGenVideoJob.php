<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\AIJob;
use App\Services\VideoGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Polls HeyGen until the video is complete, then stores the URL on the Activity.
 * Re-dispatches itself if still processing (up to 10 poll attempts).
 */
class PollHeyGenVideoJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;
    public int $timeout = 30;

    public function __construct(
        private readonly string $videoId,
        private readonly int $activityId,
        private readonly ?int $aiJobId = null,
        private readonly int $attempt = 1
    ) {}

    public function handle(VideoGenerationService $videoService): void
    {
        if ($this->attempt > 10) {
            $this->updateJobStatus('video_timeout');
            return;
        }

        $result = $videoService->pollHeyGenVideo($this->videoId);

        if ($result['status'] === 'completed' && !empty($result['video_url'])) {
            Activity::where('id', $this->activityId)->update(['video_url' => $result['video_url']]);
            $this->updateJobStatus('completed');
            return;
        }

        if ($result['status'] === 'failed') {
            $this->updateJobStatus('video_failed');
            return;
        }

        // Still processing — check again in 2 minutes
        static::dispatch($this->videoId, $this->activityId, $this->aiJobId, $this->attempt + 1)
            ->delay(now()->addMinutes(2));
    }

    private function updateJobStatus(string $status): void
    {
        if ($this->aiJobId) {
            AIJob::where('id', $this->aiJobId)->update(['status' => $status]);
        }
    }
}

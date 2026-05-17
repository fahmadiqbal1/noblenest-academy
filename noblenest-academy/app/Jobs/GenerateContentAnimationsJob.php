<?php

namespace App\Jobs;

use App\Models\Activity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateContentAnimationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 30;

    public function __construct(
        private readonly string $contentType,
        private readonly int $contentId
    ) {
        $this->onQueue('media-generation');
    }

    public function handle(): void
    {
        if ($this->contentType === 'activity') {
            $activity = Activity::with('steps')->find($this->contentId);
            if (! $activity) {
                Log::warning('GenerateContentAnimationsJob: Activity not found', ['id' => $this->contentId]);

                return;
            }

            foreach ($activity->steps as $step) {
                if (! $step->visual_url || ! $step->audio_url) {
                    GenerateStepMediaJob::dispatch('activity', $step->id);
                }
            }

            Log::info('GenerateContentAnimationsJob: Dispatched activity step media jobs', [
                'activity_id' => $activity->id,
                'steps' => $activity->steps->count(),
            ]);
        }
    }

    public function tags(): array
    {
        return ['animation-orchestrator', $this->contentType, 'content:'.$this->contentId];
    }
}

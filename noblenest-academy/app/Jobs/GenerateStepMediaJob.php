<?php

namespace App\Jobs;

use App\Models\ActivityStep;
use App\Services\AnimationPipelineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateStepMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 180;

    public function __construct(
        private readonly string $stepType,
        private readonly int $stepId
    ) {
        $this->onQueue('media-generation');
    }

    public function handle(AnimationPipelineService $pipeline): void
    {
        if ($this->stepType === 'activity') {
            $step = ActivityStep::find($this->stepId);
            if (!$step) {
                Log::warning('GenerateStepMediaJob: Activity step not found', ['id' => $this->stepId]);
                return;
            }
            $pipeline->processActivityStep($step);
        }
    }

    public function tags(): array
    {
        return ['animation', $this->stepType, 'step:' . $this->stepId];
    }
}

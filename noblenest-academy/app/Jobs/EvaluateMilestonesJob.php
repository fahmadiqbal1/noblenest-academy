<?php

namespace App\Jobs;

use App\Models\ChildProfile;
use App\Services\MilestoneService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EvaluateMilestonesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 30;

    public function __construct(public readonly int $childProfileId) {}

    public function handle(MilestoneService $milestoneService): void
    {
        $child = ChildProfile::find($this->childProfileId);

        if (! $child) {
            return; // Child deleted since job was dispatched — not an error
        }

        $milestoneService->evaluate($child);
    }

    public function failed(\Throwable $e): void
    {
        Log::warning('EvaluateMilestonesJob failed', [
            'child_profile_id' => $this->childProfileId,
            'error' => $e->getMessage(),
        ]);
    }
}

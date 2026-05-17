<?php

namespace App\Jobs;

use App\Models\ChildProfile;
use App\Services\LearningPathService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * RecomputeLearningPathJob
 *
 * Asynchronous job that recalculates a child's recommended learning path
 * after their ChildSkillState is updated.
 *
 * Caches the result for 24 hours so subsequent page loads are fast.
 * Uses Horizon workers for reliable background processing.
 */
class RecomputeLearningPathJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ChildProfile $child;

    public function __construct(ChildProfile $child)
    {
        $this->child = $child;

        // Use a high-priority queue for quick recomputation
        $this->queue = 'high';

        // Set retry policy: try up to 3 times over 10 minutes
        $this->tries = 3;
        $this->timeout = 30; // 30 seconds max per attempt
        $this->maxExceptions = 1;
    }

    /**
     * Execute the job: recompute the daily learning path and cache it.
     */
    public function handle(LearningPathService $learningPathService): void
    {
        try {
            Log::info('RecomputeLearningPathJob: starting', [
                'child_id' => $this->child->id,
            ]);

            // Build fresh daily path based on updated ChildSkillState
            $dailyPath = $learningPathService->buildDailyPath($this->child, 6);

            // Cache the result for 24 hours
            $cacheKey = "learning_path.{$this->child->id}.daily";
            Cache::put($cacheKey, $dailyPath, now()->addHours(24));

            Log::info('RecomputeLearningPathJob: success', [
                'child_id' => $this->child->id,
                'path_size' => $dailyPath->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('RecomputeLearningPathJob: error', [
                'child_id' => $this->child->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Fail hard after max attempts (don't retry forever)
            if ($this->attempts() >= $this->tries) {
                $this->fail($e);

                return;
            }

            // Retry with exponential backoff
            $this->release(now()->addSeconds(5 * $this->attempts()));
        }
    }

    /**
     * Called when the job fails permanently.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('RecomputeLearningPathJob: failed permanently', [
            'child_id' => $this->child->id,
            'error' => $exception->getMessage(),
        ]);

        // Could notify admin, trigger alert, etc.
    }
}

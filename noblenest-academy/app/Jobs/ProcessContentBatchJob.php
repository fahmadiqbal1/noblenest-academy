<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\AIJob;
use App\Services\AIAssistantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessContentBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public readonly int $aiJobId) {}

    public function handle(): void
    {
        $job = AIJob::findOrFail($this->aiJobId);

        if ($job->status !== 'pending') {
            return;
        }

        $job->update(['status' => 'processing', 'started_at' => now()]);

        try {
            $payload = $job->payload ?? [];
            $subject = $payload['subject'] ?? 'general';
            $ageTier = $payload['age_tier'] ?? 'Seedling';
            $count = (int) ($payload['count'] ?? 5);
            $language = $payload['language'] ?? 'en';
            $isFree = (bool) ($payload['is_free'] ?? true);

            // Delegate to AIAssistantService if available.
            // Falls back to creating placeholder activities so the pipeline stays unblocked.
            if (app()->bound(AIAssistantService::class)) {
                $service = app(AIAssistantService::class);
                $service->generateBatch($job, $subject, $ageTier, $count, $language, $isFree);
            } else {
                // Stub: create draft Activity records for manual review
                for ($i = 1; $i <= $count; $i++) {
                    Activity::create([
                        'title' => "{$subject} Activity {$i} ({$ageTier})",
                        'subject' => $subject,
                        'age_tier' => $ageTier,
                        'language' => $language,
                        'is_free' => $isFree,
                        'published' => false,
                        'source_job_id' => $job->id,
                    ]);
                }
            }

            $job->update(['status' => 'completed', 'completed_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('ProcessContentBatchJob failed', ['job_id' => $this->aiJobId, 'error' => $e->getMessage()]);
            $job->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            throw $e;
        }
    }
}

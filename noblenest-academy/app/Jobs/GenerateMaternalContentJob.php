<?php

namespace App\Jobs;

use App\Models\AIJob;
use App\Models\MaternalContent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Generates maternal wellness content via the AI pipeline.
 * Queued by admins; all AI-generated content arrives as
 * moderation_status=pending so a medical reviewer can approve it.
 */
class GenerateMaternalContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 180;

    public function __construct(public readonly int $aiJobId) 
    {
        $this->onQueue('maternal-content');
    }

    public function handle(): void
    {
        $job = AIJob::findOrFail($this->aiJobId);

        if ($job->status !== 'pending') {
            return;
        }

        $job->update(['status' => 'processing', 'started_at' => now()]);

        try {
            $payload = $job->payload ?? [];

            $contentType    = $payload['content_type']    ?? 'article';
            $stage          = $payload['stage']           ?? 'trimester_1';
            $category       = $payload['category']        ?? 'technique';
            $culturalOrigin = $payload['cultural_origin'] ?? null;
            $count          = min((int) ($payload['count'] ?? 3), 10);
            $language       = $payload['language']        ?? 'en';

            // Delegate to AIAssistantService when the sidecar is connected.
            // Otherwise create draft placeholders for manual completion.
            if (app()->bound(\App\Services\AIAssistantService::class)) {
                $service = app(\App\Services\AIAssistantService::class);

                // The sidecar method signature follows the same batch pattern
                $service->generateMaternalBatch(
                    job: $job,
                    contentType: $contentType,
                    stage: $stage,
                    category: $category,
                    culturalOrigin: $culturalOrigin,
                    count: $count,
                    language: $language,
                );
            } else {
                // Stub: draft records for manual review
                for ($i = 1; $i <= $count; $i++) {
                    MaternalContent::create([
                        'title'              => ucfirst($category) . " {$contentType} {$i} ({$stage})",
                        'slug'               => Str::slug("{$category}-{$contentType}-{$i}-{$stage}-" . Str::random(4)),
                        'description'        => "AI-generated placeholder — please complete manually.",
                        'benefit_explanation' => 'Pending — a medical reviewer must add the benefit explanation.',
                        'content_type'       => $contentType,
                        'stage'              => $stage,
                        'category'           => $category,
                        'cultural_origin'    => $culturalOrigin,
                        'language'           => $language,
                        'is_published'       => false,
                        'moderation_status'  => 'pending',
                    ]);
                }
            }

            $job->update(['status' => 'completed', 'completed_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('GenerateMaternalContentJob failed', [
                'job_id' => $this->aiJobId,
                'error'  => $e->getMessage(),
            ]);

            $job->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\AIJob;
use App\Models\AIProviderConfig;
use App\Services\AIProviderGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenerateActivityMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 180;

    public function __construct(
        public int $activityId,
        public string $mediaType,   // thumbnail | audio | video
        public ?int $providerId = null,
        public ?string $customPrompt = null,
        public ?int $aiJobId = null,
    ) {
        $this->onQueue('media-generation');
    }

    public function handle(AIProviderGateway $gateway): void
    {
        $activity = Activity::findOrFail($this->activityId);
        $provider = $this->providerId
            ? AIProviderConfig::findOrFail($this->providerId)
            : $this->resolveProviderForType();

        // Budget guard
        $budgetKey = 'ai_daily_'.$this->mediaType.'_count';
        $limit = match ($this->mediaType) {
            'thumbnail' => config('services.ai.daily_image_limit', 200),
            'audio' => config('services.ai.daily_audio_limit', 50),
            'video' => config('services.ai.daily_video_limit', 10),
            default => 100,
        };

        $used = (int) Cache::get($budgetKey, 0);
        if ($used >= $limit) {
            Log::warning("Daily {$this->mediaType} budget exhausted ({$used}/{$limit}). Re-queuing for tomorrow.");
            $this->release(3600); // retry in 1 hour

            return;
        }

        // Track the AIJob if provided
        $aiJob = $this->aiJobId ? AIJob::find($this->aiJobId) : null;
        $aiJob?->update(['status' => 'running', 'started_at' => now()]);

        try {
            $result = match ($this->mediaType) {
                'thumbnail' => $this->generateThumbnail($gateway, $provider, $activity),
                'audio' => $this->generateAudio($gateway, $provider, $activity),
                'video' => $this->generateVideo($gateway, $provider, $activity),
                default => throw new \InvalidArgumentException("Unknown media type: {$this->mediaType}"),
            };

            // Increment daily counter (expires at midnight)
            Cache::put($budgetKey, $used + 1, now()->endOfDay());

            // Update AIJob
            $aiJob?->update([
                'status' => 'completed',
                'result' => $result,
                'completed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error("GenerateActivityMedia failed: activity={$this->activityId} type={$this->mediaType} error={$e->getMessage()}");

            $aiJob?->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            // Handle rate limits with backoff
            if (str_contains($e->getMessage(), '429')) {
                $this->release(60);

                return;
            }

            throw $e;
        }
    }

    protected function generateThumbnail(AIProviderGateway $gateway, AIProviderConfig $provider, Activity $activity): array
    {
        $prompt = $this->customPrompt ?? sprintf(
            "Child-friendly, colorful Claymorphism illustration for a '%s' activity about '%s' for children age %d-%d. "
            .'Pastel colors (#2563EB blue, #F97316 orange accents, #F8FAFC background). '
            .'Playful, educational, safe for children. No text or words in the image.',
            $activity->subject ?? 'learning',
            $activity->title,
            $activity->age_min ?? 0,
            $activity->age_max ?? 10,
        );

        $result = $gateway->generateImage($provider, $prompt, [
            'aspect_ratio' => '16:9',
        ]);

        $activity->update(['thumbnail_url' => $result['url']]);

        return $result;
    }

    protected function generateAudio(AIProviderGateway $gateway, AIProviderConfig $provider, Activity $activity): array
    {
        $text = $this->customPrompt
            ?? $activity->description
            ?? $activity->title;

        $result = $gateway->generateAudio($provider, $text, [
            'voice_id' => data_get($provider->extra_config, 'voice_id'),
        ]);

        $activity->update(['audio_url' => $result['url']]);

        return $result;
    }

    protected function generateVideo(AIProviderGateway $gateway, AIProviderConfig $provider, Activity $activity): array
    {
        $prompt = $this->customPrompt ?? sprintf(
            "Short animated educational intro for children: '%s' — %s activity, age %d-%d. "
            .'Colorful, playful, child-safe, 5 seconds.',
            $activity->title,
            $activity->subject ?? 'learning',
            $activity->age_min ?? 0,
            $activity->age_max ?? 10,
        );

        $result = $gateway->generateVideo($provider, $prompt, [
            'duration' => 5,
        ]);

        $activity->update(['video_url' => $result['url']]);

        return $result;
    }

    protected function resolveProviderForType(): AIProviderConfig
    {
        $capNeeded = match ($this->mediaType) {
            'thumbnail' => 'image',
            'audio' => 'tts',
            'video' => 'video',
            default => 'image',
        };

        return AIProviderConfig::where('is_active', true)
            ->whereJsonContains('capabilities', $capNeeded)
            ->firstOrFail();
    }

    public function failed(\Throwable $e): void
    {
        if ($this->aiJobId) {
            AIJob::where('id', $this->aiJobId)->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }

        Log::error("GenerateActivityMedia permanently failed: activity={$this->activityId} type={$this->mediaType} error={$e->getMessage()}");
    }
}

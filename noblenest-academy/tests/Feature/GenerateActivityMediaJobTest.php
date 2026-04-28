<?php

namespace Tests\Feature;

use App\Jobs\GenerateActivityMediaJob;
use App\Models\Activity;
use App\Models\AIJob;
use App\Models\AIProviderConfig;
use App\Services\AIProviderGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GenerateActivityMediaJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_dispatches_to_media_generation_queue(): void
    {
        Queue::fake();

        $activity = Activity::factory()->create();
        $provider = AIProviderConfig::factory()->gemini()->create();

        GenerateActivityMediaJob::dispatch($activity->id, 'thumbnail', $provider->id);

        Queue::assertPushedOn('media-generation', GenerateActivityMediaJob::class);
    }

    public function test_thumbnail_generation_updates_activity(): void
    {
        $activity = Activity::factory()->create(['thumbnail_url' => null]);
        $provider = AIProviderConfig::factory()->gemini()->create();

        $aiJob = AIJob::factory()->queued()->create([
            'type' => 'image_generation',
            'provider' => $provider->slug,
        ]);

        $this->mock(AIProviderGateway::class, function ($mock) {
            $mock->shouldReceive('generateImage')
                ->once()
                ->andReturn([
                    'type' => 'image',
                    'url' => '/storage/ai/images/test.png',
                    'path' => 'public/ai/images/test.png',
                ]);
        });

        Cache::put('ai_daily_thumbnail_count', 0, 86400);

        $job = new GenerateActivityMediaJob(
            $activity->id,
            'thumbnail',
            $provider->id,
            null,
            $aiJob->id
        );
        $job->handle(app(AIProviderGateway::class));

        $activity->refresh();
        $this->assertEquals('/storage/ai/images/test.png', $activity->thumbnail_url);
    }

    public function test_audio_generation_updates_activity(): void
    {
        $activity = Activity::factory()->create(['audio_url' => null]);
        $provider = AIProviderConfig::factory()->elevenlabs()->create();

        $this->mock(AIProviderGateway::class, function ($mock) {
            $mock->shouldReceive('generateAudio')
                ->once()
                ->andReturn([
                    'type' => 'audio',
                    'url' => '/storage/ai/audio/test.mp3',
                ]);
        });

        Cache::put('ai_daily_audio_count', 0, 86400);

        $job = new GenerateActivityMediaJob($activity->id, 'audio', $provider->id);
        $job->handle(app(AIProviderGateway::class));

        $activity->refresh();
        $this->assertEquals('/storage/ai/audio/test.mp3', $activity->audio_url);
    }

    public function test_budget_guard_prevents_over_limit(): void
    {
        $activity = Activity::factory()->create();
        $provider = AIProviderConfig::factory()->gemini()->create();

        Cache::put('ai_daily_thumbnail_count', 9999, 86400);
        config(['services.ai.daily_image_limit' => 10]);

        $gateway = $this->mock(AIProviderGateway::class);
        $gateway->shouldNotReceive('generateImage');

        $job = new GenerateActivityMediaJob($activity->id, 'thumbnail', $provider->id);

        // Budget exceeded → job calls $this->release() which throws outside queue worker
        // The gateway should never be called because the budget check happens first
        try {
            $job->handle(app(AIProviderGateway::class));
        } catch (\Throwable) {
            // release() throws when not running in a queue worker - expected
        }

        $activity->refresh();
        $this->assertNull($activity->thumbnail_url);
    }

    public function test_failed_job_updates_ai_job_status(): void
    {
        $activity = Activity::factory()->create();
        $provider = AIProviderConfig::factory()->gemini()->create();

        $aiJob = AIJob::factory()->queued()->create([
            'type' => 'image_generation',
            'provider' => $provider->slug,
        ]);

        $this->mock(AIProviderGateway::class, function ($mock) {
            $mock->shouldReceive('generateImage')
                ->once()
                ->andThrow(new \RuntimeException('API unavailable'));
        });

        Cache::put('ai_daily_thumbnail_count', 0, 86400);

        $job = new GenerateActivityMediaJob(
            $activity->id,
            'thumbnail',
            $provider->id,
            null,
            $aiJob->id
        );

        try {
            $job->handle(app(AIProviderGateway::class));
        } catch (\RuntimeException) {
            // Expected
        }

        $exception = new \RuntimeException('API unavailable');
        $job->failed($exception);

        $aiJob->refresh();
        $this->assertEquals('failed', $aiJob->status);
    }
}

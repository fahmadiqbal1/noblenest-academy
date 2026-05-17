<?php

namespace Tests\Feature\AI;

use App\Helpers\I18n;
use App\Jobs\ProduceLocalizedVideoJob;
use App\Models\Activity;
use App\Models\ActivityMedia;
use App\Models\ActivityTranslation;
use App\Models\AuditLogEntry;
use App\Services\ContentSafetyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProduceLocalizedVideoJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_pipeline_creates_video_media_per_locale_with_null_adapter(): void
    {
        Queue::fake(); // capture GenerateSubtitlesJob dispatches inside the pipeline

        $activity = Activity::factory()->create([
            'instructions'     => 'Read the letter A out loud and trace it three times.',
            'cognitive_domain' => 'language',
        ]);

        // Run the producer job inline (constructor-injected deps resolved by container).
        (new ProduceLocalizedVideoJob($activity->id))->handle(
            app(ContentSafetyService::class),
            app(\App\Services\Providers\AnthropicTranslator::class),
            app(\App\Services\Providers\VideoAvatarProvider::class),
        );

        $locales = array_keys(I18n::SUPPORTED_LANGUAGES);

        // 8 video rows (one per locale).
        $videoRows = ActivityMedia::where('activity_id', $activity->id)
            ->where('media_type', 'video')
            ->get();
        $this->assertCount(count($locales), $videoRows);
        foreach ($videoRows as $row) {
            $this->assertNotEmpty($row->url);
            $this->assertStringContainsString('video:', $row->modality);
        }

        // 7 translation rows (en stays in Activity, others land in ActivityTranslation).
        $this->assertEquals(
            count($locales) - 1,
            ActivityTranslation::where('activity_id', $activity->id)->where('field', 'script')->count()
        );

        // One GenerateSubtitlesJob dispatched per locale.
        Queue::assertPushed(\App\Jobs\GenerateSubtitlesJob::class, count($locales));
    }

    public function test_unsafe_script_is_blocked_and_logged(): void
    {
        $activity = Activity::factory()->create([
            'instructions' => 'Pick up the knife and stab the apple.', // hits 'knife' + 'stab'
        ]);

        $beforeAudit = AuditLogEntry::count();

        (new ProduceLocalizedVideoJob($activity->id))->handle(
            app(ContentSafetyService::class),
            app(\App\Services\Providers\AnthropicTranslator::class),
            app(\App\Services\Providers\VideoAvatarProvider::class),
        );

        $this->assertSame(0, ActivityMedia::where('activity_id', $activity->id)->count());
        $this->assertGreaterThan($beforeAudit, AuditLogEntry::where('action', 'content_safety_block')->count());
    }

    public function test_provider_binding_defaults_to_null_adapter(): void
    {
        $provider = app(\App\Services\Providers\VideoAvatarProvider::class);
        $this->assertInstanceOf(\App\Services\Providers\VideoAvatar\NullAdapter::class, $provider);

        $whisper = app(\App\Services\Providers\WhisperAdapter::class);
        $this->assertInstanceOf(\App\Services\Providers\Whisper\LocalWhisperAdapter::class, $whisper);
    }
}

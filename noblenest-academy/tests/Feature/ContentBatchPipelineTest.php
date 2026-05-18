<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\AIJob;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regression guard for C7: activities.source_job_id never existed and was
 * not in Activity::$fillable, so AI-generated activities lost their job
 * link on create and ContentBatchController::preview()/publish() 500'd on
 * `where('source_job_id', ...)`. The whole admin content-batch pipeline
 * was dead.
 */
class ContentBatchPipelineTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function source_job_id_persists_and_links_activity_to_job(): void
    {
        $job = AIJob::create([
            'type' => 'content_batch',
            'status' => 'completed',
            'payload' => ['count' => 1],
        ]);

        $a = Activity::factory()->create(['source_job_id' => $job->id, 'published' => false]);

        $this->assertSame($job->id, $a->fresh()->source_job_id,
            'source_job_id must persist (was silently dropped — not fillable + no column)');
    }

    #[Test]
    public function admin_can_preview_and_publish_a_batch(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $job = AIJob::create([
            'type' => 'content_batch',
            'status' => 'completed',
            'payload' => ['count' => 2],
        ]);
        $a1 = Activity::factory()->create(['source_job_id' => $job->id, 'published' => false]);
        $a2 = Activity::factory()->create(['source_job_id' => $job->id, 'published' => false]);

        $this->actingAs($admin)
            ->get("/admin/content-batch/{$job->id}/preview")
            ->assertOk()
            ->assertSee($a1->title);

        $this->actingAs($admin)
            ->post("/admin/content-batch/{$job->id}/publish", ['ids' => [$a1->id, $a2->id]])
            ->assertRedirect();

        $this->assertTrue($a1->fresh()->published);
        $this->assertTrue($a2->fresh()->published);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\ExportParentDataJob;
use App\Jobs\HardDeleteParentDataJob;
use App\Models\AuditLogEntry;
use App\Models\ChildProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PrivacyGdprTest extends TestCase
{
    use RefreshDatabase;

    private User $parent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parent = User::factory()->create([
            'role'            => 'Parent',
            'password'        => Hash::make('secret123'),
            'parent_pin_hash' => Hash::make('1234'),
        ]);
        // Pre-verify PIN so we can hit PIN-gated endpoints.
        session(['parent_pin_verified_at' => now()->toIso8601String()]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function export_request_dispatches_job_and_writes_audit_log(): void
    {
        Bus::fake();

        $this->withSession(['parent_pin_verified_at' => now()->toIso8601String()])
            ->actingAs($this->parent)
            ->get('/privacy/export')
            ->assertRedirect(route('privacy.dashboard'));

        Bus::assertDispatched(ExportParentDataJob::class, fn ($job) => $job->userId === $this->parent->id);
        $this->assertDatabaseHas('audit_log_entries', [
            'actor_user_id' => $this->parent->id,
            'action'        => 'privacy.export.requested',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function export_download_requires_signed_url(): void
    {
        // Without signing → 403.
        $this->actingAs($this->parent)
            ->get("/privacy/export/{$this->parent->id}/20260517120000")
            ->assertStatus(403);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function delete_soft_deletes_and_schedules_hard_delete(): void
    {
        Bus::fake();

        ChildProfile::create([
            'parent_id'           => $this->parent->id,
            'name'                => 'K',
            'date_of_birth'       => now()->subYears(3),
            'preferred_language'  => 'en',
            'parental_consent_at' => now(),
        ]);

        $this->withSession(['parent_pin_verified_at' => now()->toIso8601String()])
            ->actingAs($this->parent)
            ->delete('/privacy/delete', [
                'password' => 'secret123',
                'confirm'  => 'DELETE',
            ])
            ->assertRedirect('/');

        $this->assertSoftDeleted('users', ['id' => $this->parent->id]);
        $this->assertDatabaseHas('audit_log_entries', [
            'actor_user_id' => $this->parent->id,
            'action'        => 'privacy.erase.requested',
        ]);
        Bus::assertDispatched(HardDeleteParentDataJob::class);
    }
}

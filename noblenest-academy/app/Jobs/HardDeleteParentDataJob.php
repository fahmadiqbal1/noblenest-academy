<?php

namespace App\Jobs;

use App\Models\AuditLogEntry;
use App\Models\ChildActivityProgress;
use App\Models\ChildProfile;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Phase 5 — final hard delete 30 days after the parent invokes GDPR erase.
 *
 * If the user has been restored (deleted_at cleared) we abort and just log.
 */
class HardDeleteParentDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $userId) {}

    public function handle(): void
    {
        $user = User::withTrashed()->find($this->userId);
        if (! $user) {
            return;
        }

        if ($user->deleted_at === null) {
            AuditLogEntry::record(
                actorUserId: null,
                action: 'privacy.erase.aborted',
                targetType: User::class,
                targetId: $user->id,
                meta: ['reason' => 'user_restored_before_window'],
            );

            return;
        }

        $childIds = ChildProfile::withTrashed()
            ->where('parent_id', $user->id)
            ->pluck('id')
            ->all();

        ChildActivityProgress::withTrashed()
            ->whereIn('child_profile_id', $childIds)
            ->forceDelete();
        ChildProfile::withTrashed()
            ->where('parent_id', $user->id)
            ->forceDelete();
        $user->forceDelete();

        AuditLogEntry::record(
            actorUserId: null,
            action: 'privacy.erase.completed',
            targetType: User::class,
            targetId: $this->userId,
            meta: ['child_ids' => $childIds],
        );
    }
}

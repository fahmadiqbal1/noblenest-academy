<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Append-only privacy / admin audit trail (Phase 5).
 *
 * Created via static helper `AuditLogEntry::record(...)`. Never updated.
 */
class AuditLogEntry extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'actor_user_id',
        'action',
        'target_type',
        'target_id',
        'ip',
        'user_agent',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Convenience writer — call from controllers/jobs.
     */
    public static function record(
        ?int $actorUserId,
        string $action,
        ?string $targetType = null,
        ?int $targetId = null,
        ?string $ip = null,
        ?string $userAgent = null,
        ?array $meta = null,
    ): self {
        return self::create([
            'actor_user_id' => $actorUserId,
            'action'        => $action,
            'target_type'   => $targetType,
            'target_id'     => $targetId,
            'ip'            => $ip ? substr($ip, 0, 45) : null,
            'user_agent'    => $userAgent ? substr($userAgent, 0, 512) : null,
            'meta'          => $meta,
            'created_at'    => now(),
        ]);
    }
}

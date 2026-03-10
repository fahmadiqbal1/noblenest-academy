<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ChildActivityProgress Model
 * 
 * Tracks a child's progress through activities.
 * Separated from user-level progress for COPPA compliance.
 */
class ChildActivityProgress extends Model
{
    use HasFactory;

    protected $table = 'child_activity_progress';

    protected $fillable = [
        'child_profile_id',
        'activity_id',
        'status',
        'score',
        'time_spent',
        'attempts',
        'trace_data',
        'drawing_path',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'score'        => 'integer',
        'time_spent'   => 'integer',
        'attempts'     => 'integer',
        'trace_data'   => 'array',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * The child profile this progress belongs to.
     */
    public function childProfile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ChildProfile::class);
    }

    /**
     * The activity this progress is for.
     */
    public function activity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    /**
     * Mark the activity as started.
     */
    public function markStarted(): self
    {
        $this->update([
            'status'     => 'in_progress',
            'started_at' => now(),
            'attempts'   => $this->attempts + 1,
        ]);
        return $this;
    }

    /**
     * Mark the activity as completed with optional score.
     */
    public function markCompleted(?int $score = null, ?int $timeSpent = null): self
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'score'        => $score,
            'time_spent'   => $timeSpent ?? $this->calculateTimeSpent(),
        ]);
        return $this;
    }

    /**
     * Calculate time spent based on started_at.
     */
    protected function calculateTimeSpent(): ?int
    {
        if (!$this->started_at) {
            return null;
        }
        return (int) $this->started_at->diffInSeconds(now());
    }

    /**
     * Scope for completed activities.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for in-progress activities.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for a specific child.
     */
    public function scopeForChild($query, int $childProfileId)
    {
        return $query->where('child_profile_id', $childProfileId);
    }
}

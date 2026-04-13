<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaternalProgress extends Model
{
    protected $table = 'maternal_progress';

    protected $fillable = [
        'maternal_profile_id',
        'maternal_content_id',
        'status',
        'completed_at',
        'time_spent',
        'rating',
        'notes',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'time_spent'   => 'integer',
        'rating'       => 'integer',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function profile(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MaternalProfile::class, 'maternal_profile_id');
    }

    public function content(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MaternalContent::class, 'maternal_content_id');
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeForProfile($query, int $profileId)
    {
        return $query->where('maternal_profile_id', $profileId);
    }

    // ------------------------------------------------------------------
    // Lifecycle methods
    // ------------------------------------------------------------------

    public function markStarted(): void
    {
        $this->update([
            'status' => 'in_progress',
        ]);
    }

    public function markCompleted(?int $rating = null): void
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'rating'       => $rating,
        ]);
    }
}

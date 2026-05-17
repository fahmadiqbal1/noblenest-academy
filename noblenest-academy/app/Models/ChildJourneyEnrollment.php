<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildJourneyEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_profile_id', 'journey_id', 'current_week',
        'started_at', 'completed_at', 'is_active',
    ];

    protected $casts = [
        'current_week' => 'integer',
        'is_active'    => 'boolean',
        'started_at'   => 'date',
        'completed_at' => 'date',
    ];

    public function childProfile(): BelongsTo
    {
        return $this->belongsTo(ChildProfile::class, 'child_profile_id');
    }

    public function journey(): BelongsTo
    {
        return $this->belongsTo(ThematicJourney::class, 'journey_id');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}

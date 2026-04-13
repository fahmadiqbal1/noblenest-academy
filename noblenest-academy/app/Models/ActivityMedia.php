<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityMedia extends Model
{
    protected $table = 'activity_media';

    protected $fillable = [
        'activity_id',
        'media_type',
        'url',
        'label',
        'modality',
        'order',
        'is_primary',
        'duration_seconds',
    ];

    protected $casts = [
        'order'            => 'integer',
        'is_primary'       => 'boolean',
        'duration_seconds' => 'integer',
    ];

    public function activity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeOfType($query, string $type)
    {
        return $query->where('media_type', $type);
    }

    public function scopeForModality($query, string $modality)
    {
        return $query->where('modality', $modality);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}

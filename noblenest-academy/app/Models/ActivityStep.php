<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'step_number',
        'title',
        'instruction',
        'visual_url',
        'video_url',
        'audio_url',
        'duration_seconds',
        'benefit_note',
    ];

    protected $casts = [
        'step_number'      => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function activity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}

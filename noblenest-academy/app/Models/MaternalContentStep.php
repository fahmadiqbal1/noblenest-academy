<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaternalContentStep extends Model
{
    protected $fillable = [
        'maternal_content_id',
        'step_number',
        'title',
        'instruction',
        'visual_url',
        'video_url',
        'audio_url',
        'duration_seconds',
        'tip',
    ];

    protected $casts = [
        'step_number'      => 'integer',
        'duration_seconds' => 'integer',
    ];

    public function content(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MaternalContent::class, 'maternal_content_id');
    }
}

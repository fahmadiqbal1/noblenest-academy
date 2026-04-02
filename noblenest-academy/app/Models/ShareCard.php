<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShareCard extends Model
{
    protected $fillable = [
        'child_profile_id',
        'activity_id',
        'badge_id',
        'card_type',
        'image_url',
        'share_count',
    ];

    protected $casts = [
        'share_count' => 'integer',
    ];

    public function childProfile(): BelongsTo
    {
        return $this->belongsTo(ChildProfile::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function badge(): BelongsTo
    {
        return $this->belongsTo(Badge::class);
    }
}

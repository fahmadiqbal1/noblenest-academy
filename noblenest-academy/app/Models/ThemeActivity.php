<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThemeActivity extends Model
{
    protected $fillable = [
        'weekly_theme_id', 'activity_id', 'subject_slot',
        'day_of_week', 'time_of_day', 'sort_order',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'sort_order'  => 'integer',
    ];

    public function weeklyTheme(): BelongsTo
    {
        return $this->belongsTo(WeeklyTheme::class, 'weekly_theme_id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}

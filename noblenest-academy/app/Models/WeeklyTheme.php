<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklyTheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'journey_id', 'week_number', 'theme_name',
        'theme_description', 'theme_emoji', 'big_idea',
    ];

    protected $casts = [
        'week_number' => 'integer',
    ];

    public function journey(): BelongsTo
    {
        return $this->belongsTo(ThematicJourney::class, 'journey_id');
    }

    public function themeActivities(): HasMany
    {
        return $this->hasMany(ThemeActivity::class, 'weekly_theme_id')
            ->orderBy('day_of_week')
            ->orderBy('time_of_day')
            ->orderBy('sort_order');
    }
}

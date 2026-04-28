<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaternalMealPlan extends Model
{
    protected $fillable = [
        'stage',
        'week_number',
        'day_of_week',
        'breakfast',
        'morning_snack',
        'lunch',
        'afternoon_snack',
        'dinner',
        'hydration_notes',
        'herb_tea_recommendation',
        'key_nutrients',
        'benefit_explanation',
        'language',
    ];

    protected $casts = [
        'week_number'      => 'integer',
        'breakfast'        => 'array',
        'morning_snack'    => 'array',
        'lunch'            => 'array',
        'afternoon_snack'  => 'array',
        'dinner'           => 'array',
        'key_nutrients'    => 'array',
    ];

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeForStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeForWeek($query, int $week)
    {
        return $query->where('week_number', $week);
    }

    public function scopeInLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }
}

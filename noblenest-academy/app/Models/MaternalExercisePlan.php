<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaternalExercisePlan extends Model
{
    protected $fillable = [
        'stage',
        'week_number',
        'day_of_week',
        'routine_name',
        'exercises',
        'total_duration_minutes',
        'intensity',
        'warmup_instructions',
        'cooldown_instructions',
        'benefit_explanation',
        'cultural_origin',
        'safety_notes',
        'contraindications',
        'language',
    ];

    protected $casts = [
        'week_number'            => 'integer',
        'exercises'              => 'array',
        'total_duration_minutes' => 'integer',
        'contraindications'      => 'array',
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

    public function scopeByIntensity($query, string $intensity)
    {
        return $query->where('intensity', $intensity);
    }

    public function scopeByCulture($query, string $culture)
    {
        return $query->where('cultural_origin', $culture);
    }
}

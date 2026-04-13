<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaternalEmergencySign extends Model
{
    protected $fillable = [
        'stage',
        'symptom',
        'severity',
        'action_text',
        'order',
        'language',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function scopeForStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeInLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeEmergency($query)
    {
        return $query->where('severity', 'emergency');
    }

    public function scopeWarning($query)
    {
        return $query->where('severity', 'warning');
    }
}

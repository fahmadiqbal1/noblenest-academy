<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon_url',
        'badge_type',
        'required_value',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'required_value' => 'integer',
    ];

    public function childProfiles(): BelongsToMany
    {
        return $this->belongsToMany(ChildProfile::class, 'child_badges')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Milestone extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'description',
        'age_months_min',
        'age_months_max',
        'domain',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'age_months_min' => 'integer',
        'age_months_max' => 'integer',
        'sort_order'     => 'integer',
    ];

    public function childProfiles(): BelongsToMany
    {
        return $this->belongsToMany(ChildProfile::class, 'child_milestone_progress')
            ->withPivot(['status', 'achieved_at', 'parent_note'])
            ->withTimestamps();
    }
}

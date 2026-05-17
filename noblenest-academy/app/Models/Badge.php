<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'emoji',
        'description',
        'icon_url',
        'badge_type',
        'criteria',
        'required_value',
        'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'required_value' => 'integer',
        'criteria'       => 'array',
    ];

    public function childProfiles(): BelongsToMany
    {
        return $this->belongsToMany(ChildProfile::class, 'child_badges')
            ->withPivot('awarded_at')
            ->withTimestamps();
    }
}

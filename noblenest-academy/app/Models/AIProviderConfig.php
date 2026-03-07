<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIProviderConfig extends Model
{
    use HasFactory;

    protected $table = 'ai_provider_configs';

    protected $fillable = [
        'name',
        'slug',
        'api_base_url',
        'api_key_encrypted',
        'model',
        'is_active',
        'capabilities',
        'extra_config',
    ];

    protected $hidden = [
        'api_key_encrypted',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'capabilities' => 'array',
        'extra_config' => 'array',
    ];

    public function jobs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AIJob::class, 'provider', 'slug');
    }
}

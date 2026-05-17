<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'connection_status',
        'connection_message',
        'last_checked_at',
        'last_live_at',
        'capabilities',
        'extra_config',
    ];

    protected $hidden = [
        'api_key_encrypted',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capabilities' => 'array',
        'extra_config' => 'array',
        'last_checked_at' => 'datetime',
        'last_live_at' => 'datetime',
    ];

    public function jobs(): HasMany
    {
        return $this->hasMany(AIJob::class, 'provider', 'slug');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'description',
        'order',
        'content',
        'video_url',
        'duration',
        'language',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order'        => 'integer',
        'duration'     => 'integer',
    ];

    public function module(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function activities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_lesson')
                    ->withPivot('order')
                    ->orderByPivot('order');
    }
}

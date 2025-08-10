<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'age_min',
        'age_max',
        'skill',
        'duration',
        'language',
        'activity_type',
        'media_url',
        'is_rtl',
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'activity_module');
    }
}

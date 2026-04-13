<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'age_min',
        'age_max',
        'color',
        'emoji',
    ];

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    /**
     * All activities linked to this course's modules (many-to-many through pivot).
     */
    public function activities()
    {
        return Activity::whereHas('modules', function ($q) {
            $q->where('modules.course_id', $this->id);
        });
    }
}

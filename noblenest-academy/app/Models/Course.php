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
    ];

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    // Add activities relationship through modules
    public function activities()
    {
        return $this->hasManyThrough(Activity::class, Module::class, 'course_id', 'id', 'id', 'id');
    }
}

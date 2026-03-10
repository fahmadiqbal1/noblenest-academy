<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Module::class);
    }

    /**
     * Get all activities associated with this course through modules.
     * Since Module <-> Activity is many-to-many, we cannot use hasManyThrough.
     * This returns a Collection of Activity models.
     */
    public function getActivitiesAttribute(): Collection
    {
        return Activity::whereHas('modules', function ($query) {
            $query->where('course_id', $this->id);
        })->get();
    }

    /**
     * Eager-loadable relationship alternative using a subquery.
     * Use: Course::with('allActivities')->get()
     */
    public function allActivities()
    {
        return $this->hasManyThrough(
            Activity::class,
            Module::class,
            'course_id',      // Foreign key on modules table
            'id',             // Foreign key on activities table
            'id',             // Local key on courses table
            'id'              // Local key on modules table
        )->join('activity_module', 'activities.id', '=', 'activity_module.activity_id')
         ->where('activity_module.module_id', '=', DB::raw('modules.id'))
         ->select('activities.*')
         ->distinct();
    }
}

<?php
// Module model for Course > Module > Lesson > Activity hierarchy
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Lessons within this module (Course > Module > Lesson hierarchy).
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    /**
     * Activities directly associated with this module (many-to-many).
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'activity_module')
                    ->withTimestamps();
    }

    /**
     * Get all activities through lessons.
     */
    public function getActivitiesThroughLessonsAttribute()
    {
        return Activity::whereHas('lessons', function ($query) {
            $query->where('module_id', $this->id);
        })->get();
    }
}


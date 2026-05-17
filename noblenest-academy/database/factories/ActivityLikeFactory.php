<?php
namespace Database\Factories;
use App\Models\ActivityLike;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityLike> */
class ActivityLikeFactory extends Factory
{
    protected $model = ActivityLike::class;
    public function definition(): array
    {
        return [
            'activity_id' => Activity::factory(),
            'user_id'     => User::factory(),
        ];
    }
}

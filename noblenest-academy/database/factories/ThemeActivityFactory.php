<?php
namespace Database\Factories;
use App\Models\ThemeActivity;
use App\Models\WeeklyTheme;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ThemeActivity> */
class ThemeActivityFactory extends Factory
{
    protected $model = ThemeActivity::class;
    public function definition(): array
    {
        return [
            'weekly_theme_id' => WeeklyTheme::factory(),
            'activity_id'     => Activity::factory(),
            'subject_slot'    => $this->faker->randomElement(['Math', 'Science', 'Art', 'Language']),
            'day_of_week'     => $this->faker->numberBetween(1, 5),
            'time_of_day'     => $this->faker->randomElement(['morning', 'afternoon']),
            'sort_order'      => $this->faker->numberBetween(0, 10),
        ];
    }
}

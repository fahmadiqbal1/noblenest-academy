<?php
namespace Database\Factories;
use App\Models\WeeklyTheme;
use App\Models\ThematicJourney;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklyTheme> */
class WeeklyThemeFactory extends Factory
{
    protected $model = WeeklyTheme::class;
    public function definition(): array
    {
        return [
            'journey_id'        => ThematicJourney::factory(),
            'week_number'       => $this->faker->numberBetween(1, 12),
            'theme_name'        => $this->faker->words(2, true),
            'theme_description' => $this->faker->sentence(),
            'theme_emoji'       => '📅',
            'big_idea'          => $this->faker->sentence(),
        ];
    }
}

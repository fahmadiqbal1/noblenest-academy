<?php
namespace Database\Factories;
use App\Models\ThematicJourney;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ThematicJourney> */
class ThematicJourneyFactory extends Factory
{
    protected $model = ThematicJourney::class;
    public function definition(): array
    {
        return [
            'title'        => $this->faker->words(2, true),
            'description'  => $this->faker->sentence(),
            'age_tier'     => $this->faker->randomElement(['baby', 'toddler', 'preschool', 'school']),
            'emoji'        => '🌟',
            'cover_color'  => '#6C63FF',
            'total_weeks'  => $this->faker->numberBetween(1, 12),
            'is_published' => true,
            'sort_order'   => $this->faker->numberBetween(0, 100),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['is_published' => true]);
    }
}

<?php
namespace Database\Factories;
use App\Models\Milestone;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Milestone> */
class MilestoneFactory extends Factory
{
    protected $model = Milestone::class;
    public function definition(): array
    {
        $min = $this->faker->numberBetween(0, 60);
        return [
            'slug'           => $this->faker->unique()->slug(3),
            'title'          => $this->faker->sentence(4),
            'description'    => $this->faker->sentence(),
            'age_months_min' => $min,
            'age_months_max' => $min + $this->faker->numberBetween(1, 60),
            'domain'         => $this->faker->randomElement(['cognitive', 'motor', 'language', 'social', 'creative', 'literacy', 'numeracy']),
            'sort_order'     => $this->faker->numberBetween(0, 100),
            'is_active'      => true,
        ];
    }
}

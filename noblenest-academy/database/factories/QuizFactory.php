<?php
namespace Database\Factories;
use App\Models\Quiz;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz> */
class QuizFactory extends Factory
{
    protected $model = Quiz::class;
    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'module_id'   => Module::factory(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Module;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Quiz> */
class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'module_id' => Module::factory(),
        ];
    }
}

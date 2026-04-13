<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        return [
            'title'       => $title,
            'slug'        => Str::slug($title),
            'description' => $this->faker->paragraph(),
            'age_min'     => $this->faker->numberBetween(0, 5),
            'age_max'     => $this->faker->numberBetween(6, 12),
            'color'       => $this->faker->hexColor(),
            'emoji'       => '📚',
        ];
    }
}

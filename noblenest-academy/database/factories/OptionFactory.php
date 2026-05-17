<?php
namespace Database\Factories;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Option> */
class OptionFactory extends Factory
{
    protected $model = Option::class;
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'option_text' => $this->faker->words(3, true),
            'is_correct'  => $this->faker->boolean(25),
        ];
    }
}

<?php
namespace Database\Factories;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson> */
class LessonFactory extends Factory
{
    protected $model = Lesson::class;
    public function definition(): array
    {
        return [
            'module_id'    => Module::factory(),
            'title'        => $this->faker->sentence(4),
            'description'  => $this->faker->paragraph(),
            'order'        => $this->faker->numberBetween(0, 20),
            'content'      => $this->faker->paragraphs(3, true),
            'video_url'    => $this->faker->optional()->url(),
            'duration'     => $this->faker->numberBetween(5, 60),
            'language'     => 'en',
            'is_published' => true,
        ];
    }
}

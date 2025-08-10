<?php

namespace Database\Factories;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        $age_min = $this->faker->numberBetween(0, 10);
        $age_max = $this->faker->numberBetween($age_min, 12);
        $skills = [
            'sensory', 'motor', 'language', 'cognitive', 'social', 'emotional', 'creative', 'etiquette', 'literacy', 'math', 'problem_solving', 'science', 'art', 'coding', 'robotics', 'mindfulness', 'physical', 'chivalry', 'moral', 'cultural'
        ];
        $languages = ['en', 'fr', 'ru', 'zh', 'es', 'ko', 'ur', 'ar'];
        $activity_types = ['video', 'tracing', 'quiz', 'story', 'drawing', 'music', 'puzzle', 'collaborative', 'outdoor', 'roleplay', 'experiment'];
        $media = [
            'https://example.com/video.mp4',
            'https://example.com/tracing.png',
            'https://example.com/quiz.json',
            'https://example.com/story.mp3',
            'https://example.com/art.jpg',
        ];
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(12),
            'age_min' => $age_min,
            'age_max' => $age_max,
            'skill' => $this->faker->randomElement($skills),
            'duration' => $this->faker->numberBetween(5, 30),
            'language' => $this->faker->randomElement($languages),
            'activity_type' => $this->faker->randomElement($activity_types),
            'media_url' => $this->faker->randomElement($media),
            'is_rtl' => function (array $attributes) {
                return in_array($attributes['language'], ['ur', 'ar']);
            },
        ];
    }
}

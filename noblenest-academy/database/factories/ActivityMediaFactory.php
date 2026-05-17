<?php
namespace Database\Factories;
use App\Models\ActivityMedia;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityMedia> */
class ActivityMediaFactory extends Factory
{
    protected $model = ActivityMedia::class;
    public function definition(): array
    {
        return [
            'activity_id'      => Activity::factory(),
            'media_type'       => $this->faker->randomElement(['video', 'image', 'audio']),
            'url'              => $this->faker->url(),
            'label'            => $this->faker->optional()->words(3, true),
            'modality'         => $this->faker->randomElement(['visual', 'audio', 'kinesthetic']),
            'order'            => $this->faker->numberBetween(0, 10),
            'is_primary'       => false,
            'duration_seconds' => $this->faker->numberBetween(10, 600),
        ];
    }
}

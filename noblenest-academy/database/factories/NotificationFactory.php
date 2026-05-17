<?php
namespace Database\Factories;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification> */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;
    public function definition(): array
    {
        return [
            'user_id'    => User::factory(),
            'type'       => $this->faker->randomElement(['badge_earned', 'milestone_achieved', 'new_activity']),
            'title'      => $this->faker->sentence(3),
            'body'       => $this->faker->sentence(),
            'data'       => [],
            'icon'       => '🔔',
            'action_url' => $this->faker->optional()->url(),
            'is_read'    => false,
            'read_at'    => null,
        ];
    }
}

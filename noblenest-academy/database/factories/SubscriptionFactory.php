<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'plan'        => 'premium',
            'provider'    => 'stripe',
            'provider_id' => 'sub_' . $this->faker->lexify('??????????????????????????'),
            'amount'      => 12.99,
            'currency'    => 'USD',
            'active'      => true,
            'starts_at'   => now(),
            'ends_at'     => now()->addMonth(),
        ];
    }
}

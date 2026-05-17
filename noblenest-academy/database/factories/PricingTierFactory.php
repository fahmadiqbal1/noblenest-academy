<?php

namespace Database\Factories;

use App\Models\PricingTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PricingTier> */
class PricingTierFactory extends Factory
{
    protected $model = PricingTier::class;

    public function definition(): array
    {
        return [
            'region_code' => $this->faker->unique()->randomElement(['GLOBAL_SOUTH', 'SOUTH_ASIA', 'SEA', 'MENA', 'EUROPE', 'US']).'_'.$this->faker->unique()->numberBetween(1, 9999),
            'region_label' => $this->faker->country(),
            'country_codes' => ['PK', 'BD', 'NG'],
            'price_monthly' => $this->faker->randomFloat(2, 1, 30),
            'price_yearly' => $this->faker->randomFloat(2, 10, 300),
            'currency_code' => 'USD',
            'stripe_price_id_monthly' => $this->faker->optional()->bothify('price_########'),
            'stripe_price_id_yearly' => $this->faker->optional()->bothify('price_########'),
            'is_active' => true,
        ];
    }
}

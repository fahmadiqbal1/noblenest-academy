<?php
namespace Database\Factories;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment> */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;
    public function definition(): array
    {
        return [
            'user_id'             => User::factory(),
            'provider'            => $this->faker->randomElement(['stripe', 'paypal']),
            'provider_payment_id' => $this->faker->uuid(),
            'amount'              => $this->faker->randomFloat(2, 1, 100),
            'currency'            => 'USD',
            'status'              => $this->faker->randomElement(['pending', 'succeeded', 'failed']),
            'paid_at'             => now(),
        ];
    }
}

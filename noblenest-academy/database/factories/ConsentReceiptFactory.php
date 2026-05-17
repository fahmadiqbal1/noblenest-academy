<?php

namespace Database\Factories;

use App\Models\ChildProfile;
use App\Models\ConsentReceipt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConsentReceipt>
 */
class ConsentReceiptFactory extends Factory
{
    protected $model = ConsentReceipt::class;

    public function definition(): array
    {
        return [
            'parent_user_id' => User::factory(),
            'child_profile_id' => ChildProfile::factory(),
            'document_version' => '2026-05-16',
            'ip' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'signed_at' => now(),
            'withdrawn_at' => null,
        ];
    }

    public function withdrawn(): self
    {
        return $this->state(fn () => ['withdrawn_at' => now()]);
    }
}

<?php

namespace Database\Factories;

use App\Models\AssessmentResponse;
use App\Models\ChildProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AssessmentResponse> */
class AssessmentResponseFactory extends Factory
{
    protected $model = AssessmentResponse::class;

    public function definition(): array
    {
        return [
            'child_id' => ChildProfile::factory(),
            'user_id' => User::factory(),
            'battery' => 'discovery',
            'answers' => [['sequence' => 1, 'option' => 2]],
            'scores' => ['cognitive_logic' => 12, 'creative' => 8],
            'completed_at' => now(),
        ];
    }
}

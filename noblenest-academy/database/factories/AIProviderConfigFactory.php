<?php

namespace Database\Factories;

use App\Models\AIProviderConfig;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;

class AIProviderConfigFactory extends Factory
{
    protected $model = AIProviderConfig::class;

    public function definition(): array
    {
        return [
            'name'              => $this->faker->company() . ' AI',
            'slug'              => $this->faker->unique()->slug(2),
            'api_base_url'      => null,
            'api_key_encrypted' => Crypt::encryptString('test-key-' . $this->faker->uuid()),
            'model'             => 'gpt-4o-mini',
            'is_active'         => true,
            'capabilities'      => ['text'],
            'extra_config'      => ['driver' => 'openai'],
            'connection_status' => 'unchecked',
        ];
    }

    public function gemini(): static
    {
        return $this->state(fn () => [
            'name'         => 'Gemini',
            'slug'         => 'gemini',
            'model'        => 'gemini-2.0-flash-exp',
            'capabilities' => ['text', 'image'],
            'extra_config' => ['driver' => 'gemini'],
        ]);
    }

    public function openai(): static
    {
        return $this->state(fn () => [
            'name'         => 'OpenAI',
            'slug'         => 'openai',
            'model'        => 'gpt-4o-mini',
            'capabilities' => ['text'],
            'extra_config' => ['driver' => 'openai'],
        ]);
    }

    public function openaiImage(): static
    {
        return $this->state(fn () => [
            'name'         => 'DALL-E 3',
            'slug'         => 'dalle3',
            'model'        => 'dall-e-3',
            'capabilities' => ['image'],
            'extra_config' => ['driver' => 'openai-image'],
        ]);
    }

    public function stability(): static
    {
        return $this->state(fn () => [
            'name'         => 'Stability AI',
            'slug'         => 'stability',
            'model'        => 'core',
            'capabilities' => ['image'],
            'extra_config' => ['driver' => 'stability'],
        ]);
    }

    public function elevenlabs(): static
    {
        return $this->state(fn () => [
            'name'         => 'ElevenLabs',
            'slug'         => 'elevenlabs',
            'model'        => 'eleven_multilingual_v2',
            'capabilities' => ['tts'],
            'extra_config' => ['driver' => 'elevenlabs'],
        ]);
    }

    public function replicate(): static
    {
        return $this->state(fn () => [
            'name'         => 'Replicate',
            'slug'         => 'replicate',
            'model'        => 'minimax/video-01',
            'capabilities' => ['video'],
            'extra_config' => ['driver' => 'replicate'],
        ]);
    }

    public function runway(): static
    {
        return $this->state(fn () => [
            'name'         => 'RunwayML',
            'slug'         => 'runway',
            'model'        => 'gen4_turbo',
            'capabilities' => ['video'],
            'extra_config' => ['driver' => 'runway'],
        ]);
    }
}

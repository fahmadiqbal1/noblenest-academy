<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AIProviderConfig;
use App\Services\AIProviderGateway;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AIProviderGatewayTest extends TestCase
{
    #[Test]
    public function anthropic_provider_can_be_verified(): void
    {
        Http::fake([
            'https://api.anthropic.com/v1/messages' => Http::response([
                'id' => 'msg_test',
                'content' => [
                    ['type' => 'text', 'text' => 'OK'],
                ],
            ], 200),
        ]);

        $provider = new AIProviderConfig([
            'name' => 'Claude',
            'slug' => 'anthropic',
            'model' => 'claude-3-5-haiku-latest',
            'api_key_encrypted' => Crypt::encryptString('test-key'),
            'extra_config' => ['driver' => 'anthropic'],
        ]);

        $health = app(AIProviderGateway::class)->verify($provider);

        $this->assertSame('live', $health['status']);
    }

    #[Test]
    public function gemini_provider_can_generate_chat_content(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Gemini reply'],
                            ],
                        ],
                    ],
                ],
                'usageMetadata' => [
                    'totalTokenCount' => 42,
                ],
            ], 200),
        ]);

        $provider = new AIProviderConfig([
            'name' => 'Gemini',
            'slug' => 'gemini',
            'model' => 'gemini-1.5-flash',
            'api_key_encrypted' => Crypt::encryptString('test-key'),
            'extra_config' => ['driver' => 'gemini'],
        ]);

        $response = app(AIProviderGateway::class)->chat($provider, 'Hello there');

        $this->assertSame('Gemini reply', $response['content']);
        $this->assertSame(42, $response['tokens']);
    }

    #[Test]
    public function github_driver_is_marked_as_configured_without_api_calls(): void
    {
        Http::fake();

        $provider = new AIProviderConfig([
            'name' => 'Repo Source',
            'slug' => 'repo-source',
            'extra_config' => [
                'driver' => 'github',
                'repo_url' => 'https://github.com/example/repo',
            ],
        ]);

        $health = app(AIProviderGateway::class)->verify($provider);

        $this->assertSame('configured', $health['status']);
        Http::assertNothingSent();
    }
}

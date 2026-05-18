<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AIProviderConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * API-pipeline tranche: the AI assistant must degrade gracefully when no
 * provider / sidecar / API key is configured — a clean mock JSON reply,
 * never a 500 — and reject malformed input with 422, not 500.
 *
 * Decision (documented in QA_FINDINGS): the Python curriculum-ai sidecar
 * IS wired with graceful degradation (OrchestratorController falls back to
 * the PHP gateway; AIAssistantService falls back to mockResponse). Kept.
 */
class AiAssistantDegradationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        AIProviderConfig::query()->delete(); // ensure "no provider configured"
    }

    #[Test]
    public function returns_200_mock_reply_when_no_provider_configured(): void
    {
        $res = $this->postJson('/ai/assistant/message', ['message' => 'Hello, what can my 3 year old learn today?']);

        $res->assertOk()
            ->assertJsonStructure(['reply', 'provider', 'suggestions']);

        $this->assertNotEmpty($res->json('reply'));
    }

    #[Test]
    public function malformed_payload_is_422_not_500(): void
    {
        $this->postJson('/ai/assistant/message', [])
            ->assertStatus(422);

        $this->postJson('/ai/assistant/message', ['message' => str_repeat('x', 5000)])
            ->assertStatus(422);
    }
}

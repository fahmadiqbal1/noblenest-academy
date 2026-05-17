<?php

namespace Tests\Feature\Observability;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_basic_health_endpoint_is_public_and_responds(): void
    {
        // 200 ok | 503 degraded — both are valid; just must not 4xx (auth not required).
        $resp = $this->get('/health');
        $this->assertContains($resp->getStatusCode(), [200, 503]);
        $resp->assertJsonStructure(['status']);
    }

    public function test_detailed_endpoint_requires_bearer_token_when_configured(): void
    {
        config(['app.health_token' => 'secret-test-token']);

        // Missing token → 401
        $this->get('/health/detailed')->assertStatus(401);

        // Wrong token → 401
        $this->withHeaders(['Authorization' => 'Bearer wrong'])
            ->get('/health/detailed')
            ->assertStatus(401);

        // Correct token → 200 (or 503 if any check legitimately fails — both acceptable; not 401)
        $resp = $this->withHeaders(['Authorization' => 'Bearer secret-test-token'])
            ->get('/health/detailed');
        $this->assertNotSame(401, $resp->getStatusCode());
    }

    public function test_request_id_header_is_attached_to_every_response(): void
    {
        $resp = $this->get('/health');
        $this->assertNotEmpty($resp->headers->get('X-Request-Id'));

        // Honor caller-supplied id if it looks valid.
        $resp2 = $this->withHeaders(['X-Request-Id' => 'caller-abc-123'])->get('/health');
        $this->assertSame('caller-abc-123', $resp2->headers->get('X-Request-Id'));
    }
}

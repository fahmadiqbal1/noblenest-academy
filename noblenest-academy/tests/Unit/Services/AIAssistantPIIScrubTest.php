<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\AIAssistantService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AIAssistantPIIScrubTest extends TestCase
{
    #[Test]
    public function it_strips_top_level_pii_keys(): void
    {
        $clean = AIAssistantService::scrubPII([
            'name' => 'Aisha',
            'nickname' => 'Bee',
            'email' => 'a@b.com',
            'phone' => '+1 555 555 5555',
            'address' => '123 Main St',
            'ip' => '1.2.3.4',
            'age' => 5,
        ]);

        $this->assertArrayNotHasKey('name', $clean);
        $this->assertArrayNotHasKey('nickname', $clean);
        $this->assertArrayNotHasKey('email', $clean);
        $this->assertArrayNotHasKey('phone', $clean);
        $this->assertArrayNotHasKey('address', $clean);
        $this->assertArrayNotHasKey('ip', $clean);
        $this->assertSame(5, $clean['age']);
    }

    #[Test]
    public function it_strips_nested_pii_keys(): void
    {
        $clean = AIAssistantService::scrubPII([
            'child' => [
                'name' => 'Aisha',
                'parental_consent_at' => '2026-01-01T00:00:00Z',
                'parental_consent_ip' => '1.2.3.4',
                'age_months' => 48,
            ],
            'meta' => [
                'email' => 'p@b.com',
                'ok' => true,
            ],
        ]);

        $this->assertArrayNotHasKey('name', $clean['child']);
        $this->assertArrayNotHasKey('parental_consent_at', $clean['child']);
        $this->assertArrayNotHasKey('parental_consent_ip', $clean['child']);
        $this->assertSame(48, $clean['child']['age_months']);
        $this->assertArrayNotHasKey('email', $clean['meta']);
        $this->assertTrue($clean['meta']['ok']);
    }

    #[Test]
    public function it_strips_parental_consent_prefixed_keys(): void
    {
        $clean = AIAssistantService::scrubPII([
            'parental_consent_at' => 'x',
            'parental_consent_ip' => 'y',
            'parental_consent_user_agent' => 'z',
            'keep_me' => true,
        ]);
        $this->assertSame(['keep_me' => true], $clean);
    }
}

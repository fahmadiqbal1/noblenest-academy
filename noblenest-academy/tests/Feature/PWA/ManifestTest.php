<?php

declare(strict_types=1);

namespace Tests\Feature\PWA;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 8 — verifies the PWA manifest is served and wired into the
 * main layout head.
 */
class ManifestTest extends TestCase
{
    use RefreshDatabase;

    public function test_manifest_file_exists_and_is_valid_json(): void
    {
        // public/manifest.json is served by the webserver (nginx) in production,
        // not by Laravel's router, so we validate the file directly.
        $path = public_path('manifest.json');
        $this->assertFileExists($path);

        $json = json_decode((string) file_get_contents($path), true);
        $this->assertIsArray($json);
        $this->assertSame('NobleNest', $json['short_name'] ?? null);
        $this->assertSame('/', $json['start_url'] ?? null);
        $this->assertSame('standalone', $json['display'] ?? null);
        $this->assertNotEmpty($json['icons'] ?? []);
    }

    public function test_main_layout_head_contains_manifest_link_and_theme_color(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        $html = (string) $response->getContent();
        $this->assertStringContainsString('rel="manifest"', $html);
        $this->assertStringContainsString('manifest.json', $html);
        $this->assertStringContainsString('name="theme-color"', $html);
    }
}

<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Security tests for language switching.
 * Validates that the language switcher only accepts valid language codes.
 */
class LanguageSwitchSecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * List of allowed language codes.
     */
    protected array $allowedLanguages = ['en', 'fr', 'ru', 'zh', 'es', 'ko', 'ur', 'ar'];

    /**
     * Test that valid language codes are accepted.
     *
     * @dataProvider validLanguageProvider
     */
    public function test_valid_language_codes_accepted(string $lang): void
    {
        $response = $this->get("/lang/{$lang}");

        // Should redirect back (302) with the language set in session
        $response->assertStatus(302);
        $this->assertEquals($lang, session('lang'));
    }

    /**
     * Test that invalid language codes are rejected.
     *
     * @dataProvider invalidLanguageProvider
     */
    public function test_invalid_language_codes_rejected(string $lang): void
    {
        $response = $this->get("/lang/{$lang}");

        // Should return 400 Bad Request or 404 Not Found (both indicate rejection)
        $this->assertContains($response->status(), [400, 404]);

        // Only check session for non-empty strings (empty string route may not set session at all)
        if ($lang !== '') {
            $this->assertNotEquals($lang, session('lang'));
        }
    }

    /**
     * Test that XSS payloads in language parameter are rejected.
     */
    public function test_xss_in_language_rejected(): void
    {
        $xssPayloads = [
            '<script>alert(1)</script>',
            'javascript:alert(1)',
            '"><script>alert(1)</script>',
            "'; DROP TABLE users; --",
        ];

        foreach ($xssPayloads as $payload) {
            $response = $this->get('/lang/'.urlencode($payload));
            // Should return 400 Bad Request or 404 Not Found (both indicate rejection)
            $this->assertContains($response->status(), [400, 404]);
            $this->assertNotEquals($payload, session('lang'));
        }
    }

    /**
     * Test that very long language values are rejected.
     */
    public function test_long_language_values_rejected(): void
    {
        $longValue = str_repeat('a', 1000);
        $response = $this->get("/lang/{$longValue}");

        $response->assertStatus(400);
        $this->assertNotEquals($longValue, session('lang'));
    }

    /**
     * Provide valid language codes for testing.
     */
    public static function validLanguageProvider(): array
    {
        return [
            'English' => ['en'],
            'French' => ['fr'],
            'Russian' => ['ru'],
            'Chinese' => ['zh'],
            'Spanish' => ['es'],
            'Korean' => ['ko'],
            'Urdu' => ['ur'],
            'Arabic' => ['ar'],
        ];
    }

    /**
     * Provide invalid language codes for testing.
     */
    public static function invalidLanguageProvider(): array
    {
        return [
            'German (not supported)' => ['de'],
            'Japanese (not supported)' => ['ja'],
            'Empty string' => [''],
            'Random string' => ['xyz'],
            'Number' => ['123'],
            'Mixed case invalid' => ['EN'],
            'With spaces' => ['e n'],
        ];
    }
}

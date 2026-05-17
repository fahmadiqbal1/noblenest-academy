<?php

namespace Tests\Feature;

use App\Helpers\I18n;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public static function localeProvider(): array
    {
        return array_map(
            fn ($l) => [$l],
            array_keys(I18n::SUPPORTED_LANGUAGES)
        );
    }

    /**
     * @dataProvider localeProvider
     */
    public function test_lang_route_sets_locale_and_html_attrs(string $locale): void
    {
        $this->get("/lang/{$locale}");

        $response = $this->get('/');
        $response->assertOk();

        $this->assertSame($locale, app()->getLocale());

        $expectedDir = in_array($locale, I18n::RTL_LANGUAGES, true) ? 'rtl' : 'ltr';
        $response->assertSee('lang="'.$locale.'"', false);
        $response->assertSee('dir="'.$expectedDir.'"', false);
    }

    public function test_rtl_locales_render_rtl_direction(): void
    {
        foreach (['ur', 'ar'] as $locale) {
            $this->get("/lang/{$locale}");
            $this->get('/')
                ->assertOk()
                ->assertSee('dir="rtl"', false);
        }
    }

    public function test_ltr_locales_render_ltr_direction(): void
    {
        foreach (['en', 'fr', 'ru', 'zh', 'es', 'ko'] as $locale) {
            $this->get("/lang/{$locale}");
            $this->get('/')
                ->assertOk()
                ->assertSee('dir="ltr"', false);
        }
    }

    public function test_authenticated_user_preferred_language_wins(): void
    {
        $user = User::factory()->create(['preferred_language' => 'fr']);

        $this->actingAs($user)->get('/')->assertOk();

        $this->assertSame('fr', app()->getLocale());
    }

    public function test_bogus_locale_falls_back_to_english(): void
    {
        // Invalid code rejected by the /lang route (400), locale stays default.
        $this->get('/lang/xx')->assertStatus(400);

        $this->get('/')->assertOk();
        $this->assertSame('en', app()->getLocale());
    }

    public function test_bogus_user_preference_falls_back_to_english(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['preferred_language' => 'zz'])->saveQuietly();

        $this->actingAs($user)->get('/')->assertOk();

        $this->assertSame('en', app()->getLocale());
    }
}

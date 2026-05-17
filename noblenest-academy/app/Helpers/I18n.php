<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Lang;

/**
 * Internationalization Helper for Noble Nest Academy.
 *
 * Thin facade over Laravel's translator. Supports 8 languages:
 * en, fr, ru, zh, es, ko, ur, ar.
 *
 * Translation files live in lang/{locale}/messages.php (and other
 * namespace files). This class preserves the original public API so
 * the existing `I18n::get('key')` Blade calls keep working.
 */
class I18n
{
    /**
     * Supported languages with their native names.
     */
    public const SUPPORTED_LANGUAGES = [
        'en' => 'English',
        'fr' => 'Français',
        'ru' => 'Русский',
        'zh' => '中文',
        'es' => 'Español',
        'ko' => '한국어',
        'ur' => 'اردو',
        'ar' => 'العربية',
    ];

    /**
     * RTL languages.
     */
    public const RTL_LANGUAGES = ['ar', 'ur'];

    /**
     * Get a translation string.
     *
     * If $key contains a dot it is treated as a fully-qualified
     * translation key (e.g. "auth.failed"); otherwise it is resolved
     * against the "messages" namespace. If the translation is missing
     * the key itself is returned (legacy behavior).
     *
     * @param  string       $key      Translation key
     * @param  string|null  $lang     Language override (defaults to app locale)
     * @param  array        $replace  Placeholder replacements
     */
    public static function get(string $key, ?string $lang = null, array $replace = []): string
    {
        $locale = $lang ?: self::currentLanguage();
        $resolveKey = str_contains($key, '.') ? $key : "messages.$key";

        $value = trans($resolveKey, $replace, $locale);

        // trans() returns the key string itself when the line is missing.
        if ($value === $resolveKey || $value === $key) {
            return $key;
        }

        return is_string($value) ? $value : $key;
    }

    /**
     * Get current language from the application locale.
     */
    public static function currentLanguage(): string
    {
        $lang = app()->getLocale();

        if (! isset(self::SUPPORTED_LANGUAGES[$lang])) {
            return 'en';
        }

        return $lang;
    }

    /**
     * Check if the given (or current) language is RTL.
     */
    public static function isRtl(?string $lang = null): bool
    {
        $lang = $lang ?: self::currentLanguage();

        return in_array($lang, self::RTL_LANGUAGES, true);
    }

    /**
     * Get text direction for the given (or current) language.
     */
    public static function direction(?string $lang = null): string
    {
        return self::isRtl($lang) ? 'rtl' : 'ltr';
    }

    /**
     * Get all available language codes.
     */
    public static function availableLanguages(): array
    {
        return array_keys(self::SUPPORTED_LANGUAGES);
    }

    /**
     * Get languages with their native names.
     */
    public static function languageOptions(): array
    {
        return self::SUPPORTED_LANGUAGES;
    }

    /**
     * Check if a translation key exists.
     */
    public static function has(string $key, ?string $lang = null): bool
    {
        $locale = $lang ?: self::currentLanguage();
        $resolveKey = str_contains($key, '.') ? $key : "messages.$key";

        return Lang::has($resolveKey, $locale);
    }

    /**
     * Get all translations for the "messages" namespace in a language.
     */
    public static function all(?string $lang = null): array
    {
        $locale = $lang ?: self::currentLanguage();
        $path = base_path("lang/{$locale}/messages.php");

        if (! is_file($path)) {
            $path = base_path('lang/en/messages.php');
        }

        $data = is_file($path) ? require $path : [];

        return is_array($data) ? $data : [];
    }

    /**
     * Legacy no-op. Translation caching is now handled by Laravel.
     */
    public static function clearCache(): bool
    {
        return true;
    }

    /**
     * Legacy no-op. Translation caching is now handled by Laravel.
     */
    public static function warmCache(): void
    {
        // No-op: Laravel's translator loads lazily.
    }
}

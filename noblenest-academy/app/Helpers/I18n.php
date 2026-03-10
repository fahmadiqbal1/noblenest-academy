<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Internationalization Helper for Noble Nest Academy.
 * 
 * Supports 8 languages: en, fr, ru, zh, es, ko, ur, ar
 * Uses Redis caching for performance.
 */
class I18n
{
    /**
     * Cache key for translations.
     */
    protected const CACHE_KEY = 'i18n_translations';

    /**
     * Cache duration in seconds (1 hour).
     */
    protected const CACHE_TTL = 3600;

    /**
     * In-memory cache for current request.
     */
    protected static ?array $translations = null;

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
     * @param string $key Translation key
     * @param string|null $lang Language code (defaults to session language)
     * @param array $replace Replacement values for placeholders
     * @return string
     */
    public static function get(string $key, ?string $lang = null, array $replace = []): string
    {
        $translations = self::loadTranslations();
        $lang = $lang ?: self::currentLanguage();

        // Try requested language, then fall back to English, then return key
        $value = $translations[$lang][$key] 
            ?? $translations['en'][$key] 
            ?? $key;

        // Handle placeholder replacements
        if (!empty($replace)) {
            foreach ($replace as $placeholder => $replacement) {
                $value = str_replace(":{$placeholder}", $replacement, $value);
            }
        }

        return $value;
    }

    /**
     * Get current language from session.
     */
    public static function currentLanguage(): string
    {
        $lang = session('lang', config('app.locale', 'en'));
        
        // Validate it's a supported language
        if (!isset(self::SUPPORTED_LANGUAGES[$lang])) {
            return 'en';
        }

        return $lang;
    }

    /**
     * Check if current language is RTL.
     */
    public static function isRtl(?string $lang = null): bool
    {
        $lang = $lang ?: self::currentLanguage();
        return in_array($lang, self::RTL_LANGUAGES, true);
    }

    /**
     * Get text direction for current language.
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
     * Load translations with caching.
     */
    protected static function loadTranslations(): array
    {
        // Return in-memory cache if available
        if (self::$translations !== null) {
            return self::$translations;
        }

        // Try to get from Redis/cache
        try {
            self::$translations = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                return self::loadFromFile();
            });
        } catch (\Exception $e) {
            // If cache fails, load directly from file
            Log::warning('I18n cache failed, loading from file', ['error' => $e->getMessage()]);
            self::$translations = self::loadFromFile();
        }

        return self::$translations;
    }

    /**
     * Load translations directly from JSON file.
     */
    protected static function loadFromFile(): array
    {
        $path = resource_path('lang/i18n.json');

        if (!file_exists($path)) {
            Log::error('I18n translation file not found', ['path' => $path]);
            return ['en' => []];
        }

        $json = file_get_contents($path);
        $translations = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('I18n JSON parse error', ['error' => json_last_error_msg()]);
            return ['en' => []];
        }

        return $translations;
    }

    /**
     * Clear the translation cache.
     * Call this after updating the i18n.json file.
     */
    public static function clearCache(): bool
    {
        self::$translations = null;
        return Cache::forget(self::CACHE_KEY);
    }

    /**
     * Warm the cache by preloading translations.
     */
    public static function warmCache(): void
    {
        self::$translations = null;
        Cache::forget(self::CACHE_KEY);
        self::loadTranslations();
    }

    /**
     * Check if a translation key exists.
     */
    public static function has(string $key, ?string $lang = null): bool
    {
        $translations = self::loadTranslations();
        $lang = $lang ?: self::currentLanguage();

        return isset($translations[$lang][$key]) || isset($translations['en'][$key]);
    }

    /**
     * Get all translations for a language.
     */
    public static function all(?string $lang = null): array
    {
        $translations = self::loadTranslations();
        $lang = $lang ?: self::currentLanguage();

        return $translations[$lang] ?? $translations['en'] ?? [];
    }
}


<?php

namespace App\Helpers;

class I18n
{
    protected static $translations = null;

    public static function get($key, $lang = null)
    {
        if (self::$translations === null) {
            $json = file_get_contents(resource_path('lang/i18n.json'));
            self::$translations = json_decode($json, true);
        }
        $lang = $lang ?: (session('lang') ?? 'en');
        return self::$translations[$lang][$key] ?? self::$translations['en'][$key] ?? $key;
    }

    public static function availableLanguages()
    {
        if (self::$translations === null) {
            $json = file_get_contents(resource_path('lang/i18n.json'));
            self::$translations = json_decode($json, true);
        }
        return array_keys(self::$translations);
    }
}


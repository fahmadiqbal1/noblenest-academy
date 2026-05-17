<?php

namespace App\Http\Middleware;

use App\Helpers\I18n;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the request locale and applies it to the application.
 *
 * Resolution priority:
 *   1. Authenticated user's preferred_language
 *   2. Session "lang" key (set by the /lang/{lang} route)
 *   3. Accept-Language header (first supported match)
 *   4. config('app.locale'), falling back to 'en'
 *
 * If the locale is resolved from session/header and differs from an
 * authenticated user's stored preference, it is persisted back to the
 * user (only on change).
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(I18n::SUPPORTED_LANGUAGES);
        $user = $request->user();

        $resolved = null;
        $source = null;

        // 1. Authenticated user preference.
        if ($user && in_array($user->preferred_language, $supported, true)) {
            $resolved = $user->preferred_language;
            $source = 'user';
        }

        // 2. Session.
        if ($resolved === null) {
            $sessionLang = $request->session()->get('lang');
            if (is_string($sessionLang) && in_array($sessionLang, $supported, true)) {
                $resolved = $sessionLang;
                $source = 'session';
            }
        }

        // 3. Accept-Language header.
        if ($resolved === null) {
            $headerLang = $this->fromAcceptLanguage($request, $supported);
            if ($headerLang !== null) {
                $resolved = $headerLang;
                $source = 'header';
            }
        }

        // 4. Config default.
        if ($resolved === null) {
            $configLocale = config('app.locale', 'en');
            $resolved = in_array($configLocale, $supported, true) ? $configLocale : 'en';
            $source = 'config';
        }

        app()->setLocale($resolved);

        // Persist to user when resolved from session/header and changed.
        if ($user
            && in_array($source, ['session', 'header'], true)
            && $user->preferred_language !== $resolved
        ) {
            $user->update(['preferred_language' => $resolved]);
        }

        return $next($request);
    }

    /**
     * Pick the first supported locale from the Accept-Language header.
     *
     * @param  array<int,string>  $supported
     */
    private function fromAcceptLanguage(Request $request, array $supported): ?string
    {
        $header = $request->server('HTTP_ACCEPT_LANGUAGE');
        if (! is_string($header) || $header === '') {
            return null;
        }

        foreach (explode(',', $header) as $part) {
            $tag = strtolower(trim(explode(';', $part)[0]));
            $primary = explode('-', $tag)[0];

            if (in_array($primary, $supported, true)) {
                return $primary;
            }
        }

        return null;
    }
}

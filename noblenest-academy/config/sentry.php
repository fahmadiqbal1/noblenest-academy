<?php

/**
 * Phase 6 — Sentry config (kept dormant until `composer require sentry/sentry-laravel`).
 *
 * Once installed, the sentry-laravel package will auto-register its
 * ServiceProvider, read this file, and bind a singleton on the
 * Application's exception handler chain. Until then this file is
 * inert — `bootstrap/app.php` reads `config('sentry.dsn')`, finds null
 * (because SENTRY_LARAVEL_DSN isn't set), and falls through.
 *
 * To activate:
 *   composer require sentry/sentry-laravel
 *   echo "SENTRY_LARAVEL_DSN=https://<key>@sentry.io/<project>" >> .env
 *   php artisan config:cache
 */
return [
    'dsn' => env('SENTRY_LARAVEL_DSN'),

    'release' => env('SENTRY_RELEASE', config('app.version')),

    'environment' => env('SENTRY_ENVIRONMENT', config('app.env')),

    // Reduce volume: only capture 100% of errors, but sample 10% of transactions.
    'sample_rate'           => (float) env('SENTRY_SAMPLE_RATE', 1.0),
    'traces_sample_rate'    => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.10),
    'profiles_sample_rate'  => (float) env('SENTRY_PROFILES_SAMPLE_RATE', 0.10),

    // Strip PII automatically. The dashboard can still see "user X had an error"
    // via the breadcrumb chain, but not "user X has email …".
    'send_default_pii' => false,

    // Don't report HTTP exceptions we want users to see (404s, 419 CSRF, 422 validation).
    'ignore_exceptions' => [
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Illuminate\Validation\ValidationException::class,
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Session\TokenMismatchException::class,
    ],

    // Breadcrumbs: capture log lines + SQL queries (without parameter values).
    'breadcrumbs' => [
        'logs'    => true,
        'sql_queries'  => true,
        'sql_bindings' => false,
        'queue_info'   => true,
        'command_info' => true,
    ],
];

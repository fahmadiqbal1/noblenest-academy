<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global HTTP middleware — applied to every request
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Resolve & apply request locale (user pref → session → header → config)
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);

        // Exclude Stripe webhook from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhook/stripe',
        ]);

        // Register route middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'subscription.active' => \App\Http\Middleware\EnsureSubscriptionActive::class,
            'feature' => \App\Http\Middleware\EnsureFeatureEnabled::class,
            // Phase 5: under-13 COPPA / GDPR-K parental consent gate.
            'parental.consent' => \App\Http\Middleware\RequireParentalConsent::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

<?php

use App\Http\Middleware\EnsureFeatureEnabled;
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\RequestId;
use App\Http\Middleware\RequireParentalConsent;
use App\Http\Middleware\RequireParentPin;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SetLocale;
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
        $middleware->prepend(RequestId::class);
        $middleware->append(SecurityHeaders::class);

        // Resolve & apply request locale (user pref → session → header → config)
        $middleware->appendToGroup('web', SetLocale::class);

        // Payment provider webhooks are server-to-server and signature-verified
        // in their controllers — they must bypass CSRF (a missing exemption
        // 419s every legitimate webhook).
        $middleware->validateCsrfTokens(except: [
            'webhook/stripe',
            'webhook/paypal',
        ]);

        // Register route middleware aliases
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'subscription.active' => EnsureSubscriptionActive::class,
            'feature' => EnsureFeatureEnabled::class,
            // Phase 5: under-13 COPPA / GDPR-K parental consent gate.
            'parental.consent' => RequireParentalConsent::class,
            // Phase 5: 4-digit parent PIN gate for sensitive parent routes.
            'parent.pin' => RequireParentPin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

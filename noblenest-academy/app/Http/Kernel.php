<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'subscription.active' => \App\Http\Middleware\EnsureSubscriptionActive::class,
        'feature' => \App\Http\Middleware\EnsureFeatureEnabled::class,
        'maternal.consent' => \App\Http\Middleware\EnsureMaternalConsent::class,
        'practitioner.active' => \App\Http\Middleware\EnsurePractitionerActive::class,
    ];
}

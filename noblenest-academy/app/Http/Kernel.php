<?php

namespace App\Http;

use App\Http\Middleware\EnsureFeatureEnabled;
use App\Http\Middleware\EnsureSubscriptionActive;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'role' => RoleMiddleware::class,
        'auth' => Authenticate::class,
        'subscription.active' => EnsureSubscriptionActive::class,
        'feature' => EnsureFeatureEnabled::class,
    ];
}

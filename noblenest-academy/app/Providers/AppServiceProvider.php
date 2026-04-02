<?php

namespace App\Providers;

use App\Services\AIAssistantService;
use App\Services\AIProviderGateway;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register AI Provider Gateway as singleton
        $this->app->singleton(AIProviderGateway::class, function ($app) {
            return new AIProviderGateway();
        });

        // Register AI Assistant Service
        $this->app->singleton(AIAssistantService::class, function ($app) {
            return new AIAssistantService($app->make(AIProviderGateway::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure global alias for I18n helper so Blade can call I18n::get()
        if (!class_exists('I18n')) {
            class_alias(\App\Helpers\I18n::class, 'I18n');
        }

        // Force HTTPS in production or when APP_URL uses https
        $appUrl = (string) config('app.url');
        if (app()->environment('production') || str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }

        // Horizon dashboard: only admins can access /horizon
        Horizon::auth(function ($request) {
            /** @var \App\Models\User|null $user */
            $user = $request->user();
            return $user !== null && $user->hasRole('admin');
        });
    }
}

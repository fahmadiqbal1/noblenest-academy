<?php

namespace App\Providers;

use App\Services\AIAssistantService;
use App\Services\AIProviderGateway;
use App\Services\Providers\ChatProviderService;
use App\Services\Providers\ImageGenerationService;
use App\Services\Providers\MediaGenerationService;
use Illuminate\Support\Facades\Route;
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
        // Register AI Provider Gateway as singleton with its dependencies
        $this->app->singleton(AIProviderGateway::class, function ($app) {
            return new AIProviderGateway(
                $app->make(ChatProviderService::class),
                $app->make(ImageGenerationService::class),
                $app->make(MediaGenerationService::class),
            );
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
        // Bind {child} route parameter to ChildProfile model
        Route::model('child', \App\Models\ChildProfile::class);

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

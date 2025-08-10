<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
    }
}

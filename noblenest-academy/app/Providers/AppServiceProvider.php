<?php

namespace App\Providers;

use App\Services\AIAssistantService;
use App\Services\AIProviderGateway;
use App\Services\Providers\ChatProviderService;
use App\Services\Providers\ImageGenerationService;
use App\Services\Providers\MediaGenerationService;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Auth;
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
        // Event listeners are wired explicitly in App\Providers\EventServiceProvider.
        // Without this, Laravel's base EventServiceProvider also auto-discovers
        // every listener, registering each one a SECOND time (double-firing —
        // e.g. ChildSkillState streaks incrementing by 2 per activity).
        \Illuminate\Foundation\Support\Providers\EventServiceProvider::disableEventDiscovery();

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

        // Phase 6 — pluggable AI content pipeline providers.
        $this->app->singleton(\App\Services\Providers\VideoAvatarProvider::class, function ($app) {
            return match (config('services.video_avatar.driver', 'null')) {
                'heygen'    => new \App\Services\Providers\VideoAvatar\HeyGenAdapter((string) config('services.heygen.api_key', '')),
                'synthesia' => new \App\Services\Providers\VideoAvatar\SynthesiaAdapter((string) config('services.synthesia.api_key', '')),
                default     => new \App\Services\Providers\VideoAvatar\NullAdapter(),
            };
        });
        $this->app->singleton(\App\Services\Providers\WhisperAdapter::class, function ($app) {
            return match (config('services.whisper.driver', 'local')) {
                'openai' => new \App\Services\Providers\Whisper\OpenAIWhisperAdapter((string) config('services.whisper.api_key', '')),
                default  => new \App\Services\Providers\Whisper\LocalWhisperAdapter(),
            };
        });
        $this->app->singleton(\App\Services\Providers\AnthropicTranslator::class, function ($app) {
            return new \App\Services\Providers\AnthropicTranslator((string) config('services.groq.api_key', ''));
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

        // Redirect authenticated users away from guest-only routes (login, register)
        // to their role-appropriate dashboard instead of the marketing home page.
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $user = Auth::user();
            if (!$user) {
                return route('noble.home');
            }
            return match ($user->role) {
                'Parent'       => route('parent.dashboard'),
                'Admin'        => route('admin.analytics.index'),
                default        => route('noble.home'),
            };
        });

        // Horizon dashboard: only admins can access /horizon
        Horizon::auth(function ($request) {
            /** @var \App\Models\User|null $user */
            $user = $request->user();
            return $user !== null && $user->hasRole('admin');
        });
    }
}

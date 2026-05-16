<?php

namespace App\Providers;

use App\Events\ActivityCompleted;
use App\Events\BadgeEarned;
use App\Events\MilestoneUnlocked;
use App\Events\ParentDisengaged;
use App\Events\StreakAtRisk;
use App\Events\StruggleDetected;
use App\Listeners\HandleBadgeEarnedListener;
use App\Listeners\HandleMilestoneUnlockedListener;
use App\Listeners\HandleParentDisengagedListener;
use App\Listeners\HandleStreakAtRiskListener;
use App\Listeners\HandleStruggleDetectedListener;
use App\Listeners\UpdateChildSkillStateListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ActivityCompleted::class => [
            UpdateChildSkillStateListener::class,
        ],

        // Viral Growth & Retention events (Phase 1 scaffold — bodies filled in later phases)
        MilestoneUnlocked::class => [
            HandleMilestoneUnlockedListener::class,
        ],
        BadgeEarned::class => [
            HandleBadgeEarnedListener::class,
        ],
        StreakAtRisk::class => [
            HandleStreakAtRiskListener::class,
        ],
        StruggleDetected::class => [
            HandleStruggleDetectedListener::class,
        ],
        ParentDisengaged::class => [
            HandleParentDisengagedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

<?php

namespace App\Console\Commands;

use App\Helpers\I18n;
use App\Jobs\ProduceLocalizedVideoJob;
use App\Models\Activity;
use App\Models\ActivityMedia;
use Illuminate\Console\Command;

/**
 * Phase 6 — idempotent backfill: dispatch ProduceLocalizedVideoJob for any
 * Activity that lacks ActivityMedia rows for the requested locale(s).
 *
 *   php artisan content:backfill-media --locale=all --limit=10 --cost-cap=2.50
 *
 * `--cost-cap` is a rough estimator only (Null/Local adapters are free; real
 * adapters charge per generation). Stop when the projected per-activity cost
 * × pending count exceeds the cap.
 */
class ContentBackfillMedia extends Command
{
    protected $signature = 'content:backfill-media
                            {--locale=all : Single locale or "all"}
                            {--limit=10 : Max activities to dispatch}
                            {--cost-cap=5.00 : Estimated USD cap (best-effort)}';

    protected $description = 'Backfill localized avatar videos + subtitles for activities missing media';

    /** Rough per-activity (all 8 locales) cost estimate, USD. Null/Local = $0. */
    private const COST_PER_ACTIVITY = 0.40;

    public function handle(): int
    {
        $locale = (string) $this->option('locale');
        $limit = (int) $this->option('limit');
        $cap = (float) $this->option('cost-cap');

        $locales = $locale === 'all' ? array_keys(I18n::SUPPORTED_LANGUAGES) : [$locale];

        $missing = Activity::query()
            ->whereDoesntHave('media', function ($q) use ($locales) {
                $q->whereIn('modality', array_map(fn ($l) => "video:{$l}", $locales));
            })
            ->limit($limit)
            ->get();

        if ($missing->isEmpty()) {
            $this->info('Nothing to backfill — every activity already has media for the requested locale(s).');

            return self::SUCCESS;
        }

        $projected = $missing->count() * self::COST_PER_ACTIVITY;
        if ($projected > $cap) {
            $this->warn(sprintf(
                'Projected spend $%.2f exceeds cap $%.2f; dispatching only the first %d activities.',
                $projected, $cap, (int) floor($cap / self::COST_PER_ACTIVITY)
            ));
            $missing = $missing->take((int) floor($cap / self::COST_PER_ACTIVITY));
        }

        $dispatched = 0;
        foreach ($missing as $activity) {
            ProduceLocalizedVideoJob::dispatch($activity->id);
            $dispatched++;
        }

        $this->info("Dispatched {$dispatched} ProduceLocalizedVideoJob(s). Watch /horizon.");

        return self::SUCCESS;
    }
}

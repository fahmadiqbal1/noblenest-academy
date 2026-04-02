<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\SendDailyDigestJob;
use App\Models\Activity;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily learning digest — parents receive yesterday's summary at 07:00 UTC
Schedule::job(new SendDailyDigestJob)->dailyAt('07:00')->withoutOverlapping();

// Export all activities to JSON for PageIndex curriculum sidecar
// Usage: php artisan curriculum:export-json --output=/tmp/activities_export.json
// Scheduled nightly so the PageIndex is always fresh
Artisan::command('curriculum:export-json {--output=}', function () {
    $output = $this->option('output') ?? storage_path('app/activities_export.json');
    $activities = Activity::select([
        'id', 'title', 'description', 'subject', 'age_group',
        'language', 'is_free', 'is_muslim_only', 'duration_minutes',
        'difficulty', 'learning_objectives',
    ])->get()->toArray();

    file_put_contents($output, json_encode($activities, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $this->info("Exported " . count($activities) . " activities to {$output}");
})->purpose('Export activities to JSON for the curriculum-ai PageIndex sidecar');

Schedule::command('curriculum:export-json')->dailyAt('02:30');

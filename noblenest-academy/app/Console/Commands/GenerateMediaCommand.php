<?php

namespace App\Console\Commands;

use App\Jobs\GenerateActivityMediaJob;
use App\Models\Activity;
use Illuminate\Console\Command;

class GenerateMediaCommand extends Command
{
    protected $signature = 'media:generate
        {--type=thumbnail : Media type: thumbnail, audio, or video}
        {--subject= : Filter activities by subject}
        {--age-tier= : Filter by age group (e.g. "3-4")}
        {--limit=10 : Maximum activities to process}
        {--provider= : AI provider config ID}
        {--missing-only : Only process activities missing the specified media type}';

    protected $description = 'Batch-dispatch media generation jobs for activities';

    public function handle(): int
    {
        $type = $this->option('type');
        if (! in_array($type, ['thumbnail', 'audio', 'video'])) {
            $this->error("Invalid type '{$type}'. Must be: thumbnail, audio, or video.");

            return self::FAILURE;
        }

        $column = match ($type) {
            'thumbnail' => 'thumbnail_url',
            'audio' => 'audio_url',
            'video' => 'video_url',
        };

        $query = Activity::query();

        if ($subject = $this->option('subject')) {
            $query->where('subject', $subject);
        }

        if ($ageTier = $this->option('age-tier')) {
            $query->where('age_group', $ageTier);
        }

        if ($this->option('missing-only')) {
            $query->whereNull($column);
        }

        $limit = (int) $this->option('limit');
        $activities = $query->take($limit)->get();

        if ($activities->isEmpty()) {
            $this->warn('No matching activities found.');

            return self::SUCCESS;
        }

        $providerId = $this->option('provider') ? (int) $this->option('provider') : null;
        $dispatched = 0;

        $bar = $this->output->createProgressBar($activities->count());
        $bar->start();

        foreach ($activities as $activity) {
            GenerateActivityMediaJob::dispatch(
                $activity->id,
                $type,
                $providerId,
                null,
                null
            );
            $dispatched++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Dispatched {$dispatched} {$type} generation job(s).");

        return self::SUCCESS;
    }
}

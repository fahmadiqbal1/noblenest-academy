<?php

namespace App\Console\Commands;

use App\Services\CurriculumHealthService;
use App\Jobs\GenerateActivityMediaJob;
use App\Models\Activity;
use App\Models\AIJob;
use App\Models\AIProviderConfig;
use App\Services\AIProviderGateway;
use Illuminate\Console\Command;

class AutoGenerateCurriculumCommand extends Command
{
    protected $signature = 'curriculum:auto-generate
        {--dry-run : Show gaps without generating anything}
        {--max-activities=20 : Maximum activities to generate per run}
        {--with-media : Also dispatch thumbnail generation for new activities}';

    protected $description = 'Scan curriculum gaps and auto-generate activities + optional media';

    public function handle(CurriculumHealthService $healthService, AIProviderGateway $gateway): int
    {
        $this->info('Scanning curriculum gaps...');

        $gaps = collect($healthService->getGaps());
        $score = $healthService->getHealthScore();

        $this->info("Health Score: {$score}% — {$gaps->count()} gap(s) found.");

        if ($gaps->isEmpty()) {
            $this->info('No gaps detected. Curriculum is fully covered.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->table(
                ['Age Group', 'Skill', 'Current Count', 'Target'],
                $gaps->map(fn ($g) => [$g['age'], $g['skill'], $g['count'], $g['target']])->toArray()
            );
            $this->newLine();
            $this->line($healthService->getGapReport());
            return self::SUCCESS;
        }

        $max = (int) $this->option('max-activities');
        $generated = 0;
        $withMedia = $this->option('with-media');

        // Find the best text provider
        $textProvider = AIProviderConfig::where('is_active', true)
            ->whereJsonContains('capabilities', 'text')
            ->first();

        if (! $textProvider) {
            $this->error('No active text-capable AI provider found. Add one in the Orchestrator.');
            return self::FAILURE;
        }

        // Hoist image provider query outside the loop to avoid N+1
        $imageProvider = $withMedia
            ? AIProviderConfig::where('is_active', true)
                ->where(function ($q) {
                    $q->whereJsonContains('capabilities', 'image')
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(extra_config, '$.driver')) IN ('gemini', 'stability', 'openai-image')");
                })->first()
            : null;

        $bar = $this->output->createProgressBar(min($gaps->count(), $max));
        $bar->start();

        foreach ($gaps->take($max) as $gap) {
            $prompt = "Create a single educational activity for children age {$gap['age']} in the '{$gap['skill']}' domain. "
                . "Return JSON with keys: title, description, instructions (array of steps), materials_needed (array), "
                . "learning_objectives (array), duration_minutes (integer), difficulty (easy/medium/hard). "
                . "Make it engaging, age-appropriate, and hands-on.";

            try {
                $aiJob = AIJob::create([
                    'type' => 'activity_generation',
                    'status' => 'running',
                    'provider' => $textProvider->slug,
                    'locale' => 'en',
                    'payload' => ['prompt' => $prompt, 'age' => $gap['age'], 'skill' => $gap['skill']],
                ]);

                $result = $gateway->chat($textProvider, $prompt);
                $content = is_array($result) ? $result : json_decode($result, true);

                if (! $content || ! isset($content['title'])) {
                    $aiJob->update(['status' => 'failed', 'error_message' => 'Invalid response structure']);
                    $bar->advance();
                    continue;
                }

                $activity = Activity::create([
                    'title' => $content['title'],
                    'description' => $content['description'] ?? '',
                    'type' => 'guided',
                    'subject' => $gap['skill'],
                    'age_group' => $gap['age'],
                    'language' => 'en',
                    'instructions' => $content['instructions'] ?? [],
                    'materials_needed' => $content['materials_needed'] ?? [],
                    'learning_objectives' => $content['learning_objectives'] ?? [],
                    'duration_minutes' => $content['duration_minutes'] ?? 15,
                    'difficulty' => $content['difficulty'] ?? 'easy',
                    'is_free' => true,
                ]);

                $aiJob->update([
                    'status' => 'completed',
                    'result' => ['activity_id' => $activity->id, 'title' => $activity->title],
                ]);

                if ($withMedia && $imageProvider) {
                    GenerateActivityMediaJob::dispatch(
                            $activity->id,
                            'thumbnail',
                            $imageProvider->id
                        );
                }

                $generated++;
            } catch (\Throwable $e) {
                $this->warn("Failed for age {$gap['age']}/{$gap['skill']}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Generated {$generated} activities. Score was {$score}%.");

        return self::SUCCESS;
    }
}

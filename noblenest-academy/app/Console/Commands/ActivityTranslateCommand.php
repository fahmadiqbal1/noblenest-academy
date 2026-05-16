<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\ActivityTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

/**
 * Phase 3 — populate `activity_translations` for a target locale.
 *
 * Usage:
 *   php artisan activity:translate ar
 *   php artisan activity:translate fr --fields=title,description --provider=openai
 *   php artisan activity:translate ur --dry-run --limit=10
 *
 * Providers (pluggable):
 *   echo          — prepends "[locale] " to the source value (dev / smoke).
 *   curriculum-ai — POST to the Python LangChain sidecar at services/curriculum-ai/.
 *   openai        — direct OpenAI Batch API call (CRG_OPENAI_API_KEY).
 *
 * Idempotent: rows already present in activity_translations are skipped
 * unless --force is passed.
 */
class ActivityTranslateCommand extends Command
{
    protected $signature = 'activity:translate
                            {locale : Target locale (fr, ru, zh, es, ko, ur, ar)}
                            {--fields=title,description,instructions_for_parent,benefit_explanation : Fields to translate, comma-separated}
                            {--provider=echo : echo | curriculum-ai | openai}
                            {--limit= : Process at most N activities}
                            {--dry-run : Plan + count without writing}
                            {--force : Re-translate even if a row already exists}';

    protected $description = 'Batch-translate Activity fields into a target locale and persist to activity_translations.';

    public function handle(): int
    {
        $locale = $this->argument('locale');
        if (! in_array($locale, ['fr', 'ru', 'zh', 'es', 'ko', 'ur', 'ar'], true)) {
            $this->error("Unsupported locale: {$locale}. Use fr|ru|zh|es|ko|ur|ar.");
            return self::FAILURE;
        }

        $fields = array_filter(array_map('trim', explode(',', (string) $this->option('fields'))));
        if (! $fields) {
            $this->error('No fields specified.');
            return self::FAILURE;
        }

        $provider = $this->option('provider');
        if (! in_array($provider, ['echo', 'curriculum-ai', 'openai'], true)) {
            $this->error("Unknown provider: {$provider}.");
            return self::FAILURE;
        }

        $query = Activity::query();
        if ($limit = (int) $this->option('limit')) {
            $query->limit($limit);
        }

        $total = $query->count();
        $this->info("Translating {$total} activities → {$locale} via {$provider} (fields: " . implode(',', $fields) . ")");

        $created = 0; $skipped = 0; $errors = 0;

        $query->orderBy('id')->chunk(50, function ($activities) use ($locale, $fields, $provider, &$created, &$skipped, &$errors) {
            foreach ($activities as $activity) {
                foreach ($fields as $field) {
                    $source = (string) ($activity->{$field} ?? '');
                    if ($source === '') { $skipped++; continue; }

                    $existing = ActivityTranslation::query()
                        ->where('activity_id', $activity->id)
                        ->where('locale', $locale)
                        ->where('field', $field)
                        ->first();

                    if ($existing && ! $this->option('force')) {
                        $skipped++;
                        continue;
                    }

                    try {
                        $translated = $this->translate($source, $locale, $provider);
                    } catch (\Throwable $e) {
                        $this->warn("activity#{$activity->id} field={$field}: " . $e->getMessage());
                        $errors++;
                        continue;
                    }

                    if ($this->option('dry-run')) {
                        $created++;
                        continue;
                    }

                    ActivityTranslation::updateOrCreate(
                        ['activity_id' => $activity->id, 'locale' => $locale, 'field' => $field],
                        ['value' => $translated]
                    );
                    $created++;
                }
            }
        });

        $this->newLine();
        $this->info(sprintf(
            '%s: %d created/updated, %d skipped, %d errors',
            $this->option('dry-run') ? 'Dry-run' : 'Done',
            $created, $skipped, $errors
        ));
        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function translate(string $source, string $locale, string $provider): string
    {
        return match ($provider) {
            'echo'          => "[{$locale}] {$source}",
            'curriculum-ai' => $this->translateViaSidecar($source, $locale),
            'openai'        => $this->translateViaOpenAI($source, $locale),
            default         => $source,
        };
    }

    private function translateViaSidecar(string $source, string $locale): string
    {
        $url = env('CURRICULUM_AI_URL', 'http://127.0.0.1:8001') . '/translate';
        $payload = ['text' => $source, 'target_locale' => $locale, 'domain' => 'children-education'];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 30,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code < 200 || $code >= 300) {
            throw new \RuntimeException("Sidecar HTTP {$code}: " . substr((string) $body, 0, 120));
        }
        $decoded = json_decode((string) $body, true);
        return (string) Arr::get($decoded, 'translation', $source);
    }

    private function translateViaOpenAI(string $source, string $locale): string
    {
        $key = env('CRG_OPENAI_API_KEY') ?: env('OPENAI_API_KEY');
        if (! $key) throw new \RuntimeException('OPENAI_API_KEY not set.');

        $model = env('CRG_OPENAI_MODEL', 'gpt-4o-mini');
        $localeName = ['fr' => 'French', 'ru' => 'Russian', 'zh' => 'Mandarin Chinese', 'es' => 'Spanish', 'ko' => 'Korean', 'ur' => 'Urdu', 'ar' => 'Arabic'][$locale] ?? $locale;

        $payload = [
            'model'    => $model,
            'messages' => [
                ['role' => 'system', 'content' => "You translate child-education content into {$localeName}. Preserve a warm, parent-friendly tone. Output the translation only, nothing else."],
                ['role' => 'user',   'content' => $source],
            ],
            'temperature' => 0.2,
        ];

        $ch = curl_init(env('CRG_OPENAI_BASE_URL', 'https://api.openai.com/v1') . '/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', "Authorization: Bearer {$key}"],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 30,
        ]);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code < 200 || $code >= 300) {
            throw new \RuntimeException("OpenAI HTTP {$code}: " . substr((string) $body, 0, 120));
        }
        return trim((string) Arr::get(json_decode((string) $body, true), 'choices.0.message.content', $source));
    }
}

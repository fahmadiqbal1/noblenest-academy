<?php

namespace App\Console\Commands;

use App\Helpers\I18n;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Phase 3 — machine-translate the lang/en/*.php namespace files into the
 * other 7 supported locales via Groq (OpenAI-compatible) llama-3.3-70b-versatile.
 *
 * Usage:
 *   php artisan i18n:translate
 *   php artisan i18n:translate --locale=fr
 *   php artisan i18n:translate --cost-cap=2.50 --force
 *
 * Idempotent: only missing/empty target keys are filled unless --force.
 * Without GROQ_API_KEY it does NOT fail — it writes the English values
 * verbatim (untranslated passthrough), records them in
 * lang/_meta/{locale}.json and exits 0.
 */
class I18nTranslate extends Command
{
    protected $signature = 'i18n:translate
                            {--locale= : Single target locale (default: all 7 non-en)}
                            {--cost-cap=5.00 : Hard USD spend cap}
                            {--force : Overwrite existing non-empty translations}';

    protected $description = 'Machine-translate lang/en/*.php into the other supported locales';

    /** USD per token rough estimate (Groq llama-3.3-70b ≈ $0.59/M in + $0.79/M out). */
    private const COST_PER_TOKEN = 0.000001;

    private const BATCH_SIZE = 50;

    private const MODEL = 'llama-3.3-70b-versatile';

    private const ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

    public function handle(): int
    {
        $sourceDir = base_path('lang/en');
        $metaDir = base_path('lang/_meta');

        if (! is_dir($sourceDir)) {
            $this->error("Source dir not found: {$sourceDir}");

            return self::FAILURE;
        }
        if (! is_dir($metaDir)) {
            mkdir($metaDir, 0775, true);
        }

        $costCap = (float) $this->option('cost-cap');
        $force = (bool) $this->option('force');
        $apiKey = (string) config('services.groq.api_key', '');
        $passthrough = $apiKey === '';

        if ($passthrough) {
            $this->warn('GROQ_API_KEY not set — writing UNTRANSLATED PASSTHROUGH (English verbatim). The lead should re-run with a key.');
        }

        $allLocales = array_keys(I18n::SUPPORTED_LANGUAGES);
        $targets = $this->option('locale')
            ? [$this->option('locale')]
            : array_values(array_filter($allLocales, fn ($l) => $l !== 'en'));

        $sourceFiles = glob($sourceDir.'/*.php') ?: [];
        $spend = 0.0;
        $summary = [];

        foreach ($targets as $locale) {
            if (! in_array($locale, $allLocales, true) || $locale === 'en') {
                $this->warn("Skipping unsupported/source locale: {$locale}");

                continue;
            }

            $machineKeys = [];
            $passthroughKeys = [];

            foreach ($sourceFiles as $sourceFile) {
                $file = basename($sourceFile);
                $en = $this->loadArray($sourceFile);
                $targetPath = base_path("lang/{$locale}/{$file}");
                $target = $this->loadArray($targetPath);

                // Keys needing translation (missing or empty), unless --force.
                $todo = [];
                foreach ($en as $key => $val) {
                    if (! is_string($val)) {
                        continue;
                    }
                    $existing = $target[$key] ?? null;
                    if ($force || $existing === null || $existing === '') {
                        $todo[$key] = $val;
                    }
                }

                if (empty($todo)) {
                    // Keep stable order even when nothing changes.
                    $this->writeArray($targetPath, $this->ordered($en, $target));

                    continue;
                }

                if ($passthrough) {
                    foreach ($todo as $key => $val) {
                        $target[$key] = $val;
                        $passthroughKeys[] = $key;
                    }
                } else {
                    foreach (array_chunk($todo, self::BATCH_SIZE, true) as $batch) {
                        $estTokens = $this->estimateTokens($batch);
                        if (($spend + $estTokens * self::COST_PER_TOKEN) > $costCap) {
                            $this->warn(sprintf(
                                'Cost cap $%.2f reached (spent ~$%.4f). Stopping before next batch.',
                                $costCap,
                                $spend
                            ));
                            break 3;
                        }

                        $translated = $this->translateBatch($batch, $locale, $apiKey);
                        $usedTokens = $this->estimateTokens($batch) + $this->estimateTokens($translated);
                        $spend += $usedTokens * self::COST_PER_TOKEN;

                        foreach ($translated as $key => $val) {
                            $target[$key] = $val;
                            $machineKeys[] = $key;
                        }
                    }
                }

                $this->writeArray($targetPath, $this->ordered($en, $target));
            }

            $needsReview = in_array($locale, I18n::RTL_LANGUAGES, true);
            $meta = [
                'machine_translated' => array_values(array_unique($machineKeys)),
                'translated_at' => now()->toIso8601String(),
                'needs_human_review' => $needsReview || ! empty($passthroughKeys),
            ];
            if (! empty($passthroughKeys)) {
                $meta['untranslated_passthrough'] = array_values(array_unique($passthroughKeys));
            }
            file_put_contents(
                "{$metaDir}/{$locale}.json",
                json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n"
            );

            $summary[$locale] = [
                'machine' => count($meta['machine_translated']),
                'passthrough' => count($passthroughKeys),
                'review' => $needsReview,
            ];
        }

        $this->newLine();
        $this->info(sprintf('Estimated spend: ~$%.4f (cap $%.2f)', $spend, $costCap));
        $this->newLine();
        $this->line('=== HUMAN REVIEW TODO ===');
        foreach ($summary as $locale => $s) {
            $flag = $s['review'] ? '  ⚠ RTL — HUMAN REVIEW REQUIRED' : '';
            $this->line(sprintf(
                ' %s: %d machine-translated, %d passthrough%s',
                $locale,
                $s['machine'],
                $s['passthrough'],
                $flag
            ));
        }
        if (isset($summary['ur']) || isset($summary['ar'])) {
            $this->warn(' ur + ar are RTL and flagged needs_human_review=true.');
        }

        return self::SUCCESS;
    }

    /** @return array<string,mixed> */
    private function loadArray(string $path): array
    {
        if (! is_file($path)) {
            return [];
        }
        $data = require $path;

        return is_array($data) ? $data : [];
    }

    /**
     * Return target ordered to match the English key order, preserving
     * any extra target-only keys at the end.
     *
     * @param  array<string,mixed>  $en
     * @param  array<string,mixed>  $target
     * @return array<string,mixed>
     */
    private function ordered(array $en, array $target): array
    {
        $out = [];
        foreach (array_keys($en) as $key) {
            if (array_key_exists($key, $target)) {
                $out[$key] = $target[$key];
            }
        }
        foreach ($target as $key => $val) {
            if (! array_key_exists($key, $out)) {
                $out[$key] = $val;
            }
        }

        return $out;
    }

    /** @param array<string,mixed> $data */
    private function writeArray(string $path, array $data): void
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $export = var_export($data, true);
        $php = "<?php\n\nreturn ".$export.";\n";
        file_put_contents($path, $php);
    }

    /** @param array<string,string> $batch */
    private function estimateTokens(array $batch): int
    {
        // ~4 chars per token rough heuristic.
        return (int) ceil(strlen(json_encode($batch, JSON_UNESCAPED_UNICODE)) / 4);
    }

    /**
     * Translate one batch of key=>value pairs via the Anthropic API.
     *
     * @param  array<string,string>  $batch
     * @return array<string,string>
     */
    private function translateBatch(array $batch, string $locale, string $apiKey): array
    {
        $langName = I18n::SUPPORTED_LANGUAGES[$locale] ?? $locale;
        $payload = json_encode($batch, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
You are a professional UI string translator. Translate the VALUES of this
JSON object from English into {$langName} ({$locale}). Rules:
- Keep every JSON KEY exactly as-is.
- Preserve any :placeholder tokens (e.g. :name, :count) verbatim.
- Preserve HTML/markup if present.
- Return ONLY the translated JSON object, no commentary, no code fences.

JSON:
{$payload}
PROMPT;

        try {
            $response = Http::withToken($apiKey)
                ->timeout(120)
                ->post(self::ENDPOINT, [
                    'model' => self::MODEL,
                    'temperature' => 0.1,
                    'max_tokens' => 4096,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a professional UI string translator. Reply with a single JSON object only.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if (! $response->successful()) {
                $this->warn("Groq API error ({$response->status()}) for {$locale}; keeping English for this batch.");

                return $batch;
            }

            $text = (string) $response->json('choices.0.message.content', '');
            $text = trim(preg_replace('/^```(?:json)?|```$/m', '', $text));
            $decoded = json_decode($text, true);

            if (! is_array($decoded)) {
                $this->warn("Could not parse translation JSON for {$locale}; keeping English for this batch.");

                return $batch;
            }

            // Only accept keys that exist in the batch.
            $out = [];
            foreach ($batch as $key => $orig) {
                $out[$key] = isset($decoded[$key]) && is_string($decoded[$key]) && $decoded[$key] !== ''
                    ? $decoded[$key]
                    : $orig;
            }

            return $out;
        } catch (\Throwable $e) {
            $this->warn("Translation request failed for {$locale}: {$e->getMessage()} — keeping English.");

            return $batch;
        }
    }
}

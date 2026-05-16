<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\ActivityStep;
use App\Models\ActivityTranslation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Phase 3 — bulk content generator from CSV.
 *
 * Usage:
 *   php artisan content:generate database/seed-data/toddler-activities.csv
 *   php artisan content:generate database/seed-data/cultural-japanese.csv --dry-run
 *
 * Expected CSV columns (header row required, in this order or any order — header
 * names drive the mapping):
 *
 *   Required:
 *     title, description, age_min, age_max, subject, activity_type, duration_minutes
 *   Strongly recommended:
 *     emoji, benefit_explanation, instructions_for_parent, language (default 'en')
 *   Optional JSON-encoded arrays (use literal `[]` for empty):
 *     learning_objectives, materials_needed, safety_warnings, skills_improved
 *   Optional steps (up to 8): step_1_title, step_1_instruction, …, step_8_title, step_8_instruction
 *   Optional translations: per-locale columns like  ar_title, ar_description, fr_title, …
 *
 * Activities with `title + age_min + subject` already present are SKIPPED
 * (idempotent re-runs). Use `--force` to overwrite.
 */
class ContentGenerateCommand extends Command
{
    protected $signature = 'content:generate
                            {csv : Path to the CSV file (relative to project root or absolute)}
                            {--dry-run : Parse + validate but do not write to the DB}
                            {--force : Overwrite existing activities matched by (title, age_min, subject)}';

    protected $description = 'Bulk-import activities from a CSV. Idempotent, with step + translation support.';

    private const SUPPORTED_LOCALES = ['en', 'fr', 'ru', 'zh', 'es', 'ko', 'ur', 'ar'];

    public function handle(): int
    {
        $path = $this->argument('csv');
        if (! is_file($path)) {
            $path = base_path($path);
        }
        if (! is_file($path)) {
            $this->error("CSV not found: {$path}");
            return self::FAILURE;
        }

        $rows = $this->readCsv($path);
        $headers = array_keys($rows[0] ?? []);
        $this->info(sprintf("Loaded %d rows from %s", count($rows), $path));
        $this->line('Headers: ' . implode(', ', $headers));

        $required = ['title', 'description', 'age_min', 'age_max', 'subject', 'activity_type', 'duration_minutes'];
        foreach ($required as $col) {
            if (! in_array($col, $headers, true)) {
                $this->error("CSV missing required column: {$col}");
                return self::FAILURE;
            }
        }

        $created = 0; $updated = 0; $skipped = 0; $errors = 0;

        foreach ($rows as $i => $row) {
            try {
                $this->processRow($row, $created, $updated, $skipped);
            } catch (\Throwable $e) {
                $errors++;
                $this->warn(sprintf("Row %d (%s): %s", $i + 2, $row['title'] ?? '(no title)', $e->getMessage()));
            }
        }

        $this->newLine();
        $this->info(sprintf(
            '%s: %d created, %d updated, %d skipped, %d errors',
            $this->option('dry-run') ? 'Dry-run' : 'Done',
            $created, $updated, $skipped, $errors
        ));

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function readCsv(string $path): array
    {
        $rows = [];
        $fh = fopen($path, 'r');
        if (! $fh) {
            throw new \RuntimeException("Cannot open CSV: {$path}");
        }
        $headers = fgetcsv($fh, escape: '\\');
        if (! $headers) {
            fclose($fh);
            throw new \RuntimeException("Empty CSV: {$path}");
        }
        $headers = array_map(fn ($h) => trim($h, "\"'\xEF\xBB\xBF \t"), $headers);
        while (($cells = fgetcsv($fh, escape: '\\')) !== false) {
            if (count($cells) === 1 && trim((string) $cells[0]) === '') continue;
            $row = [];
            foreach ($headers as $i => $h) {
                $row[$h] = $cells[$i] ?? null;
            }
            $rows[] = $row;
        }
        fclose($fh);
        return $rows;
    }

    private function processRow(array $row, int &$created, int &$updated, int &$skipped): void
    {
        $payload = [
            'title'                   => $this->str($row, 'title'),
            'description'             => $this->str($row, 'description'),
            'age_min'                 => (int) $row['age_min'],
            'age_max'                 => (int) $row['age_max'],
            'subject'                 => $this->str($row, 'subject'),
            'language'                => $this->str($row, 'language') ?: 'en',
            'activity_type'           => $this->str($row, 'activity_type'),
            'emoji'                   => $this->str($row, 'emoji') ?: '🌟',
            'duration_minutes'        => (int) ($row['duration_minutes'] ?? 10),
            'is_free'                 => filter_var($row['is_free'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'benefit_explanation'     => $this->str($row, 'benefit_explanation'),
            'instructions_for_parent' => $this->str($row, 'instructions_for_parent'),
            'thumbnail_url'           => $this->str($row, 'thumbnail_url') ?: null,
            'learning_objectives'     => $this->jsonArray($row, 'learning_objectives'),
            'materials_needed'        => $this->jsonArray($row, 'materials_needed'),
            'safety_warnings'         => $this->jsonArray($row, 'safety_warnings'),
            'skills_improved'         => $this->jsonArray($row, 'skills_improved'),
        ];

        if ($payload['title'] === '' || $payload['subject'] === '' || $payload['activity_type'] === '') {
            throw new \InvalidArgumentException('title/subject/activity_type must be non-empty.');
        }

        if ($this->option('dry-run')) {
            $created++;
            return;
        }

        DB::transaction(function () use ($row, $payload, &$created, &$updated, &$skipped) {
            $existing = Activity::query()
                ->where('title', $payload['title'])
                ->where('age_min', $payload['age_min'])
                ->where('subject', $payload['subject'])
                ->first();

            if ($existing && ! $this->option('force')) {
                $skipped++;
                return;
            }

            $activity = $existing
                ? tap($existing)->update($payload)
                : Activity::create($payload);

            $existing ? $updated++ : $created++;

            // Steps
            for ($n = 1; $n <= 8; $n++) {
                $title = $this->str($row, "step_{$n}_title");
                $instr = $this->str($row, "step_{$n}_instruction");
                if ($title === '' && $instr === '') continue;
                ActivityStep::updateOrCreate(
                    ['activity_id' => $activity->id, 'step_number' => $n],
                    ['title' => $title ?: "Step {$n}", 'instruction' => $instr]
                );
            }

            // Per-locale translations: column names like `ar_title`, `fr_description`.
            foreach (self::SUPPORTED_LOCALES as $locale) {
                if ($locale === 'en') continue;
                foreach (['title', 'description', 'instructions_for_parent', 'benefit_explanation'] as $field) {
                    $key = "{$locale}_{$field}";
                    $val = $this->str($row, $key);
                    if ($val === '') continue;
                    ActivityTranslation::updateOrCreate(
                        ['activity_id' => $activity->id, 'locale' => $locale, 'field' => $field],
                        ['value' => $val]
                    );
                }
            }
        });
    }

    private function str(array $row, string $key): string
    {
        return trim((string) ($row[$key] ?? ''));
    }

    private function jsonArray(array $row, string $key): array
    {
        $raw = $this->str($row, $key);
        if ($raw === '') return [];
        // Accept JSON arrays or pipe-separated strings ("a|b|c").
        if (str_starts_with($raw, '[')) {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }
        return array_values(array_filter(array_map('trim', explode('|', $raw))));
    }
}

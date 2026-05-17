<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;

/**
 * Phase 10 — bundle the accreditation pack as a zip suitable for
 * Cognia / BAC submission.
 *
 *   php artisan accreditation:build [--out=path/to/file.zip]
 *
 * Includes every markdown doc under docs/accreditation/, plus a
 * generated `learning-outcomes.csv` derived from the seeded curriculum,
 * plus a manifest listing all files and their last-modified timestamps.
 * PDF conversion is deferred (Phase 10.1 will add dompdf/pandoc).
 */
class AccreditationBuild extends Command
{
    protected $signature = 'accreditation:build {--out=}';

    protected $description = 'Build the Cognia / BAC accreditation submission zip';

    public function handle(): int
    {
        $sourceDir = base_path('docs/accreditation');
        if (! is_dir($sourceDir)) {
            $this->error("Missing source dir: {$sourceDir}");

            return self::FAILURE;
        }

        $ts = date('Y-m-d_His');
        $out = (string) ($this->option('out') ?? storage_path("app/accreditation/noblenest-accreditation-{$ts}.zip"));
        $outDir = dirname($out);
        if (! is_dir($outDir)) {
            mkdir($outDir, 0775, true);
        }

        // Generate learning-outcomes.csv on demand from the seeded curriculum.
        $csvPath = "{$sourceDir}/learning-outcomes.generated.csv";
        $this->writeLearningOutcomesCsv($csvPath);

        $zip = new ZipArchive();
        if ($zip->open($out, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error("Cannot open {$out} for writing.");

            return self::FAILURE;
        }

        $manifest = [];
        $files = glob("{$sourceDir}/*.md") ?: [];
        foreach ($files as $f) {
            $rel = basename($f);
            $zip->addFile($f, $rel);
            $manifest[] = ['file' => $rel, 'modified' => date('c', filemtime($f) ?: time())];
        }
        if (is_file($csvPath)) {
            $zip->addFile($csvPath, 'learning-outcomes.generated.csv');
            $manifest[] = ['file' => 'learning-outcomes.generated.csv', 'modified' => date('c')];
        }

        $manifestJson = json_encode([
            'product'   => 'Noble Nest Academy',
            'version'   => 'v1',
            'built_at'  => date('c'),
            'submission_for' => ['Cognia', 'BAC'],
            'files'     => $manifest,
            'notes'     => 'PDF conversion deferred to Phase 10.1; reviewers can read markdown directly or convert via pandoc.',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $zip->addFromString('MANIFEST.json', $manifestJson);

        $zip->close();

        $size = is_file($out) ? filesize($out) : 0;
        $this->info("Built accreditation zip: {$out} ({$size} bytes, " . count($manifest) . ' docs)');

        return self::SUCCESS;
    }

    private function writeLearningOutcomesCsv(string $path): void
    {
        $rows = [['age_tier', 'cognitive_domain', 'outcome', 'mastery_criterion']];
        // Static MVP seed — replace with DB read once outcome rubrics are normalized.
        $tiers = [
            'baby (0-23m)'   => ['sensory', 'language', 'motor'],
            'toddler (2-3y)' => ['language', 'numeracy', 'motor'],
            'preschool (4-5y)' => ['literacy', 'numeracy', 'arts', 'social'],
            'school (6-10y)' => ['literacy', 'math', 'science', 'stem_coding', 'arts'],
        ];
        foreach ($tiers as $tier => $domains) {
            foreach ($domains as $d) {
                $rows[] = [$tier, $d, "Demonstrate age-appropriate competence in {$d}", 'EMA score ≥ 0.80'];
            }
        }
        $fp = fopen($path, 'w');
        foreach ($rows as $r) {
            fputcsv($fp, $r);
        }
        fclose($fp);
    }
}

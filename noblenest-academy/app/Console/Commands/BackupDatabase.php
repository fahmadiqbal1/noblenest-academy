<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

/**
 * Phase 9 — daily database backup.
 *
 *   php artisan backup:db [--retention=30]
 *
 * Streams mysqldump → gzip → storage/app/backups/db-YYYY-MM-DD_HHmmss.sql.gz.
 * Prunes files older than --retention days. S3 upload is stubbed pending
 * Phase 12 (deploy wires the Hostinger Object Storage bucket).
 */
class BackupDatabase extends Command
{
    protected $signature = 'backup:db {--retention=30 : days to keep}';

    protected $description = 'Dump the configured MySQL database (gzipped) and prune old backups';

    public function handle(): int
    {
        $retention = (int) $this->option('retention');
        $dir = storage_path('app/backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $conn = (array) config('database.connections.' . config('database.default'));
        if (($conn['driver'] ?? '') !== 'mysql') {
            $this->warn('backup:db: non-mysql driver, skipping.');

            return self::SUCCESS;
        }

        $ts = Carbon::now()->format('Y-m-d_His');
        $file = "{$dir}/db-{$ts}.sql.gz";
        $cmd = sprintf(
            "mysqldump -h%s -P%s -u%s -p%s %s | gzip > %s",
            escapeshellarg((string) ($conn['host'] ?? '127.0.0.1')),
            escapeshellarg((string) ($conn['port'] ?? 3306)),
            escapeshellarg((string) ($conn['username'] ?? '')),
            escapeshellarg((string) ($conn['password'] ?? '')),
            escapeshellarg((string) ($conn['database'] ?? '')),
            escapeshellarg($file)
        );

        $result = Process::run($cmd);
        if (! $result->successful()) {
            $this->error('mysqldump failed: ' . $result->errorOutput());

            return self::FAILURE;
        }

        $size = is_file($file) ? filesize($file) : 0;
        $this->info("Backup written: {$file} ({$size} bytes)");

        // Phase 12 — wire Hostinger Object Storage / Cloudflare R2 upload here.
        $this->line('S3 upload deferred to Phase 12 deploy.');

        $cutoff = Carbon::now()->subDays($retention);
        $pruned = 0;
        foreach (glob("{$dir}/db-*.sql.gz") ?: [] as $old) {
            if (Carbon::createFromTimestamp(filemtime($old))->lt($cutoff)) {
                @unlink($old);
                $pruned++;
            }
        }
        $this->info("Pruned {$pruned} backup(s) older than {$retention} days.");

        return self::SUCCESS;
    }
}

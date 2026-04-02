<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class StressAuditCommand extends Command
{
    protected $signature   = 'stress:audit {--json : Output as JSON}';
    protected $description = 'Audit the application stack for 5 000-user readiness';

    private array $results = [];
    private int   $score   = 0;
    private int   $maxScore = 0;

    public function handle(): int
    {
        $this->info('');
        $this->info('  Noble Nest Academy — 5 000-User Readiness Audit');
        $this->info('  ================================================');
        $this->info('');

        $this->checkPHP();
        $this->checkOPcache();
        $this->checkDatabase();
        $this->checkSessionDriver();
        $this->checkCacheDriver();
        $this->checkQueueDriver();
        $this->checkAppEnv();
        $this->checkRouteCache();
        $this->checkConfigCache();
        $this->checkViewCache();
        $this->checkWebServer();
        $this->checkDBPooling();

        $this->printSummary();

        if ($this->option('json')) {
            $this->line(json_encode($this->results, JSON_PRETTY_PRINT));
        }

        return $this->score < ($this->maxScore * 0.7) ? self::FAILURE : self::SUCCESS;
    }

    // ------------------------------------------------------------------

    private function checkPHP(): void
    {
        $version = PHP_VERSION;
        $ok      = version_compare($version, '8.2', '>=');
        $this->record(
            'PHP Version',
            $ok ? 'pass' : 'warn',
            $ok ? "PHP {$version} ✓" : "PHP {$version} — upgrade to 8.2+ for JIT performance gains",
            recommendation: 'Enable PHP 8.2+ with OPcache + JIT for ~30% throughput improvement.'
        );
    }

    private function checkOPcache(): void
    {
        $enabled = function_exists('opcache_get_status') && opcache_get_status() !== false;
        $status  = $enabled ? opcache_get_status() : [];
        $jit     = $enabled && ($status['jit']['enabled'] ?? false);

        $this->record(
            'OPcache',
            $enabled ? 'pass' : 'fail',
            $enabled ? ('OPcache enabled' . ($jit ? ' + JIT' : ' (JIT off)')) : 'OPcache DISABLED',
            recommendation: 'Add to php.ini: opcache.enable=1, opcache.jit_buffer_size=64M, opcache.jit=1255'
        );
    }

    private function checkDatabase(): void
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $ms = round((microtime(true) - $start) * 1000, 1);

            $maxConns = DB::scalar("SHOW VARIABLES LIKE 'max_connections'") ??
                        collect(DB::select("SHOW VARIABLES LIKE 'max_connections'"))->first()?->Value ?? '?';

            $status = $ms < 5 ? 'pass' : ($ms < 20 ? 'warn' : 'fail');
            $this->record(
                'Database',
                $status,
                "Round-trip {$ms} ms — max_connections={$maxConns}",
                recommendation: 'Set max_connections ≥ 500 in my.cnf. Use PgBouncer/ProxySQL for connection pooling at scale.'
            );
        } catch (\Throwable $e) {
            $this->record('Database', 'fail', 'Cannot connect: ' . $e->getMessage());
        }
    }

    private function checkSessionDriver(): void
    {
        $driver = config('session.driver');
        $ok     = in_array($driver, ['redis', 'memcached'], true);
        $this->record(
            'Session Driver',
            $ok ? 'pass' : 'fail',
            "SESSION_DRIVER={$driver}",
            recommendation: "Switch to SESSION_DRIVER=redis. File/database sessions lock under concurrent load and don't scale horizontally."
        );
    }

    private function checkCacheDriver(): void
    {
        $store = config('cache.default');
        $ok    = in_array($store, ['redis', 'memcached'], true);
        $this->record(
            'Cache Store',
            $ok ? 'pass' : 'warn',
            "CACHE_STORE={$store}",
            recommendation: "Switch to CACHE_STORE=redis. File/database caches are serialised — Redis handles 100K+ ops/sec."
        );
    }

    private function checkQueueDriver(): void
    {
        $driver = config('queue.default');
        $ok     = in_array($driver, ['redis', 'sqs', 'beanstalkd'], true);
        $this->record(
            'Queue Driver',
            $ok ? 'pass' : ($driver === 'sync' ? 'fail' : 'warn'),
            "QUEUE_CONNECTION={$driver}",
            recommendation: "Use QUEUE_CONNECTION=redis with Horizon. 'sync' blocks PHP processes — devastating under load."
        );
    }

    private function checkAppEnv(): void
    {
        $env   = config('app.env');
        $debug = config('app.debug');
        $ok    = $env === 'production' && ! $debug;
        $this->record(
            'App Environment',
            $ok ? 'pass' : 'fail',
            "APP_ENV={$env}, APP_DEBUG=" . ($debug ? 'true' : 'false'),
            recommendation: "Set APP_ENV=production, APP_DEBUG=false. Debug mode serialises every exception — ~50 % slower."
        );
    }

    private function checkRouteCache(): void
    {
        $cached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
        $this->record(
            'Route Cache',
            $cached ? 'pass' : 'warn',
            $cached ? 'Routes cached ✓' : 'Routes NOT cached',
            recommendation: "Run 'php artisan route:cache'. Adds ~5–15 ms per request when not cached."
        );
    }

    private function checkConfigCache(): void
    {
        $cached = file_exists(base_path('bootstrap/cache/config.php'));
        $this->record(
            'Config Cache',
            $cached ? 'pass' : 'warn',
            $cached ? 'Config cached ✓' : 'Config NOT cached',
            recommendation: "Run 'php artisan config:cache'. Removes env() calls on every request."
        );
    }

    private function checkViewCache(): void
    {
        $files = glob(storage_path('framework/views/*.php'));
        $count = count($files ?: []);
        $ok    = $count > 0;
        $this->record(
            'View Cache',
            $ok ? 'pass' : 'warn',
            $ok ? "{$count} compiled views" : 'No compiled views — first-request compilation spike',
            recommendation: "Run 'php artisan view:cache' during deployment. Cold compilation adds 50–300 ms per unique view."
        );
    }

    private function checkWebServer(): void
    {
        $server = $_SERVER['SERVER_SOFTWARE'] ?? (php_sapi_name() === 'cli' ? 'CLI (artisan serve)' : 'Unknown');
        $isNginx    = stripos($server, 'nginx') !== false;
        $isApache   = stripos($server, 'apache') !== false;
        $isCli      = stripos($server, 'CLI') !== false;

        if ($isCli) {
            $this->record(
                'Web Server',
                'fail',
                "Running via 'php artisan serve' — single-threaded",
                recommendation: "Use Nginx + PHP-FPM (pm=dynamic, max_children=200+) or FrankenPHP. artisan serve is limited to ~10 req/s."
            );
        } elseif ($isNginx) {
            $this->record('Web Server', 'pass', "Nginx ✓");
        } elseif ($isApache) {
            $this->record(
                'Web Server',
                'warn',
                'Apache — acceptable but not optimal',
                recommendation: "Prefer Nginx + PHP-FPM for 5 000-user loads. Ensure mpm_event is enabled (not prefork)."
            );
        } else {
            $this->record('Web Server', 'warn', $server);
        }
    }

    private function checkDBPooling(): void
    {
        try {
            $activeConns = collect(DB::select("SHOW STATUS LIKE 'Threads_connected'"))->first()?->Value ?? 0;
            $maxConns    = collect(DB::select("SHOW VARIABLES LIKE 'max_connections'"))->first()?->Value ?? 151;
            $pct         = $maxConns > 0 ? round(($activeConns / $maxConns) * 100) : 0;
            $status      = $pct < 50 ? 'pass' : ($pct < 80 ? 'warn' : 'fail');
            $this->record(
                'DB Connections',
                $status,
                "Active: {$activeConns} / {$maxConns} ({$pct} %)",
                recommendation: 'Under 5 000 users, expect 200–500 simultaneous DB connections. Raise max_connections and use connection pooling (ProxySQL).'
            );
        } catch (\Throwable) {
            $this->record('DB Connections', 'warn', 'Could not query connection stats');
        }
    }

    // ------------------------------------------------------------------

    private function record(string $check, string $status, string $detail, string $recommendation = ''): void
    {
        $this->maxScore += 2;
        $points = match($status) { 'pass' => 2, 'warn' => 1, 'fail' => 0, default => 0 };
        $this->score += $points;

        $icon  = match($status) { 'pass' => '✅', 'warn' => '⚠️ ', 'fail' => '❌', default => '❓' };
        $label = str_pad($check, 22);

        $this->line("  {$icon}  {$label}  {$detail}");
        if ($recommendation && $status !== 'pass') {
            $this->line("       <fg=gray>→ {$recommendation}</>");
        }

        $this->results[$check] = compact('status', 'detail', 'recommendation');
    }

    private function printSummary(): void
    {
        $pct    = $this->maxScore > 0 ? round(($this->score / $this->maxScore) * 100) : 0;
        $grade  = match(true) {
            $pct >= 90 => ['🟢 PRODUCTION READY',  'green'],
            $pct >= 70 => ['🟡 NEEDS MINOR FIXES',  'yellow'],
            $pct >= 50 => ['🟠 NOT READY — FIX NOW', 'yellow'],
            default    => ['🔴 CRITICAL — DO NOT DEPLOY', 'red'],
        };

        $this->info('');
        $this->info("  ─────────────────────────────────────────────");
        $this->line("  Score: <fg={$grade[1]}>{$this->score} / {$this->maxScore} ({$pct} %)  {$grade[0]}</>");
        $this->info("  ─────────────────────────────────────────────");
        $this->info('');
        $this->info('  Run load tests:');
        $this->info('    cd tests/load && npm install');
        $this->info('    BASE_URL=http://your-server npm run test:smoke    # quick sanity');
        $this->info('    BASE_URL=http://your-server npm run test:load     # 500 users');
        $this->info('    BASE_URL=http://your-server npm run test:stress   # ramp to 5 000');
        $this->info('    BASE_URL=http://your-server npm run test:spike    # viral spike');
        $this->info('');
    }
}

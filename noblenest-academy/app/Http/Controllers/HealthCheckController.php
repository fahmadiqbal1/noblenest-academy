<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

/**
 * Health Check Endpoint for Production Monitoring
 *
 * Used by:
 * - Load balancers (readiness probes)
 * - Kubernetes liveness/readiness checks
 * - Deployment validation scripts
 *
 * Minimal dependencies — no session, no auth, no database ORM.
 * Response time target: <100ms
 */
class HealthCheckController extends Controller
{
    /**
     * GET /health — Returns 200 OK if all critical systems operational, 503 if degraded.
     *
     * Response structure:
     * {
     *   "status": "healthy" | "degraded" | "unhealthy",
     *   "checks": {
     *     "database": { "pass": true, "time_ms": 3.2 },
     *     "redis": { "pass": true, "time_ms": 1.1 },
     *     "configuration": { "pass": true, "issues": [] },
     *     "caches": { "pass": true, "details": {...} },
     *     ...
     *   },
     *   "timestamp": "2026-04-10T15:30:00Z",
     *   "app_version": "1.0.0"
     * }
     */
    public function check()
    {
        $startTime = microtime(true);
        $results = [];
        $criticalPassed = true;

        // 1. Database connectivity (CRITICAL)
        $dbCheck = $this->checkDatabase();
        $results['database'] = $dbCheck;
        if (!$dbCheck['pass']) {
            $criticalPassed = false;
        }

        // 2. Redis/Cache (CRITICAL)
        $cacheCheck = $this->checkCache();
        $results['cache'] = $cacheCheck;
        if (!$cacheCheck['pass']) {
            $criticalPassed = false;
        }

        // 3. Configuration (CRITICAL)
        $configCheck = $this->checkConfiguration();
        $results['configuration'] = $configCheck;
        if (!$configCheck['pass']) {
            $criticalPassed = false;
        }

        // 4. Caches status (WARNING — degraded but not critical)
        $cachesCheck = $this->checkCaches();
        $results['caches'] = $cachesCheck;

        // 5. Queue driver (WARNING)
        $queueCheck = $this->checkQueueDriver();
        $results['queue'] = $queueCheck;

        // Determine overall status
        $elapsedMs = round((microtime(true) - $startTime) * 1000, 1);
        $status = $criticalPassed ? 'healthy' : 'unhealthy';

        if ($criticalPassed && !$cachesCheck['pass']) {
            $status = 'degraded';
        }

        $response = [
            'status' => $status,
            'checks' => $results,
            'elapsed_ms' => $elapsedMs,
            'timestamp' => now()->toIso8601String(),
            'app_version' => config('app.version', 'unknown'),
        ];

        $statusCode = $status === 'healthy' ? 200 : ($status === 'degraded' ? 200 : 503);

        return response()->json($response, $statusCode)
                       ->header('Cache-Control', 'no-store, must-revalidate')
                       ->header('X-Health-Status', $status);
    }

    /**
     * GET /health/detailed — Full diagnostic (includes external API checks)
     * More expensive; use for manual troubleshooting, not load balancers
     */
    public function detailed(\Illuminate\Http\Request $request)
    {
        // Phase 9 — gate behind HEALTH_TOKEN bearer to avoid leaking diagnostics.
        $expected = (string) config('app.health_token', env('HEALTH_TOKEN', ''));
        if ($expected !== '') {
            $auth = (string) $request->header('Authorization', '');
            if (! str_starts_with($auth, 'Bearer ') || substr($auth, 7) !== $expected) {
                return response()->json(['error' => 'unauthorized'], 401);
            }
        }

        $results = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'configuration' => $this->checkConfiguration(),
            'caches' => $this->checkCaches(),
            'queue' => $this->checkQueueDriver(),
            'opcache' => $this->checkOPcache(),
            'external_apis' => $this->checkExternalAPIs(),
        ];

        return response()->json([
            'status' => 'detailed_report',
            'checks' => $results,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    // ============================================================================
    // INDIVIDUAL CHECKS
    // ============================================================================

    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $ms = round((microtime(true) - $start) * 1000, 1);

            return [
                'pass' => $ms < 20,
                'time_ms' => $ms,
                'message' => "Database OK ({$ms}ms round-trip)",
            ];
        } catch (\Throwable $e) {
            Log::error('Health check: Database failed', ['error' => $e->getMessage()]);
            return [
                'pass' => false,
                'time_ms' => null,
                'message' => 'Database connection failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function checkCache(): array
    {
        try {
            $start = microtime(true);
            $store = config('cache.default');

            if ($store === 'redis') {
                Redis::ping();
            } else {
                cache()->put('__health_check__', 'ping', 10);
                cache()->get('__health_check__');
                cache()->forget('__health_check__');
            }

            $ms = round((microtime(true) - $start) * 1000, 1);
            $ok = $store === 'redis' || $store === 'memcached';

            return [
                'pass' => true,
                'time_ms' => $ms,
                'driver' => $store,
                'message' => "{$store} cache operational",
                'recommended' => !$ok ? 'Switch to redis or memcached for production' : null,
            ];
        } catch (\Throwable $e) {
            Log::error('Health check: Cache failed', ['error' => $e->getMessage()]);
            return [
                'pass' => false,
                'message' => 'Cache unavailable',
                'error' => $e->getMessage(),
            ];
        }
    }

    private function checkConfiguration(): array
    {
        $issues = [];
        $env = config('app.env');
        $debug = config('app.debug');
        $sessionDriver = config('session.driver');
        $queueDriver = config('queue.default');

        if ($env !== 'production') {
            $issues[] = "APP_ENV={$env} (should be 'production')";
        }

        if ($debug === true) {
            $issues[] = "APP_DEBUG=true (should be false — ~50% performance loss)";
        }

        // Non-redis session/queue is a valid deployment choice (DB-backed on
        // shared hosting). It is a scaling RECOMMENDATION, not a health failure.
        $recommendations = [];
        if (! in_array($sessionDriver, ['redis', 'memcached'])) {
            $recommendations[] = "SESSION_DRIVER={$sessionDriver} (redis/memcached recommended at scale)";
        }
        if (! in_array($queueDriver, ['redis', 'sqs'])) {
            $recommendations[] = "QUEUE_CONNECTION={$queueDriver} (redis recommended at scale)";
        }

        return [
            'pass' => count($issues) === 0,
            'issues' => $issues,
            'recommendations' => $recommendations,
            'message' => count($issues) === 0 ? 'Configuration OK' : 'Configuration issues detected',
        ];
    }

    private function checkCaches(): array
    {
        $routeCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
        $configCached = file_exists(base_path('bootstrap/cache/config.php'));
        $viewFiles = glob(storage_path('framework/views/*.php')) ?: [];
        $viewsCached = count($viewFiles) > 0;

        return [
            'pass' => $routeCached && $configCached && $viewsCached,
            'routes_cached' => $routeCached,
            'config_cached' => $configCached,
            'views_compiled' => $viewsCached ? count($viewFiles) : 0,
            'message' => $routeCached && $configCached && $viewsCached
                ? 'All deployment caches OK'
                : 'Run: php artisan optimize',
        ];
    }

    private function checkQueueDriver(): array
    {
        $driver = config('queue.default');
        $isSafe = in_array($driver, ['redis', 'sqs', 'beanstalkd']);

        return [
            'pass' => $isSafe,
            'driver' => $driver,
            'message' => $isSafe
                ? "Queue driver '{$driver}' OK"
                : "QUEUE_CONNECTION={$driver} (sync blocks — avoid in production)",
        ];
    }

    private function checkOPcache(): array
    {
        $enabled = function_exists('opcache_get_status') && opcache_get_status() !== false;
        $status = $enabled ? opcache_get_status() : [];
        $jit = $enabled && ($status['jit']['enabled'] ?? false);

        return [
            'pass' => $enabled,
            'enabled' => $enabled,
            'jit_enabled' => $jit,
            'message' => $enabled
                ? ('OPcache ' . ($jit ? 'enabled with JIT' : 'enabled (JIT off)'))
                : 'OPcache DISABLED — enable for 20-30% throughput gain',
        ];
    }

    private function checkExternalAPIs(): array
    {
        $results = [];

        // Test AI providers (sample — don't actually call the APIs in health check)
        $aiProviders = config('services.ai_providers', []);
        if (!empty($aiProviders)) {
            $results['ai_providers_configured'] = count($aiProviders);
        }

        // Phase 6: actually ping Stripe (cheap GET — no side effects, ~150ms typical).
        $results['stripe'] = $this->checkStripe();

        return [
            'pass' => ($results['stripe']['pass'] ?? true) !== false,
            'details' => $results,
            'message' => 'External API credentials present',
        ];
    }

    /**
     * Phase 6 — live Stripe connectivity probe.
     *
     * Issues a single Account::retrieve() against the configured secret key.
     * That's a cheap call that exercises the network path + auth without
     * creating any side effects. Failure shape carries the exception class
     * so the dashboard can distinguish "wrong key" from "rate-limited" from
     * "Stripe down".
     */
    private function checkStripe(): array
    {
        if (! class_exists(\Stripe\Stripe::class)) {
            return ['pass' => null, 'configured' => false, 'message' => 'Stripe SDK not installed'];
        }
        $secret = config('services.stripe.secret');
        if (empty($secret)) {
            return ['pass' => null, 'configured' => false, 'message' => 'STRIPE_SECRET_KEY not set'];
        }
        try {
            $start = microtime(true);
            \Stripe\Stripe::setApiKey($secret);
            $account = \Stripe\Account::retrieve();
            $ms = round((microtime(true) - $start) * 1000, 1);

            return [
                'pass'       => true,
                'configured' => true,
                'time_ms'    => $ms,
                'account_id' => $account->id ?? null,
                'message'    => "Stripe reachable ({$ms}ms)",
            ];
        } catch (\Stripe\Exception\AuthenticationException $e) {
            return ['pass' => false, 'configured' => true, 'error' => 'AuthenticationException', 'message' => 'Stripe key invalid'];
        } catch (\Stripe\Exception\RateLimitException $e) {
            return ['pass' => false, 'configured' => true, 'error' => 'RateLimitException', 'message' => 'Stripe rate-limited'];
        } catch (\Throwable $e) {
            Log::warning('Stripe health probe failed', ['error' => $e->getMessage()]);
            return ['pass' => false, 'configured' => true, 'error' => class_basename($e), 'message' => substr($e->getMessage(), 0, 200)];
        }
    }
}

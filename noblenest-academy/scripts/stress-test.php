<?php

/**
 * Noble Nest Academy — Stress Test Script
 *
 * Simulates concurrent users across key endpoints.
 * Uses non-blocking stream sockets for parallelism (no curl extension needed).
 *
 * Usage: php scripts/stress-test.php [base_url] [total_requests] [concurrency]
 */
$baseUrl = $argv[1] ?? 'http://127.0.0.1:8000';
$totalReqs = (int) ($argv[2] ?? 10000);
$concurrency = (int) ($argv[3] ?? 200);

$parsed = parse_url($baseUrl);
$host = $parsed['host'] ?? '127.0.0.1';
$port = $parsed['port'] ?? 80;

// ── Endpoints to hit (weighted by real-world traffic mix) ──────────────
$endpoints = [
    ['method' => 'GET',  'path' => '/',                'weight' => 30, 'name' => 'Home'],
    ['method' => 'GET',  'path' => '/login',           'weight' => 15, 'name' => 'Login page'],
    ['method' => 'GET',  'path' => '/register',        'weight' => 10, 'name' => 'Register page'],
    ['method' => 'GET',  'path' => '/pricing',         'weight' => 10, 'name' => 'Pricing'],
    ['method' => 'GET',  'path' => '/activities',      'weight' => 20, 'name' => 'Activity index'],
    ['method' => 'GET',  'path' => '/marketplace',     'weight' => 10, 'name' => 'Marketplace'],
    ['method' => 'GET',  'path' => '/onboarding',      'weight' => 3,  'name' => 'Onboarding'],
];

// Build weighted pool
$weightedPool = [];
foreach ($endpoints as $ep) {
    for ($i = 0; $i < $ep['weight']; $i++) {
        $weightedPool[] = $ep;
    }
}

// ── Stats ──────────────────────────────────────────────────────────────
$stats = [
    'total_requests' => $totalReqs,
    'concurrency' => $concurrency,
    'started_at' => microtime(true),
    'status_codes' => [],
    'endpoint_times' => [],
    'errors' => 0,
    'timeouts' => 0,
    'connect_errors' => 0,
    'completed' => 0,
];

foreach ($endpoints as $ep) {
    $stats['endpoint_times'][$ep['name']] = [];
}

echo "==========================================================\n";
echo "  Noble Nest Academy -- Stress Test\n";
echo "==========================================================\n";
echo "  Target:       {$baseUrl}\n";
echo '  Total reqs:   '.number_format($totalReqs)."\n";
echo "  Concurrency:  {$concurrency} parallel connections\n";
echo '  Endpoints:    '.count($endpoints)." weighted routes\n";
echo "==========================================================\n\n";

// ── Helper: fire one request via stream_socket_client ──────────────────
function fireRequest(string $host, int $port, string $method, string $path): array
{
    $start = microtime(true);
    $errno = 0;
    $errstr = '';

    $fp = @stream_socket_client(
        "tcp://{$host}:{$port}",
        $errno,
        $errstr,
        10,
        STREAM_CLIENT_CONNECT
    );

    if (! $fp) {
        return ['error' => 'connect', 'time' => (microtime(true) - $start) * 1000, 'code' => 0];
    }

    stream_set_timeout($fp, 30);

    $request = "{$method} {$path} HTTP/1.1\r\n"
        ."Host: {$host}:{$port}\r\n"
        ."Connection: close\r\n"
        ."User-Agent: NobleNest-StressTest/1.0\r\n"
        ."Accept: text/html\r\n"
        ."\r\n";

    fwrite($fp, $request);

    // Read just the status line (we don't need the body for load testing)
    $statusLine = fgets($fp, 1024);
    $elapsed = (microtime(true) - $start) * 1000;

    // Drain enough to complete the connection
    $meta = stream_get_meta_data($fp);
    if ($meta['timed_out']) {
        fclose($fp);

        return ['error' => 'timeout', 'time' => $elapsed, 'code' => 0];
    }

    // Read remaining response to properly close connection
    stream_set_blocking($fp, false);
    while (! feof($fp)) {
        fread($fp, 8192);
    }
    fclose($fp);

    $code = 0;
    if ($statusLine && preg_match('/HTTP\/\d\.\d\s+(\d{3})/', $statusLine, $m)) {
        $code = (int) $m[1];
    }

    return ['error' => null, 'time' => $elapsed, 'code' => $code];
}

// ── Run in batches using stream sockets ────────────────────────────────
$remaining = $totalReqs;
$progressW = 50;

while ($remaining > 0) {
    $batchSize = min($concurrency, $remaining);

    // Open all connections non-blocking, then read
    $connections = [];
    for ($i = 0; $i < $batchSize; $i++) {
        $ep = $weightedPool[array_rand($weightedPool)];
        $start = microtime(true);
        $errno = 0;
        $errstr = '';

        $fp = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT | STREAM_CLIENT_ASYNC_CONNECT
        );

        if (! $fp) {
            $stats['errors']++;
            $stats['connect_errors']++;
            $stats['completed']++;

            continue;
        }

        $connections[] = [
            'fp' => $fp,
            'ep' => $ep,
            'start' => $start,
            'sent' => false,
            'done' => false,
        ];
    }

    // Send requests to all open connections
    foreach ($connections as &$conn) {
        $fp = $conn['fp'];
        stream_set_timeout($fp, 30);
        stream_set_blocking($fp, false);

        $path = $conn['ep']['path'];
        $method = $conn['ep']['method'];
        $request = "{$method} {$path} HTTP/1.1\r\nHost: {$host}:{$port}\r\nConnection: close\r\nUser-Agent: NobleNest-StressTest/1.0\r\nAccept: text/html\r\n\r\n";
        @fwrite($fp, $request);
        $conn['sent'] = true;
    }
    unset($conn);

    // Read responses
    $pending = count($connections);
    $maxWait = 30; // seconds
    $waitStart = microtime(true);

    while ($pending > 0 && (microtime(true) - $waitStart) < $maxWait) {
        $readStreams = [];
        $readIdx = [];
        foreach ($connections as $idx => &$conn) {
            if (! $conn['done'] && is_resource($conn['fp'])) {
                $readStreams[] = $conn['fp'];
                $readIdx[] = $idx;
            }
        }
        unset($conn);

        if (empty($readStreams)) {
            break;
        }

        $write = null;
        $except = null;
        $changed = @stream_select($readStreams, $write, $except, 1);

        if ($changed === false) {
            break;
        }

        foreach ($readStreams as $fp) {
            // Find which connection this is
            $connIdx = null;
            foreach ($readIdx as $ri) {
                if (isset($connections[$ri]) && $connections[$ri]['fp'] === $fp) {
                    $connIdx = $ri;
                    break;
                }
            }
            if ($connIdx === null) {
                continue;
            }

            $data = @fread($fp, 4096);
            $elapsed = (microtime(true) - $connections[$connIdx]['start']) * 1000;

            if ($data === false || $data === '' || feof($fp)) {
                // Connection completed or failed
                $ep = $connections[$connIdx]['ep'];

                if ($data === false) {
                    $stats['errors']++;
                    $stats['timeouts']++;
                } else {
                    // Try to parse status code from whatever we got
                    $stats['endpoint_times'][$ep['name']][] = $elapsed;
                }

                @fclose($fp);
                $connections[$connIdx]['done'] = true;
                $stats['completed']++;
                $pending--;

                continue;
            }

            // Parse status code from response
            if (preg_match('/HTTP\/\d\.\d\s+(\d{3})/', $data, $m)) {
                $code = (string) $m[1];
                $stats['status_codes'][$code] = ($stats['status_codes'][$code] ?? 0) + 1;
                $ep = $connections[$connIdx]['ep'];
                $stats['endpoint_times'][$ep['name']][] = $elapsed;

                // Drain and close
                while (! feof($fp)) {
                    @fread($fp, 8192);
                }
                @fclose($fp);
                $connections[$connIdx]['done'] = true;
                $stats['completed']++;
                $pending--;
            }
        }
    }

    // Clean up any remaining
    foreach ($connections as &$conn) {
        if (! $conn['done'] && is_resource($conn['fp'])) {
            @fclose($conn['fp']);
            $stats['errors']++;
            $stats['timeouts']++;
            $stats['completed']++;
        }
    }
    unset($conn);

    $remaining -= $batchSize;

    // Progress
    $pct = (($totalReqs - $remaining) / $totalReqs);
    $done = (int) ($pct * $progressW);
    $bar = str_repeat('#', $done).str_repeat('.', $progressW - $done);
    $pctS = str_pad(number_format($pct * 100, 1), 6, ' ', STR_PAD_LEFT);
    $elapsed = number_format(microtime(true) - $stats['started_at'], 1);
    echo "\r  [{$bar}] {$pctS}%  ({$stats['completed']}/{$totalReqs})  {$elapsed}s";
}

$totalTime = microtime(true) - $stats['started_at'];

echo "\n\n";
echo "==========================================================\n";
echo "  RESULTS\n";
echo "==========================================================\n\n";

$rps = $stats['completed'] / $totalTime;
echo '  Duration:          '.number_format($totalTime, 2)." seconds\n";
echo '  Requests/sec:      '.number_format($rps, 1)." RPS\n";
echo '  Completed:         '.number_format($stats['completed'])."\n";
echo '  Errors:            '.number_format($stats['errors'])."\n";
echo '  Timeouts:          '.number_format($stats['timeouts'])."\n";
echo '  Connect failures:  '.number_format($stats['connect_errors'])."\n\n";

// Status code breakdown
echo "  -- HTTP Status Codes --\n";
ksort($stats['status_codes']);
foreach ($stats['status_codes'] as $code => $count) {
    $successCount = max(1, $stats['completed'] - $stats['errors']);
    $pct = number_format(($count / $successCount) * 100, 1);
    $label = match ($code) {
        '200' => 'OK',
        '302' => 'Redirect',
        '301' => 'Moved',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '419' => 'CSRF expired',
        '429' => 'Rate Limited',
        '500' => 'Server Error',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        default => $code,
    };
    echo "    {$code} {$label}: ".number_format($count)." ({$pct}%)\n";
}

// Per-endpoint latency
echo "\n  -- Endpoint Latency (ms) --\n";
echo '    '.str_pad('Endpoint', 20).str_pad('Count', 8).str_pad('Avg', 10).str_pad('p50', 10).str_pad('p95', 10).str_pad('p99', 10)."Max\n";
echo '    '.str_repeat('-', 78)."\n";

$allTimes = [];
foreach ($stats['endpoint_times'] as $name => $times) {
    if (empty($times)) {
        continue;
    }
    sort($times);
    $allTimes = array_merge($allTimes, $times);

    $count = count($times);
    $avg = array_sum($times) / $count;
    $p50 = $times[(int) ($count * 0.50)];
    $p95 = $times[(int) ($count * 0.95)];
    $p99 = $times[min((int) ($count * 0.99), $count - 1)];
    $max = end($times);

    echo '    '.str_pad($name, 20)
        .str_pad(number_format($count), 8)
        .str_pad(number_format($avg, 1), 10)
        .str_pad(number_format($p50, 1), 10)
        .str_pad(number_format($p95, 1), 10)
        .str_pad(number_format($p99, 1), 10)
        .number_format($max, 1)."\n";
}

// Overall latency
if (! empty($allTimes)) {
    sort($allTimes);
    $total = count($allTimes);
    $avgAll = array_sum($allTimes) / $total;
    $p50All = $allTimes[(int) ($total * 0.50)];
    $p95All = $allTimes[(int) ($total * 0.95)];
    $p99All = $allTimes[min((int) ($total * 0.99), $total - 1)];
    $maxAll = end($allTimes);

    echo '    '.str_repeat('-', 78)."\n";
    echo '    '.str_pad('OVERALL', 20)
        .str_pad(number_format($total), 8)
        .str_pad(number_format($avgAll, 1), 10)
        .str_pad(number_format($p50All, 1), 10)
        .str_pad(number_format($p95All, 1), 10)
        .str_pad(number_format($p99All, 1), 10)
        .number_format($maxAll, 1)."\n";
}

echo "\n";

// Verdict
$errorRate = $stats['errors'] / max(1, $stats['completed']) * 100;
$has5xx = false;
foreach ($stats['status_codes'] as $code => $count) {
    if ((int) $code >= 500) {
        $has5xx = true;
    }
}

echo "==========================================================\n";
echo "  VERDICT\n";
echo "==========================================================\n\n";

if ($errorRate < 1 && ! $has5xx && $rps > 100) {
    echo '  [PASS] Application handled '.number_format($stats['completed'])." requests\n";
    echo '     at '.number_format($rps, 0).' RPS with '.number_format($errorRate, 2)."% error rate.\n";
    echo "     Ready for production with proper infrastructure.\n";
} elseif ($errorRate < 5 && $rps > 50) {
    echo '  [MARGINAL] '.number_format($errorRate, 1).'% error rate, '.number_format($rps, 0)." RPS.\n";
    echo "     Needs optimization before 10K concurrent users.\n";
    echo "     Check: connection pooling, query caching, worker count.\n";
} else {
    echo '  [NEEDS SCALING] '.number_format($errorRate, 1).'% error rate, '.number_format($rps, 0)." RPS.\n";
    echo "     Local dev server cannot handle this load (expected).\n";
    echo "     Required for production: load balancer, Redis sessions,\n";
    echo "     queue workers, horizontal scaling, CDN for static assets.\n";
}

echo "\n  -- Production Scaling Recommendations --\n\n";

if (isset($p95All) && $p95All > 500) {
    echo '  [!] p95 latency >'.number_format($p95All, 0)."ms -- add Redis page caching\n";
}
if ($stats['connect_errors'] > 0) {
    echo '  [!] '.$stats['connect_errors']." connection failures -- increase max_connections\n";
}
if ($stats['timeouts'] > 0) {
    echo '  [!] '.$stats['timeouts']." timeouts -- increase PHP-FPM workers / Apache threads\n";
}
if ($has5xx) {
    echo "  [!] 5xx errors detected -- check php-fpm error logs\n";
}

echo "\n  For 10K concurrent users in production:\n";
echo "  * Nginx + PHP-FPM (pm.max_children=200+)\n";
echo "  * Redis for sessions + cache (SESSION_DRIVER=redis)\n";
echo "  * MySQL connection pooling (max_connections=500+)\n";
echo "  * CDN (Cloudflare) for static assets + page cache\n";
echo "  * Horizontal: 2-4 app servers behind load balancer\n";
echo "  * OPcache enabled (opcache.memory=256, preloading)\n";
echo "  * Queue workers for AI jobs (Supervisor + Redis)\n";
echo "\n==========================================================\n";

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase 9 — assigns a per-request UUID, shares it with the log context,
 * and echoes it back as the X-Request-Id response header so callers can
 * correlate a client error with a server log line.
 *
 * Honors an incoming `X-Request-Id` header (use the caller's id if they
 * supplied one — supports edge ingress correlation).
 */
class RequestId
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = (string) $request->header('X-Request-Id', '');
        if ($id === '' || ! preg_match('/^[A-Za-z0-9_\-]{8,128}$/', $id)) {
            $id = (string) Str::uuid();
        }

        if (method_exists(Log::class, 'shareContext')) {
            Log::shareContext(['request_id' => $id]);
        } else {
            Log::withContext(['request_id' => $id]);
        }

        $response = $next($request);
        $response->headers->set('X-Request-Id', $id);

        return $response;
    }
}

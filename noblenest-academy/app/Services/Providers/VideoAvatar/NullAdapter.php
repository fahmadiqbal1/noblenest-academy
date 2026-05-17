<?php

namespace App\Services\Providers\VideoAvatar;

use App\Services\Providers\VideoAvatarProvider;
use App\Services\Providers\VideoGenerationResult;
use App\Services\Providers\VideoGenerationStatus;

/**
 * Phase 6 — deterministic, no-network avatar adapter for local dev and CI.
 *
 * Always returns a fake Completed result pointing at a local placeholder
 * URL under /storage/videos/null/. Never throws — safe to bind by default.
 */
class NullAdapter implements VideoAvatarProvider
{
    public function generate(string $script, string $locale, ?string $voiceId = null): VideoGenerationResult
    {
        // Deterministic id so repeated calls inside a single request can be debugged.
        $jobId = 'null-'.substr(sha1($locale.'|'.$script.'|'.($voiceId ?? '')), 0, 16);
        $url = '/storage/videos/null/'.$jobId.'.mp4';

        return VideoGenerationResult::completed($jobId, $url);
    }

    public function status(string $jobId): VideoGenerationStatus
    {
        return VideoGenerationStatus::Completed;
    }

    public function supports(string $locale): bool
    {
        return true;
    }
}

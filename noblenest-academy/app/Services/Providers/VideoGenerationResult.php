<?php

namespace App\Services\Providers;

/**
 * Phase 6 — DTO returned by VideoAvatarProvider::generate() and ::status().
 *
 * Immutable. `videoUrl` is null until the provider reports Completed.
 */
final class VideoGenerationResult
{
    public function __construct(
        public readonly string $jobId,
        public readonly ?string $videoUrl,
        public readonly VideoGenerationStatus $status,
        public readonly ?string $error = null,
    ) {
    }

    public static function completed(string $jobId, string $videoUrl): self
    {
        return new self($jobId, $videoUrl, VideoGenerationStatus::Completed);
    }

    public static function queued(string $jobId): self
    {
        return new self($jobId, null, VideoGenerationStatus::Queued);
    }

    public static function failed(string $jobId, string $error): self
    {
        return new self($jobId, null, VideoGenerationStatus::Failed, $error);
    }
}

<?php

namespace App\Services\Providers;

/**
 * Phase 6 — pluggable provider for avatar/talking-head video generation.
 *
 * The container resolves this to the adapter selected by
 * config('services.video_avatar.driver'). MVP ships with NullAdapter
 * by default; HeyGen and Synthesia adapters are wired and ready to use
 * once the ops team injects credentials in production.
 */
interface VideoAvatarProvider
{
    /**
     * Submit a script for video generation.
     *
     * @throws \App\Services\Providers\Exceptions\MissingProviderCredentialException
     *         when the selected real-provider adapter is missing its API key.
     */
    public function generate(string $script, string $locale, ?string $voiceId = null): VideoGenerationResult;

    /**
     * Poll a previously-submitted job.
     */
    public function status(string $jobId): VideoGenerationStatus;

    /**
     * Whether this provider can render the given locale.
     */
    public function supports(string $locale): bool;
}

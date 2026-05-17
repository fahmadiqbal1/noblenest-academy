<?php

namespace App\Services\Providers;

/**
 * Phase 6 — speech-to-text / subtitle generation contract.
 *
 * Drivers (config('services.whisper.driver')):
 *  - local   → LocalWhisperAdapter (default; fixed VTT for tests/dev)
 *  - openai  → OpenAIWhisperAdapter (real HTTP, requires WHISPER_API_KEY)
 */
interface WhisperAdapter
{
    /**
     * Transcribe a remote or local audio/video URL to a WebVTT string.
     *
     * Implementations MUST return a syntactically-valid WEBVTT document.
     *
     * @throws \App\Services\Providers\Exceptions\MissingProviderCredentialException
     */
    public function transcribeToVtt(string $mediaUrl, string $locale): string;
}

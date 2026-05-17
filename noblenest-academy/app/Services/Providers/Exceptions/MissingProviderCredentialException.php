<?php

namespace App\Services\Providers\Exceptions;

/**
 * Phase 6 — raised when a real-provider adapter (HeyGen, Synthesia,
 * OpenAI Whisper, etc.) is selected but its API key is empty.
 *
 * Callers should treat this as a soft configuration error: log it,
 * fall back to a null/stub adapter, or surface a "needs ops credentials"
 * message in admin tooling.
 */
class MissingProviderCredentialException extends \RuntimeException
{
    public static function forProvider(string $provider, string $envVar): self
    {
        return new self(sprintf(
            'Provider "%s" requires environment variable %s but it is empty.',
            $provider,
            $envVar
        ));
    }
}

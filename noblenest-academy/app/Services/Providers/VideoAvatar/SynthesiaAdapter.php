<?php

namespace App\Services\Providers\VideoAvatar;

use App\Services\Providers\Exceptions\MissingProviderCredentialException;
use App\Services\Providers\VideoAvatarProvider;
use App\Services\Providers\VideoGenerationResult;
use App\Services\Providers\VideoGenerationStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Phase 6 — Synthesia avatar adapter.
 *
 * Real HTTP scaffold against https://api.synthesia.io/v2/videos.
 * Auth is Bearer with config('services.synthesia.api_key').
 */
class SynthesiaAdapter implements VideoAvatarProvider
{
    private const ENDPOINT_GENERATE = 'https://api.synthesia.io/v2/videos';

    private const ENDPOINT_STATUS = 'https://api.synthesia.io/v2/videos/';

    private const SUPPORTED_LOCALES = ['en', 'fr', 'ru', 'zh', 'es', 'ko', 'ur', 'ar'];

    public function __construct(
        private readonly ?string $apiKey = null,
        private readonly ?string $defaultAvatarId = null,
    ) {}

    public function generate(string $script, string $locale, ?string $voiceId = null): VideoGenerationResult
    {
        $apiKey = $this->resolvedApiKey();

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(60)
            ->post(self::ENDPOINT_GENERATE, [
                'test' => false,
                'visibility' => 'private',
                'title' => 'Noble Nest Academy - '.$locale,
                'input' => [[
                    'scriptText' => $script,
                    'avatar' => $this->defaultAvatarId ?? (string) config('services.synthesia.default_avatar_id', 'anna_costume1_cameraA'),
                    'avatarSettings' => [
                        'voice' => $voiceId ?? (string) config('services.synthesia.default_voice_id', ''),
                    ],
                ]],
            ]);

        if (! $response->successful()) {
            Log::warning('SynthesiaAdapter::generate non-200', [
                'status' => $response->status(),
                'body' => substr((string) $response->body(), 0, 500),
            ]);

            return VideoGenerationResult::failed(
                'synthesia-failed-'.substr(sha1($script.$locale), 0, 12),
                'Synthesia API error: '.$response->status(),
            );
        }

        $jobId = (string) data_get($response->json(), 'id', '');
        if ($jobId === '') {
            return VideoGenerationResult::failed('synthesia-unknown', 'Missing id in Synthesia response.');
        }

        return VideoGenerationResult::queued($jobId);
    }

    public function status(string $jobId): VideoGenerationStatus
    {
        $apiKey = $this->resolvedApiKey();

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(30)
            ->get(self::ENDPOINT_STATUS.rawurlencode($jobId));

        if (! $response->successful()) {
            return VideoGenerationStatus::Failed;
        }

        $status = (string) data_get($response->json(), 'status', 'in_progress');

        return match ($status) {
            'complete', 'completed' => VideoGenerationStatus::Completed,
            'failed', 'error' => VideoGenerationStatus::Failed,
            'queued' => VideoGenerationStatus::Queued,
            default => VideoGenerationStatus::Processing,
        };
    }

    public function supports(string $locale): bool
    {
        return in_array($locale, self::SUPPORTED_LOCALES, true);
    }

    private function resolvedApiKey(): string
    {
        $key = $this->apiKey ?? (string) config('services.synthesia.api_key', '');
        if ($key === '') {
            throw MissingProviderCredentialException::forProvider('synthesia', 'SYNTHESIA_API_KEY');
        }

        return $key;
    }
}

<?php

namespace App\Services\Providers\VideoAvatar;

use App\Services\Providers\Exceptions\MissingProviderCredentialException;
use App\Services\Providers\VideoAvatarProvider;
use App\Services\Providers\VideoGenerationResult;
use App\Services\Providers\VideoGenerationStatus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Phase 6 — HeyGen avatar adapter.
 *
 * Real HTTP scaffold against https://api.heygen.com/v2/video/generate.
 * Credentials come from config('services.heygen.api_key'); when empty,
 * generate()/status() raise MissingProviderCredentialException so the
 * caller can fall back to a null/stub adapter without crashing the worker.
 */
class HeyGenAdapter implements VideoAvatarProvider
{
    private const ENDPOINT_GENERATE = 'https://api.heygen.com/v2/video/generate';

    private const ENDPOINT_STATUS = 'https://api.heygen.com/v1/video_status.get';

    /** HeyGen advertises broad locale support; restrict to our 8-locale set. */
    private const SUPPORTED_LOCALES = ['en', 'fr', 'ru', 'zh', 'es', 'ko', 'ur', 'ar'];

    public function __construct(
        private readonly ?string $apiKey = null,
        private readonly ?string $defaultAvatarId = null,
        private readonly ?string $defaultVoiceId = null,
    ) {}

    public function generate(string $script, string $locale, ?string $voiceId = null): VideoGenerationResult
    {
        $apiKey = $this->resolvedApiKey();

        $response = Http::withHeaders(['X-Api-Key' => $apiKey])
            ->acceptJson()
            ->timeout(60)
            ->post(self::ENDPOINT_GENERATE, [
                'video_inputs' => [[
                    'character' => [
                        'type' => 'avatar',
                        'avatar_id' => $this->defaultAvatarId ?? (string) config('services.heygen.default_avatar_id', ''),
                    ],
                    'voice' => [
                        'type' => 'text',
                        'input_text' => $script,
                        'voice_id' => $voiceId ?? $this->defaultVoiceId ?? (string) config('services.heygen.default_voice_id', ''),
                    ],
                ]],
                'caption' => false,
                'dimension' => ['width' => 1280, 'height' => 720],
                'locale' => $locale,
            ]);

        if (! $response->successful()) {
            Log::warning('HeyGenAdapter::generate non-200', [
                'status' => $response->status(),
                'body' => substr((string) $response->body(), 0, 500),
            ]);
            $jobId = 'heygen-failed-'.substr(sha1($script.$locale), 0, 12);

            return VideoGenerationResult::failed($jobId, 'HeyGen API error: '.$response->status());
        }

        $jobId = (string) data_get($response->json(), 'data.video_id', '');
        if ($jobId === '') {
            return VideoGenerationResult::failed('heygen-unknown', 'Missing video_id in HeyGen response.');
        }

        return VideoGenerationResult::queued($jobId);
    }

    public function status(string $jobId): VideoGenerationStatus
    {
        $apiKey = $this->resolvedApiKey();

        $response = Http::withHeaders(['X-Api-Key' => $apiKey])
            ->acceptJson()
            ->timeout(30)
            ->get(self::ENDPOINT_STATUS, ['video_id' => $jobId]);

        if (! $response->successful()) {
            return VideoGenerationStatus::Failed;
        }

        $heygenStatus = (string) data_get($response->json(), 'data.status', 'processing');

        return match ($heygenStatus) {
            'completed', 'success' => VideoGenerationStatus::Completed,
            'failed', 'error' => VideoGenerationStatus::Failed,
            'pending', 'waiting' => VideoGenerationStatus::Queued,
            default => VideoGenerationStatus::Processing,
        };
    }

    public function supports(string $locale): bool
    {
        return in_array($locale, self::SUPPORTED_LOCALES, true);
    }

    private function resolvedApiKey(): string
    {
        $key = $this->apiKey ?? (string) config('services.heygen.api_key', '');
        if ($key === '') {
            throw MissingProviderCredentialException::forProvider('heygen', 'HEYGEN_API_KEY');
        }

        return $key;
    }
}

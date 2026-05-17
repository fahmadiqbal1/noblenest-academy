<?php

namespace App\Services\Providers;

use App\Models\AIProviderConfig;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Media Generation Service — handles ElevenLabs (audio), Replicate (video), RunwayML (video)
 *
 * Extracted from AIProviderGateway (lines 539-701).
 */
class MediaGenerationService
{
    const POLL_TIMEOUT = 300;  // 5 minutes

    const POLL_INTERVAL = 5;   // Check every 5 seconds

    public function verify(AIProviderConfig $provider, string $apiKey): array
    {
        $driver = $this->resolveDriver($provider);

        return match ($driver) {
            'elevenlabs' => $this->verifyViaElevenLabs($apiKey),
            'replicate' => $this->verifyViaReplicate($apiKey),
            'runway' => $this->verifyViaRunway($apiKey),
            default => ['status' => 'unknown', 'message' => 'Unknown media provider'],
        };
    }

    public function generateAudio(AIProviderConfig $provider, string $text, array $options = []): array
    {
        if (! $provider->api_key_encrypted) {
            throw new \RuntimeException('No API key configured for this provider.');
        }

        $apiKey = Crypt::decryptString($provider->api_key_encrypted);

        try {
            return $this->generateAudioViaElevenLabs($apiKey, $text, $provider, $options);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Audio generation failed: '.$e->getMessage());
        }
    }

    public function generateVideo(AIProviderConfig $provider, string $prompt, array $options = []): array
    {
        if (! $provider->api_key_encrypted) {
            throw new \RuntimeException('No API key configured for this provider.');
        }

        $apiKey = Crypt::decryptString($provider->api_key_encrypted);
        $driver = $this->resolveDriver($provider);

        try {
            return match ($driver) {
                'runway' => $this->generateVideoViaRunway($apiKey, $prompt, $options),
                default => $this->generateVideoViaReplicate($apiKey, $provider->model, $prompt, $options),
            };
        } catch (\Throwable $e) {
            throw new \RuntimeException("Video generation failed ({$driver}): ".$e->getMessage());
        }
    }

    // ============================================================================
    // VERIFICATION
    // ============================================================================

    private function verifyViaElevenLabs(string $apiKey): array
    {
        try {
            $response = Http::withHeaders(['xi-api-key' => $apiKey])
                ->acceptJson()->timeout(15)
                ->get('https://api.elevenlabs.io/v1/user');

            if ($response->successful()) {
                $tier = data_get($response->json(), 'subscription.tier', 'free');

                return ['status' => 'live', 'message' => "ElevenLabs connected. Tier: {$tier}."];
            }

            return ['status' => 'failed', 'message' => $this->formatHttpError($response->status(), $response->body())];
        } catch (\Throwable $e) {
            return ['status' => 'failed', 'message' => Str::limit($e->getMessage(), 180)];
        }
    }

    private function verifyViaReplicate(string $apiKey): array
    {
        try {
            $response = Http::withToken($apiKey)->acceptJson()->timeout(15)
                ->get('https://api.replicate.com/v1/account');

            if ($response->successful()) {
                return ['status' => 'live', 'message' => 'Replicate connected. Account: '.$response->json('username', 'verified')];
            }

            return ['status' => 'failed', 'message' => $this->formatHttpError($response->status(), $response->body())];
        } catch (\Throwable $e) {
            return ['status' => 'failed', 'message' => Str::limit($e->getMessage(), 180)];
        }
    }

    private function verifyViaRunway(string $apiKey): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'X-Runway-Version' => '2024-11-06',
            ])
                ->acceptJson()->timeout(15)
                ->get('https://api.dev.runwayml.com/v1/organizations');

            if ($response->successful()) {
                return ['status' => 'live', 'message' => 'RunwayML connected.'];
            }

            return ['status' => 'failed', 'message' => $this->formatHttpError($response->status(), $response->body())];
        } catch (\Throwable $e) {
            return ['status' => 'failed', 'message' => Str::limit($e->getMessage(), 180)];
        }
    }

    // ============================================================================
    // AUDIO GENERATION
    // ============================================================================

    private function generateAudioViaElevenLabs(string $apiKey, string $text, AIProviderConfig $provider, array $options): array
    {
        $voiceId = $options['voice_id'] ?? data_get($provider->extra_config, 'voice_id', 'JBFqnCBsd6RMkjVDRZzb');
        $model = $options['model'] ?? $provider->model ?? 'eleven_multilingual_v2';

        try {
            $response = Http::withHeaders([
                'xi-api-key' => $apiKey,
                'Accept' => 'audio/mpeg',
                'Content-Type' => 'application/json',
            ])
                ->timeout(60)
                ->post("https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}", [
                    'text' => $text,
                    'model_id' => $model,
                    'voice_settings' => ['stability' => 0.5, 'similarity_boost' => 0.75],
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException($this->formatHttpError($response->status(), $response->body()));
            }

            Storage::disk('public')->makeDirectory('ai/tts');
            $path = 'ai/tts/'.uniqid('tts_', true).'.mp3';
            Storage::disk('public')->put($path, $response->body());

            return [
                'type' => 'audio',
                'url' => Storage::disk('public')->url($path),
                'content' => "Audio generated via ElevenLabs: {$path}",
            ];
        } catch (\Throwable $e) {
            throw new \RuntimeException('ElevenLabs TTS failed: '.$e->getMessage());
        }
    }

    // ============================================================================
    // VIDEO GENERATION
    // ============================================================================

    private function generateVideoViaReplicate(string $apiKey, ?string $model, string $prompt, array $options): array
    {
        $model = $model ?: 'minimax/video-01';

        try {
            $response = Http::withToken($apiKey)->acceptJson()->timeout(30)
                ->post("https://api.replicate.com/v1/models/{$model}/predictions", [
                    'input' => [
                        'prompt' => $prompt,
                        'num_frames' => $options['num_frames'] ?? 120,
                        'fps' => $options['fps'] ?? 24,
                    ],
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException($this->formatHttpError($response->status(), $response->body()));
            }

            $pollUrl = $response->json('urls.get');
            if (! $pollUrl) {
                throw new \RuntimeException('Replicate did not return poll URL');
            }

            $output = $this->pollReplicate($apiKey, $pollUrl);
            $url = is_array($output) ? ($output[0] ?? (string) $output) : (string) $output;

            return ['type' => 'video', 'url' => $url, 'content' => "Video generated via Replicate: {$url}"];
        } catch (\Throwable $e) {
            throw new \RuntimeException('Replicate video generation failed: '.$e->getMessage());
        }
    }

    private function pollReplicate(string $apiKey, string $pollUrl): mixed
    {
        $deadline = time() + self::POLL_TIMEOUT;

        while (time() < $deadline) {
            sleep(self::POLL_INTERVAL);

            try {
                $poll = Http::withToken($apiKey)->acceptJson()->timeout(20)->get($pollUrl);
                $status = $poll->json('status');

                if ($status === 'succeeded') {
                    return $poll->json('output');
                }

                if (in_array($status, ['failed', 'canceled'], true)) {
                    throw new \RuntimeException("Replicate prediction {$status}: ".($poll->json('error', 'Unknown error')));
                }
            } catch (\Throwable $e) {
                if (time() >= $deadline) {
                    break;
                }
            }
        }

        throw new \RuntimeException('Replicate prediction timed out after '.self::POLL_TIMEOUT.'s');
    }

    private function generateVideoViaRunway(string $apiKey, string $prompt, array $options): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'X-Runway-Version' => '2024-11-06',
            ])
                ->acceptJson()->timeout(30)
                ->post('https://api.dev.runwayml.com/v1/text_to_video', [
                    'model' => $options['model'] ?? 'gen4_turbo',
                    'promptText' => $prompt,
                    'duration' => $options['duration'] ?? 5,
                    'ratio' => $options['ratio'] ?? '1280:720',
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException($this->formatHttpError($response->status(), $response->body()));
            }

            $taskId = $response->json('id');
            if (! $taskId) {
                throw new \RuntimeException('RunwayML did not return task ID');
            }

            $url = $this->pollRunway($apiKey, $taskId);
            $url = is_array($url) ? ($url[0] ?? (string) $url) : (string) $url;

            return ['type' => 'video', 'url' => $url, 'content' => "Video generated via RunwayML: {$url}"];
        } catch (\Throwable $e) {
            throw new \RuntimeException('RunwayML video generation failed: '.$e->getMessage());
        }
    }

    private function pollRunway(string $apiKey, string $taskId): mixed
    {
        $deadline = time() + self::POLL_TIMEOUT;

        while (time() < $deadline) {
            sleep(self::POLL_INTERVAL);

            try {
                $poll = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'X-Runway-Version' => '2024-11-06',
                ])
                    ->acceptJson()->timeout(20)
                    ->get("https://api.dev.runwayml.com/v1/tasks/{$taskId}");

                $status = $poll->json('status');

                if ($status === 'SUCCEEDED') {
                    return $poll->json('output');
                }

                if (in_array($status, ['FAILED', 'CANCELED'], true)) {
                    throw new \RuntimeException("RunwayML task {$status}: ".($poll->json('failure', 'Unknown error')));
                }
            } catch (\Throwable $e) {
                if (time() >= $deadline) {
                    break;
                }
            }
        }

        throw new \RuntimeException('RunwayML task timed out after '.self::POLL_TIMEOUT.'s');
    }

    // ============================================================================
    // HELPERS
    // ============================================================================

    private function resolveDriver(AIProviderConfig $provider): string
    {
        $haystack = Str::lower(implode(' ', array_filter([
            data_get($provider->extra_config, 'driver', ''),
            $provider->slug,
            $provider->name,
        ])));

        if (Str::contains($haystack, ['elevenlabs', 'eleven_labs'])) {
            return 'elevenlabs';
        }
        if (Str::contains($haystack, ['runway', 'runwayml'])) {
            return 'runway';
        }

        return 'replicate';
    }

    private function formatHttpError(int $status, string $body): string
    {
        return sprintf('API error %d: %s', $status, Str::limit(trim($body), 180));
    }
}

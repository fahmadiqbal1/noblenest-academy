<?php

namespace App\Services\Providers\Whisper;

use App\Services\Providers\Exceptions\MissingProviderCredentialException;
use App\Services\Providers\WhisperAdapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Phase 6 — OpenAI Whisper adapter.
 *
 * Real HTTP scaffold against /v1/audio/transcriptions with response_format=vtt.
 * Requires WHISPER_API_KEY (Bearer); raises MissingProviderCredentialException
 * if empty so callers can fall back to LocalWhisperAdapter cleanly.
 */
class OpenAIWhisperAdapter implements WhisperAdapter
{
    private const ENDPOINT = 'https://api.openai.com/v1/audio/transcriptions';

    private const MODEL = 'whisper-1';

    public function __construct(private readonly ?string $apiKey = null) {}

    public function transcribeToVtt(string $mediaUrl, string $locale): string
    {
        $apiKey = $this->apiKey ?? (string) config('services.whisper.api_key', '');
        if ($apiKey === '') {
            throw MissingProviderCredentialException::forProvider('openai-whisper', 'WHISPER_API_KEY');
        }

        // The OpenAI endpoint requires a multipart upload of the audio file
        // bytes. For MVP we fetch the media first, then forward the bytes.
        try {
            $audio = Http::timeout(60)->get($mediaUrl);
            if (! $audio->successful()) {
                Log::warning('OpenAIWhisperAdapter: failed to fetch media', [
                    'url' => $mediaUrl,
                    'status' => $audio->status(),
                ]);

                return $this->fallbackVtt($locale);
            }

            $response = Http::withToken($apiKey)
                ->timeout(120)
                ->attach('file', $audio->body(), 'audio.mp4')
                ->post(self::ENDPOINT, [
                    'model' => self::MODEL,
                    'response_format' => 'vtt',
                    'language' => $locale,
                ]);

            if (! $response->successful()) {
                Log::warning('OpenAIWhisperAdapter: non-200', [
                    'status' => $response->status(),
                    'body' => substr((string) $response->body(), 0, 500),
                ]);

                return $this->fallbackVtt($locale);
            }

            $body = trim((string) $response->body());

            return str_starts_with($body, 'WEBVTT') ? $body : "WEBVTT\n\n".$body;
        } catch (\Throwable $e) {
            Log::warning('OpenAIWhisperAdapter failed', ['error' => $e->getMessage()]);

            return $this->fallbackVtt($locale);
        }
    }

    private function fallbackVtt(string $locale): string
    {
        return "WEBVTT\n\n00:00:00.000 --> 00:00:02.000\n[transcript unavailable for {$locale}]\n";
    }
}

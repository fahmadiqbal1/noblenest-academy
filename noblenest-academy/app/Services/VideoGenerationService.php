<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * VideoGenerationService
 *
 * Abstracts HeyGen (AI avatar video) and multiple TTS providers.
 * Configured via .env:
 *   HEYGEN_API_KEY=...
 *   TTS_PROVIDER=openai|elevenlabs
 *   OPENAI_TTS_API_KEY=...
 *   ELEVENLABS_API_KEY=...
 */
class VideoGenerationService
{
    private const HEYGEN_BASE = 'https://api.heygen.com';

    // ------------------------------------------------------------------
    // HeyGen — AI Avatar Video
    // ------------------------------------------------------------------

    /**
     * Submit a video generation job to HeyGen.
     * Returns the video_id string for polling, or null on failure.
     */
    public function generateHeyGenVideo(
        string $script,
        string $avatarId,
        string $voiceId,
        string $language = 'en'
    ): ?string {
        $apiKey = config('services.heygen.api_key');
        if (empty($apiKey)) {
            return null;
        }

        try {
            $response = Http::withHeaders(['X-Api-Key' => $apiKey])
                ->acceptJson()
                ->timeout(30)
                ->post(self::HEYGEN_BASE . '/v2/video/generate', [
                    'video_inputs' => [
                        [
                            'character' => [
                                'type'      => 'avatar',
                                'avatar_id' => $avatarId,
                                'avatar_style' => 'normal',
                            ],
                            'voice' => [
                                'type'     => 'text',
                                'input_text' => $script,
                                'voice_id'   => $voiceId,
                                'speed'      => 1.0,
                            ],
                        ],
                    ],
                    'dimension' => ['width' => 1280, 'height' => 720],
                    'aspect_ratio' => '16:9',
                ]);

            if ($response->successful()) {
                return $response->json('data.video_id');
            }
        } catch (\Throwable) {
            // Fall through
        }

        return null;
    }

    /**
     * Poll HeyGen for video status.
     * Returns ['status' => pending|processing|completed|failed, 'video_url' => ?string]
     */
    public function pollHeyGenVideo(string $videoId): array
    {
        $apiKey = config('services.heygen.api_key');
        if (empty($apiKey)) {
            return ['status' => 'not_configured', 'video_url' => null];
        }

        try {
            $response = Http::withHeaders(['X-Api-Key' => $apiKey])
                ->acceptJson()
                ->timeout(15)
                ->get(self::HEYGEN_BASE . "/v1/video_status.get?video_id={$videoId}");

            if ($response->successful()) {
                $data = $response->json('data', []);
                return [
                    'status'    => $data['status'] ?? 'unknown',
                    'video_url' => $data['video_url'] ?? null,
                    'thumbnail' => $data['thumbnail_url'] ?? null,
                ];
            }
        } catch (\Throwable) {
            // Fall through
        }

        return ['status' => 'error', 'video_url' => null];
    }

    // ------------------------------------------------------------------
    // TTS — Text-to-Speech
    // ------------------------------------------------------------------

    /**
     * Generate speech audio and store it locally.
     * Returns the storage path, or null on failure.
     */
    public function generateSpeech(string $text, string $language = 'en', string $voice = 'nova'): ?string
    {
        $provider = config('services.tts.provider', 'openai');

        return match ($provider) {
            'elevenlabs' => $this->ttsViaElevenLabs($text, $language),
            default      => $this->ttsViaOpenAI($text, $voice),
        };
    }

    private function ttsViaOpenAI(string $text, string $voice): ?string
    {
        $apiKey = config('services.openai_tts.api_key') ?? config('services.openai.api_key');
        if (empty($apiKey)) {
            return null;
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/audio/speech', [
                    'model' => 'tts-1',
                    'input' => Str::limit($text, 4096),
                    'voice' => $voice,
                    'response_format' => 'mp3',
                ]);

            if ($response->successful()) {
                $path = 'tts/' . Str::uuid() . '.mp3';
                Storage::disk('public')->put($path, $response->body());
                return $path;
            }
        } catch (\Throwable) {
            // Fall through
        }

        return null;
    }

    private function ttsViaElevenLabs(string $text, string $language): ?string
    {
        $apiKey = config('services.elevenlabs.api_key');
        if (empty($apiKey)) {
            return null;
        }

        // Use the multilingual v2 model
        $voiceId = config('services.elevenlabs.voice_id', 'EXAVITQu4vr4xnSDxMaL');

        try {
            $response = Http::withHeaders(['xi-api-key' => $apiKey])
                ->timeout(30)
                ->post("https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}", [
                    'text'       => Str::limit($text, 5000),
                    'model_id'   => 'eleven_multilingual_v2',
                    'voice_settings' => ['stability' => 0.5, 'similarity_boost' => 0.75],
                ]);

            if ($response->successful()) {
                $path = 'tts/' . Str::uuid() . '.mp3';
                Storage::disk('public')->put($path, $response->body());
                return $path;
            }
        } catch (\Throwable) {
            // Fall through
        }

        return null;
    }

    // ------------------------------------------------------------------
    // Translation pipeline helper
    // ------------------------------------------------------------------

    /**
     * Translate activity content using a configured LLM (via OpenAI-compatible API).
     */
    public function translateContent(string $content, string $targetLanguage): ?string
    {
        $apiKey = config('services.openai.api_key');
        if (empty($apiKey)) {
            return null;
        }

        $langNames = [
            'fr' => 'French', 'ar' => 'Arabic', 'ur' => 'Urdu',
            'es' => 'Spanish', 'zh' => 'Chinese', 'ko' => 'Korean', 'ru' => 'Russian',
        ];

        $targetName = $langNames[$targetLanguage] ?? $targetLanguage;

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => "You are a professional children's education content translator. Translate exactly into {$targetName}. Preserve any HTML/markdown structure."],
                        ['role' => 'user', 'content' => $content],
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 1000,
                ]);

            if ($response->successful()) {
                return trim((string) data_get($response->json(), 'choices.0.message.content', ''));
            }
        } catch (\Throwable) {
            // Fall through
        }

        return null;
    }
}

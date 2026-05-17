<?php

namespace App\Services\Providers;

use App\Models\AIProviderConfig;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Image Generation Service — handles Stability, OpenAI/DALL-E, Gemini
 *
 * Extracted from AIProviderGateway (lines 391-537).
 * Handles verification and image generation for 3 providers.
 */
class ImageGenerationService
{
    public function verify(AIProviderConfig $provider, string $apiKey): array
    {
        $driver = $this->resolveDriver($provider);

        return match ($driver) {
            'stability' => $this->verifyViaStability($apiKey),
            'gemini' => $this->verifyViaGemini($provider, $apiKey),
            default => $this->verifyViaOpenAI($apiKey),
        };
    }

    public function generate(AIProviderConfig $provider, string $prompt, array $options = []): array
    {
        if (! $provider->api_key_encrypted) {
            throw new \RuntimeException('No API key configured for this provider.');
        }

        $apiKey = Crypt::decryptString($provider->api_key_encrypted);
        $driver = $this->resolveDriver($provider);

        try {
            return match ($driver) {
                'stability' => $this->generateImageViaStability($apiKey, $prompt, $options),
                'gemini' => $this->generateImageViaGemini($apiKey, $provider->model, $prompt, $options),
                default => $this->generateImageViaOpenAI($apiKey, $prompt, $options),
            };
        } catch (\Throwable $e) {
            throw new \RuntimeException("Image generation failed ({$driver}): ".$e->getMessage());
        }
    }

    // ============================================================================
    // VERIFICATION
    // ============================================================================

    private function verifyViaStability(string $apiKey): array
    {
        try {
            $response = Http::withHeaders(['Authorization' => 'Bearer '.$apiKey])
                ->acceptJson()->timeout(15)
                ->get('https://api.stability.ai/v1/user/account');

            if ($response->successful()) {
                return [
                    'status' => 'live',
                    'message' => 'Stability AI connected. Account: '.($response->json('id') ?? 'OK'),
                ];
            }

            return [
                'status' => 'failed',
                'message' => $this->formatHttpError($response->status(), $response->body()),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'failed',
                'message' => Str::limit($e->getMessage(), 180),
            ];
        }
    }

    private function verifyViaOpenAI(string $apiKey): array
    {
        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(20)
                ->get('https://api.openai.com/v1/models');

            if ($response->successful()) {
                return [
                    'status' => 'live',
                    'message' => 'OpenAI (DALL-E) connected',
                ];
            }

            return [
                'status' => 'failed',
                'message' => $this->formatHttpError($response->status(), $response->body()),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'failed',
                'message' => Str::limit($e->getMessage(), 180),
            ];
        }
    }

    private function verifyViaGemini(AIProviderConfig $provider, string $apiKey): array
    {
        try {
            $model = $provider->model ?? 'gemini-2.0-flash-exp';
            $response = Http::acceptJson()
                ->timeout(20)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => 'OK']]],
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'status' => 'live',
                    'message' => 'Gemini (image generation) connected',
                ];
            }

            return [
                'status' => 'failed',
                'message' => $this->formatHttpError($response->status(), $response->body()),
            ];
        } catch (\Throwable $e) {
            return [
                'status' => 'failed',
                'message' => Str::limit($e->getMessage(), 180),
            ];
        }
    }

    // ============================================================================
    // GENERATION
    // ============================================================================

    private function generateImageViaStability(string $apiKey, string $prompt, array $options): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Accept' => 'application/json',
            ])
                ->timeout(60)
                ->asMultipart()
                ->post('https://api.stability.ai/v2beta/stable-image/generate/core', [
                    ['name' => 'prompt', 'contents' => $prompt],
                    ['name' => 'output_format', 'contents' => 'png'],
                    ['name' => 'aspect_ratio', 'contents' => $options['aspect_ratio'] ?? '16:9'],
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException($this->formatHttpError($response->status(), $response->body()));
            }

            Storage::disk('public')->makeDirectory('ai/images');
            $path = 'ai/images/'.uniqid('img_', true).'.png';
            Storage::disk('public')->put($path, base64_decode($response->json('image')));

            return [
                'type' => 'image',
                'url' => Storage::disk('public')->url($path),
                'content' => "Image generated via Stability AI: {$path}",
            ];
        } catch (\Throwable $e) {
            throw new \RuntimeException('Stability image generation failed: '.$e->getMessage());
        }
    }

    private function generateImageViaOpenAI(string $apiKey, string $prompt, array $options): array
    {
        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(60)
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => $options['model'] ?? 'dall-e-3',
                    'prompt' => $prompt,
                    'n' => 1,
                    'size' => $options['size'] ?? '1024x1024',
                    'response_format' => 'url',
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException($this->formatHttpError($response->status(), $response->body()));
            }

            return [
                'type' => 'image',
                'url' => data_get($response->json(), 'data.0.url'),
                'content' => 'Image generated via DALL-E',
            ];
        } catch (\Throwable $e) {
            throw new \RuntimeException('OpenAI image generation failed: '.$e->getMessage());
        }
    }

    private function generateImageViaGemini(string $apiKey, ?string $model, string $prompt, array $options): array
    {
        $models = [$model ?? 'gemini-2.0-flash-exp', 'imagen-3.0-generate-002'];
        $lastError = null;

        foreach ($models as $tryModel) {
            try {
                return $this->callGeminiImageModel($apiKey, $tryModel, $prompt, $options);
            } catch (\RuntimeException $e) {
                $lastError = $e;
                if (! Str::contains($e->getMessage(), ['404', 'not found', 'not supported'], true)) {
                    throw $e;
                }
            }
        }

        throw $lastError ?? new \RuntimeException('All Gemini image models failed');
    }

    private function callGeminiImageModel(string $apiKey, string $model, string $prompt, array $options): array
    {
        try {
            $response = Http::acceptJson()
                ->timeout($options['timeout'] ?? 90)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ],
                    'generationConfig' => [
                        'responseModalities' => ['TEXT', 'IMAGE'],
                        'temperature' => 0.4,
                    ],
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Gemini image error: '.$this->formatHttpError($response->status(), $response->body()));
            }

            $parts = data_get($response->json(), 'candidates.0.content.parts', []);
            $imageData = null;
            $mimeType = 'image/png';

            foreach ($parts as $part) {
                if (isset($part['inlineData'])) {
                    $imageData = $part['inlineData']['data'] ?? null;
                    $mimeType = $part['inlineData']['mimeType'] ?? 'image/png';
                    break;
                }
            }

            if (! $imageData) {
                throw new \RuntimeException('Gemini did not return image data');
            }

            $ext = match ($mimeType) {
                'image/jpeg' => 'jpg',
                'image/webp' => 'webp',
                default => 'png',
            };

            Storage::disk('public')->makeDirectory('ai/images');
            $path = 'ai/images/'.Str::uuid().".{$ext}";
            Storage::disk('public')->put($path, base64_decode($imageData));

            return [
                'type' => 'image',
                'url' => Storage::disk('public')->url($path),
                'content' => "Image generated via Gemini ({$model}): {$path}",
            ];
        } catch (\Throwable $e) {
            throw new \RuntimeException('Gemini image model call failed: '.$e->getMessage());
        }
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

        if (Str::contains($haystack, ['stability.ai', 'stable-diffusion'])) {
            return 'stability';
        }
        if (Str::contains($haystack, ['gemini', 'google', 'imagen'])) {
            return 'gemini';
        }

        return 'openai';
    }

    private function formatHttpError(int $status, string $body): string
    {
        return sprintf('API error %d: %s', $status, Str::limit(trim($body), 180));
    }
}

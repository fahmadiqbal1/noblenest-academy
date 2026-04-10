<?php

namespace App\Services;

use App\Models\AIProviderConfig;
use App\Services\Providers\ChatProviderService;
use App\Services\Providers\ImageGenerationService;
use App\Services\Providers\MediaGenerationService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * AIProviderGateway — Router for multiple AI provider integrations
 *
 * Refactored from 707-line monolith into focused services:
 * - ChatProviderService (Claude, ChatGPT, Gemini)
 * - ImageGenerationService (Stability, DALL-E, Gemini)
 * - MediaGenerationService (ElevenLabs, Replicate, RunwayML)
 *
 * This class routes to appropriate service based on capability.
 */
class AIProviderGateway
{
    public function __construct(
        protected ChatProviderService $chatService,
        protected ImageGenerationService $imageService,
        protected MediaGenerationService $mediaService,
    ) {}

    /**
     * Verify provider connectivity (routes to appropriate service)
     */
    public function verify(AIProviderConfig $provider): array
    {
        $driver = $this->resolveDriver($provider);

        // GitHub provider (no API verification needed)
        if ($driver === 'github') {
            return [
                'status' => 'configured',
                'message' => 'Repository-based provider configured. No live API handshake required.',
            ];
        }

        // No API key stored
        if (!$provider->api_key_encrypted) {
            return [
                'status' => 'unchecked',
                'message' => 'No API key stored yet.',
            ];
        }

        // Decrypt API key
        try {
            $apiKey = Crypt::decryptString($provider->api_key_encrypted);
        } catch (\Throwable $e) {
            return [
                'status' => 'failed',
                'message' => 'Stored API key could not be decrypted.',
            ];
        }

        // Route to appropriate service
        try {
            return match ($driver) {
                // Chat providers
                'anthropic', 'openai', 'gemini' => $this->chatService->verify($provider, $apiKey, $this->normalizeBaseUrl($provider->api_base_url, $driver)),

                // Image providers
                'stability', 'openai-image' => $this->imageService->verify($provider, $apiKey),

                // Media providers
                'elevenlabs', 'replicate', 'runway' => $this->mediaService->verify($provider, $apiKey),

                // Default (treat as OpenAI-compatible)
                default => $this->chatService->verify($provider, $apiKey, $this->normalizeBaseUrl($provider->api_base_url, $driver)),
            };
        } catch (\Throwable $e) {
            return [
                'status' => 'failed',
                'message' => Str::limit($e->getMessage(), 180),
            ];
        }
    }

    /**
     * Execute chat completion (delegates to ChatProviderService)
     */
    public function chat(AIProviderConfig $provider, string $prompt, array $options = []): array
    {
        return $this->chatService->chat($provider, $prompt, $options);
    }


    protected function normalizeBaseUrl(?string $baseUrl, string $driver = 'openai'): string
    {
        $default = match ($driver) {
            'anthropic'    => 'https://api.anthropic.com/v1',
            'gemini'       => 'https://generativelanguage.googleapis.com/v1beta',
            'stability'    => 'https://api.stability.ai',
            'elevenlabs'   => 'https://api.elevenlabs.io',
            'replicate'    => 'https://api.replicate.com/v1',
            'runway'       => 'https://api.dev.runwayml.com/v1',
            'openai-image' => 'https://api.openai.com/v1',
            default        => 'https://api.openai.com/v1',
        };

        $baseUrl = rtrim($baseUrl ?: $default, '/');

        if (in_array($driver, ['stability', 'elevenlabs', 'replicate', 'runway'], true)) {
            return $baseUrl;
        }

        if ($driver === 'gemini') {
            if (! Str::contains(parse_url($baseUrl, PHP_URL_PATH) ?: '', '/v1beta')) {
                $baseUrl .= '/v1beta';
            }
            return $baseUrl;
        }

        if (! Str::contains(parse_url($baseUrl, PHP_URL_PATH) ?: '', '/v1')) {
            $baseUrl .= '/v1';
        }

        return $baseUrl;
    }

    protected function resolveDriver(AIProviderConfig $provider): string
    {
        $driver = Str::lower((string) data_get($provider->extra_config, 'driver', ''));

        if ($driver !== '') {
            return $driver;
        }

        if (! empty($provider->extra_config['repo_url'])) {
            return 'github';
        }

        $haystack = Str::lower(implode(' ', array_filter([
            $provider->slug,
            $provider->name,
            $provider->api_base_url,
            $provider->model,
        ])));

        if (Str::contains($haystack, ['anthropic', 'claude'])) { return 'anthropic'; }
        if (Str::contains($haystack, ['gemini', 'google', 'generativelanguage'])) { return 'gemini'; }
        if (Str::contains($haystack, ['stability.ai', 'stable-diffusion', 'stable-image'])) { return 'stability'; }
        if (Str::contains($haystack, ['elevenlabs', 'eleven_labs', 'eleven-labs'])) { return 'elevenlabs'; }
        if (Str::contains($haystack, ['replicate.com', 'replicate'])) { return 'replicate'; }
        if (Str::contains($haystack, ['runwayml', 'runway'])) { return 'runway'; }
        if (Str::contains($haystack, ['dall-e', 'dalle', 'openai-image'])) { return 'openai-image'; }
        return 'openai';
    }

    protected function defaultModelFor(string $driver): string
    {
        return match ($driver) {
            'anthropic'    => 'claude-3-5-haiku-latest',
            'gemini'       => 'gemini-1.5-flash',
            'stability'    => 'core',
            'elevenlabs'   => 'eleven_multilingual_v2',
            'replicate'    => 'minimax/video-01',
            'runway'       => 'gen4_turbo',
            'openai-image' => 'dall-e-3',
            default        => 'gpt-4o-mini',
        };
    }

    /**
     * Generate image (delegates to ImageGenerationService)
     */
    public function generateImage(AIProviderConfig $provider, string $prompt, array $options = []): array
    {
        return $this->imageService->generate($provider, $prompt, $options);
    }

    /**
     * Generate audio (delegates to MediaGenerationService)
     */
    public function generateAudio(AIProviderConfig $provider, string $text, array $options = []): array
    {
        return $this->mediaService->generateAudio($provider, $text, $options);
    }

    /**
     * Generate video (delegates to MediaGenerationService)
     */
    public function generateVideo(AIProviderConfig $provider, string $prompt, array $options = []): array
    {
        return $this->mediaService->generateVideo($provider, $prompt, $options);
    }

    protected function formatHttpError(int $status, string $body): string
    {
        return sprintf('API error %d: %s', $status, Str::limit(trim($body), 180));
    }
}
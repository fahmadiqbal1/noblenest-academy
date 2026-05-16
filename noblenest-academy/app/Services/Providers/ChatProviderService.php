<?php

namespace App\Services\Providers;

use App\Models\AIProviderConfig;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Chat Completions Service — handles Claude, ChatGPT, Gemini
 *
 * Extracted from AIProviderGateway to follow Single Responsibility Principle.
 * Handles: verification, chat completions, fallbacks
 */
class ChatProviderService
{
    protected array $supportedDrivers = ['anthropic', 'grok', 'openai', 'gemini'];

    /**
     * Verify provider connectivity and credentials
     */
    public function verify(AIProviderConfig $provider, string $apiKey, string $baseUrl): array
    {
        $driver = $this->resolveDriver($provider);

        if ($driver === 'anthropic') {
            return $this->verifyViaAnthropic($provider, $apiKey, $baseUrl);
        }

        if ($driver === 'gemini') {
            return $this->verifyViaGemini($provider, $apiKey, $baseUrl);
        }

        // Generic OpenAI-compatible verification
        return $this->verifyViaOpenAI($provider, $apiKey, $baseUrl);
    }

    /**
     * Execute chat completion request
     */
    public function chat(AIProviderConfig $provider, string $prompt, array $options = []): array
    {
        $apiKey = Crypt::decryptString($provider->api_key_encrypted);
        $driver = $this->resolveDriver($provider);
        $baseUrl = $this->normalizeBaseUrl($provider->api_base_url, $driver);
        $model = $options['model'] ?? $provider->model ?? $this->defaultModelFor($driver);

        if ($driver === 'anthropic') {
            return $this->chatViaAnthropic($apiKey, $baseUrl, $model, $prompt, $options);
        }

        if ($driver === 'gemini') {
            return $this->chatViaGemini($apiKey, $baseUrl, $model, $prompt, $options);
        }

        return $this->chatViaOpenAI($apiKey, $baseUrl, $model, $prompt, $options);
    }

    // ============================================================================
    // PRIVATE: Provider-specific implementations
    // ============================================================================

    private function verifyViaOpenAI(AIProviderConfig $provider, string $apiKey, string $baseUrl): array
    {
        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(20)
                ->get("{$baseUrl}/models");

            if ($response->successful()) {
                $models = $response->json('data', []);
                return [
                    'status' => 'live',
                    'message' => count($models) > 0
                        ? 'Connected successfully and model discovery is available.'
                        : 'Connected successfully.',
                ];
            }

            // Fallback to chat completion test if models endpoint doesn't exist
            if (in_array($response->status(), [404, 405], true)) {
                return $this->verifyChatCompletion($provider, $apiKey, $baseUrl);
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

    private function verifyChatCompletion(AIProviderConfig $provider, string $apiKey, string $baseUrl): array
    {
        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(20)
                ->post("{$baseUrl}/chat/completions", [
                    'model' => $provider->model ?? 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Reply with the word OK only.'],
                        ['role' => 'user', 'content' => 'Health check'],
                    ],
                    'max_tokens' => 5,
                    'temperature' => 0,
                ]);

            if ($response->successful()) {
                return [
                    'status' => 'live',
                    'message' => 'Connected successfully using a lightweight completion check.',
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

    private function verifyViaAnthropic(AIProviderConfig $provider, string $apiKey, string $baseUrl): array
    {
        try {
            $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                ])
                ->acceptJson()
                ->timeout(20)
                ->post("{$baseUrl}/messages", [
                    'model' => $provider->model ?? 'claude-3-5-haiku-latest',
                    'max_tokens' => 8,
                    'temperature' => 0,
                    'messages' => [
                        ['role' => 'user', 'content' => 'Reply with OK only.'],
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'status' => 'live',
                    'message' => 'Connected successfully using Anthropic messages API.',
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

    private function verifyViaGemini(AIProviderConfig $provider, string $apiKey, string $baseUrl): array
    {
        try {
            $model = $provider->model ?? 'gemini-1.5-flash';
            $response = Http::acceptJson()
                ->timeout(20)
                ->post("{$baseUrl}/models/{$model}:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'Reply with OK only.'],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0,
                        'maxOutputTokens' => 8,
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'status' => 'live',
                    'message' => 'Connected successfully using Gemini generateContent API.',
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

    private function chatViaOpenAI(string $apiKey, string $baseUrl, string $model, string $prompt, array $options): array
    {
        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout($options['timeout'] ?? 60)
                ->post("{$baseUrl}/chat/completions", [
                    'model' => $model,
                    'messages' => array_values(array_filter([
                        ! empty($options['system_prompt'])
                            ? ['role' => 'system', 'content' => $options['system_prompt']]
                            : null,
                        ['role' => 'user', 'content' => $prompt],
                    ])),
                    'temperature' => $options['temperature'] ?? 0.6,
                    'max_tokens' => $options['max_tokens'] ?? 600,
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException($this->formatHttpError($response->status(), $response->body()));
            }

            $data = $response->json();

            return [
                'content' => trim((string) data_get($data, 'choices.0.message.content', '')),
                'model' => data_get($data, 'model', $model),
                'tokens' => data_get($data, 'usage.total_tokens'),
            ];
        } catch (\Throwable $e) {
            throw new \RuntimeException("OpenAI chat failed: " . $e->getMessage());
        }
    }

    private function chatViaAnthropic(string $apiKey, string $baseUrl, string $model, string $prompt, array $options): array
    {
        try {
            $response = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                ])
                ->acceptJson()
                ->timeout($options['timeout'] ?? 60)
                ->post("{$baseUrl}/messages", [
                    'model' => $model,
                    'system' => $options['system_prompt'] ?? null,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => $options['temperature'] ?? 0.6,
                    'max_tokens' => $options['max_tokens'] ?? 600,
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException($this->formatHttpError($response->status(), $response->body()));
            }

            $data = $response->json();
            $segments = collect(data_get($data, 'content', []))
                ->pluck('text')
                ->filter()
                ->implode("\n\n");

            return [
                'content' => trim($segments),
                'model' => data_get($data, 'model', $model),
                'tokens' => data_get($data, 'usage.input_tokens', 0) + data_get($data, 'usage.output_tokens', 0),
            ];
        } catch (\Throwable $e) {
            throw new \RuntimeException("Anthropic chat failed: " . $e->getMessage());
        }
    }

    private function chatViaGemini(string $apiKey, string $baseUrl, string $model, string $prompt, array $options): array
    {
        try {
            $body = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => $options['temperature'] ?? 0.6,
                    'maxOutputTokens' => $options['max_tokens'] ?? 600,
                ],
            ];

            if (! empty($options['system_prompt'])) {
                $body['systemInstruction'] = [
                    'parts' => [
                        ['text' => $options['system_prompt']],
                    ],
                ];
            }

            $response = Http::acceptJson()
                ->timeout($options['timeout'] ?? 60)
                ->post("{$baseUrl}/models/{$model}:generateContent?key={$apiKey}", $body);

            if (! $response->successful()) {
                throw new \RuntimeException($this->formatHttpError($response->status(), $response->body()));
            }

            $data = $response->json();
            $content = collect(data_get($data, 'candidates.0.content.parts', []))
                ->pluck('text')
                ->filter()
                ->implode("\n\n");

            return [
                'content' => trim($content),
                'model' => $model,
                'tokens' => data_get($data, 'usageMetadata.totalTokenCount'),
            ];
        } catch (\Throwable $e) {
            throw new \RuntimeException("Gemini chat failed: " . $e->getMessage());
        }
    }

    // ============================================================================
    // HELPERS
    // ============================================================================

    protected function normalizeBaseUrl(?string $baseUrl, string $driver = 'openai'): string
    {
        $default = match ($driver) {
            'anthropic' => 'https://api.anthropic.com/v1',
            'grok'      => 'https://api.x.ai/v1',
            'gemini'    => 'https://generativelanguage.googleapis.com/v1beta',
            default     => 'https://api.openai.com/v1',
        };

        $baseUrl = rtrim($baseUrl ?: $default, '/');

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
        $haystack = Str::lower(implode(' ', array_filter([
            data_get($provider->extra_config, 'driver', ''),
            $provider->slug,
            $provider->name,
            $provider->api_base_url,
            $provider->model,
        ])));

        if (Str::contains($haystack, ['anthropic', 'claude'])) {
            return 'anthropic';
        }
        if (Str::contains($haystack, ['grok', 'x.ai', 'xai'])) {
            return 'grok';
        }
        if (Str::contains($haystack, ['gemini', 'google', 'generativelanguage'])) {
            return 'gemini';
        }

        return 'openai';
    }

    protected function defaultModelFor(string $driver): string
    {
        return match ($driver) {
            'anthropic' => 'claude-haiku-4-5-20251001',
            'grok'      => 'grok-beta',
            'gemini'    => 'gemini-1.5-flash',
            default     => 'gpt-4o-mini',
        };
    }

    protected function formatHttpError(int $status, string $body): string
    {
        return sprintf('API error %d: %s', $status, Str::limit(trim($body), 180));
    }
}

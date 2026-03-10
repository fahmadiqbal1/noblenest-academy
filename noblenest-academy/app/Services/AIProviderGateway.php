<?php

namespace App\Services;

use App\Models\AIProviderConfig;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AIProviderGateway
{
    public function verify(AIProviderConfig $provider): array
    {
        if ($this->resolveDriver($provider) === 'github') {
            return [
                'status' => 'configured',
                'message' => 'Repository-based provider configured. No live API handshake required.',
            ];
        }

        if (! $provider->api_key_encrypted) {
            return [
                'status' => 'unchecked',
                'message' => 'No API key stored yet.',
            ];
        }

        try {
            $apiKey = Crypt::decryptString($provider->api_key_encrypted);
        } catch (\Throwable $e) {
            return [
                'status' => 'failed',
                'message' => 'Stored API key could not be decrypted.',
            ];
        }

        $driver = $this->resolveDriver($provider);
        $baseUrl = $this->normalizeBaseUrl($provider->api_base_url, $driver);

        try {
            if ($driver === 'anthropic') {
                return $this->verifyViaAnthropic($provider, $apiKey, $baseUrl);
            }

            if ($driver === 'gemini') {
                return $this->verifyViaGemini($provider, $apiKey, $baseUrl);
            }

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

            if (in_array($response->status(), [404, 405], true)) {
                return $this->verifyViaChatCompletion($provider, $apiKey, $baseUrl);
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

    public function chat(AIProviderConfig $provider, string $prompt, array $options = []): array
    {
        if (! $provider->api_key_encrypted) {
            throw new \RuntimeException('No API key is configured for this provider.');
        }

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
    }

    protected function verifyViaChatCompletion(AIProviderConfig $provider, string $apiKey, string $baseUrl): array
    {
        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(20)
            ->post("{$baseUrl}/chat/completions", [
                'model' => $provider->model ?? $this->defaultModelFor('openai'),
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
    }

    protected function verifyViaAnthropic(AIProviderConfig $provider, string $apiKey, string $baseUrl): array
    {
        $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
            ])
            ->acceptJson()
            ->timeout(20)
            ->post("{$baseUrl}/messages", [
                'model' => $provider->model ?? $this->defaultModelFor('anthropic'),
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
    }

    protected function verifyViaGemini(AIProviderConfig $provider, string $apiKey, string $baseUrl): array
    {
        $model = $provider->model ?? $this->defaultModelFor('gemini');

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
    }

    protected function chatViaAnthropic(string $apiKey, string $baseUrl, string $model, string $prompt, array $options): array
    {
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
    }

    protected function chatViaGemini(string $apiKey, string $baseUrl, string $model, string $prompt, array $options): array
    {
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
    }

    protected function normalizeBaseUrl(?string $baseUrl, string $driver = 'openai'): string
    {
        $default = match ($driver) {
            'anthropic' => 'https://api.anthropic.com/v1',
            'gemini' => 'https://generativelanguage.googleapis.com/v1beta',
            default => 'https://api.openai.com/v1',
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

        if (Str::contains($haystack, ['anthropic', 'claude'])) {
            return 'anthropic';
        }

        if (Str::contains($haystack, ['gemini', 'google', 'generativelanguage'])) {
            return 'gemini';
        }

        return 'openai';
    }

    protected function defaultModelFor(string $driver): string
    {
        return match ($driver) {
            'anthropic' => 'claude-3-5-haiku-latest',
            'gemini' => 'gemini-1.5-flash',
            default => 'gpt-4o-mini',
        };
    }

    protected function formatHttpError(int $status, string $body): string
    {
        return sprintf('API error %d: %s', $status, Str::limit(trim($body), 180));
    }
}
<?php

namespace App\Services;

use App\Models\AIProviderConfig;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
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

            if ($driver === 'stability') {
                return $this->verifyViaStability($apiKey);
            }
            if ($driver === 'elevenlabs') {
                return $this->verifyViaElevenLabs($apiKey);
            }
            if ($driver === 'replicate') {
                return $this->verifyViaReplicate($apiKey);
            }
            if ($driver === 'runway') {
                return $this->verifyViaRunway($apiKey);
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

    // ------------------------------------------------------------------
    // Stability AI — image generation
    // ------------------------------------------------------------------

    protected function verifyViaStability(string $apiKey): array
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $apiKey])
            ->acceptJson()->timeout(15)
            ->get('https://api.stability.ai/v1/user/account');
        if ($response->successful()) {
            return ['status' => 'live', 'message' => 'Stability AI connected. Account: ' . ($response->json('id') ?? 'OK')];
        }
        return ['status' => 'failed', 'message' => $this->formatHttpError($response->status(), $response->body())];
    }

    public function generateImage(AIProviderConfig $provider, string $prompt, array $options = []): array
    {
        $apiKey = Crypt::decryptString($provider->api_key_encrypted);
        $driver = $this->resolveDriver($provider);
        if (in_array($driver, ['openai', 'openai-image'], true)) {
            return $this->generateImageViaOpenAI($apiKey, $prompt, $options);
        }
        return $this->generateImageViaStability($apiKey, $prompt, $options);
    }

    protected function generateImageViaStability(string $apiKey, string $prompt, array $options): array
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $apiKey, 'Accept' => 'application/json'])
            ->timeout(60)->asMultipart()
            ->post('https://api.stability.ai/v2beta/stable-image/generate/core', [
                ['name' => 'prompt',        'contents' => $prompt],
                ['name' => 'output_format', 'contents' => 'png'],
                ['name' => 'aspect_ratio',  'contents' => $options['aspect_ratio'] ?? '16:9'],
            ]);
        if (! $response->successful()) {
            throw new \RuntimeException('Stability AI error: ' . $this->formatHttpError($response->status(), $response->body()));
        }
        Storage::disk('public')->makeDirectory('ai/images');
        $path = 'ai/images/' . uniqid('img_', true) . '.png';
        Storage::disk('public')->put($path, base64_decode($response->json('image')));
        return ['type' => 'image', 'url' => Storage::disk('public')->url($path), 'content' => "Image generated: {$path}"];
    }

    protected function generateImageViaOpenAI(string $apiKey, string $prompt, array $options): array
    {
        $response = Http::withToken($apiKey)->acceptJson()->timeout(60)
            ->post('https://api.openai.com/v1/images/generations', [
                'model'           => $options['model'] ?? 'dall-e-3',
                'prompt'          => $prompt,
                'n'               => 1,
                'size'            => $options['size'] ?? '1024x1024',
                'response_format' => 'url',
            ]);
        if (! $response->successful()) {
            throw new \RuntimeException('OpenAI image error: ' . $this->formatHttpError($response->status(), $response->body()));
        }
        return ['type' => 'image', 'url' => data_get($response->json(), 'data.0.url'), 'content' => 'Image generated via DALL-E.'];
    }

    // ------------------------------------------------------------------
    // ElevenLabs — text-to-speech
    // ------------------------------------------------------------------

    protected function verifyViaElevenLabs(string $apiKey): array
    {
        $response = Http::withHeaders(['xi-api-key' => $apiKey])
            ->acceptJson()->timeout(15)
            ->get('https://api.elevenlabs.io/v1/user');
        if ($response->successful()) {
            $tier = data_get($response->json(), 'subscription.tier', 'free');
            return ['status' => 'live', 'message' => "ElevenLabs connected. Tier: {$tier}."];
        }
        return ['status' => 'failed', 'message' => $this->formatHttpError($response->status(), $response->body())];
    }

    public function generateAudio(AIProviderConfig $provider, string $text, array $options = []): array
    {
        $apiKey  = Crypt::decryptString($provider->api_key_encrypted);
        $voiceId = $options['voice_id'] ?? data_get($provider->extra_config, 'voice_id', 'JBFqnCBsd6RMkjVDRZzb');
        $model   = $options['model'] ?? $provider->model ?? 'eleven_multilingual_v2';
        $response = Http::withHeaders([
                'xi-api-key'   => $apiKey,
                'Accept'       => 'audio/mpeg',
                'Content-Type' => 'application/json',
            ])
            ->timeout(60)
            ->post("https://api.elevenlabs.io/v1/text-to-speech/{$voiceId}", [
                'text'           => $text,
                'model_id'       => $model,
                'voice_settings' => ['stability' => 0.5, 'similarity_boost' => 0.75],
            ]);
        if (! $response->successful()) {
            throw new \RuntimeException('ElevenLabs error: ' . $this->formatHttpError($response->status(), $response->body()));
        }
        Storage::disk('public')->makeDirectory('ai/tts');
        $path = 'ai/tts/' . uniqid('tts_', true) . '.mp3';
        Storage::disk('public')->put($path, $response->body());
        return ['type' => 'audio', 'url' => Storage::disk('public')->url($path), 'content' => "Audio generated: {$path}"];
    }

    // ------------------------------------------------------------------
    // Replicate — video / image via hosted models
    // ------------------------------------------------------------------

    protected function verifyViaReplicate(string $apiKey): array
    {
        $response = Http::withToken($apiKey)->acceptJson()->timeout(15)
            ->get('https://api.replicate.com/v1/account');
        if ($response->successful()) {
            return ['status' => 'live', 'message' => 'Replicate connected. Account: ' . $response->json('username', 'verified')];
        }
        return ['status' => 'failed', 'message' => $this->formatHttpError($response->status(), $response->body())];
    }

    public function generateVideo(AIProviderConfig $provider, string $prompt, array $options = []): array
    {
        $apiKey = Crypt::decryptString($provider->api_key_encrypted);
        $driver = $this->resolveDriver($provider);
        if ($driver === 'runway') {
            return $this->generateVideoViaRunway($apiKey, $prompt, $options);
        }
        return $this->generateVideoViaReplicate($apiKey, $provider->model, $prompt, $options);
    }

    protected function generateVideoViaReplicate(string $apiKey, ?string $model, string $prompt, array $options): array
    {
        $model    = $model ?: 'minimax/video-01';
        $response = Http::withToken($apiKey)->acceptJson()->timeout(30)
            ->post("https://api.replicate.com/v1/models/{$model}/predictions", [
                'input' => [
                    'prompt'     => $prompt,
                    'num_frames' => $options['num_frames'] ?? 120,
                    'fps'        => $options['fps'] ?? 24,
                ],
            ]);
        if (! $response->successful()) {
            throw new \RuntimeException('Replicate error: ' . $this->formatHttpError($response->status(), $response->body()));
        }
        $output = $this->pollReplicate($apiKey, $response->json('urls.get'), 300);
        $url    = is_array($output) ? ($output[0] ?? (string) $output) : (string) $output;
        return ['type' => 'video', 'url' => $url, 'content' => "Video generated via Replicate: {$url}"];
    }

    protected function pollReplicate(string $apiKey, string $pollUrl, int $maxSeconds): mixed
    {
        $deadline = time() + $maxSeconds;
        while (time() < $deadline) {
            sleep(5);
            $poll   = Http::withToken($apiKey)->acceptJson()->timeout(20)->get($pollUrl);
            $status = $poll->json('status');
            if ($status === 'succeeded') {
                return $poll->json('output');
            }
            if (in_array($status, ['failed', 'canceled'], true)) {
                throw new \RuntimeException("Replicate prediction {$status}: " . ($poll->json('error', 'Unknown error')));
            }
        }
        throw new \RuntimeException('Replicate prediction timed out after ' . $maxSeconds . 's.');
    }

    // ------------------------------------------------------------------
    // RunwayML — video generation
    // ------------------------------------------------------------------

    protected function verifyViaRunway(string $apiKey): array
    {
        $response = Http::withHeaders([
                'Authorization'   => 'Bearer ' . $apiKey,
                'X-Runway-Version' => '2024-11-06',
            ])
            ->acceptJson()->timeout(15)
            ->get('https://api.dev.runwayml.com/v1/organizations');
        if ($response->successful()) {
            return ['status' => 'live', 'message' => 'RunwayML connected.'];
        }
        return ['status' => 'failed', 'message' => $this->formatHttpError($response->status(), $response->body())];
    }

    protected function generateVideoViaRunway(string $apiKey, string $prompt, array $options): array
    {
        $response = Http::withHeaders([
                'Authorization'   => 'Bearer ' . $apiKey,
                'X-Runway-Version' => '2024-11-06',
            ])
            ->acceptJson()->timeout(30)
            ->post('https://api.dev.runwayml.com/v1/text_to_video', [
                'model'      => $options['model'] ?? 'gen4_turbo',
                'promptText' => $prompt,
                'duration'   => $options['duration'] ?? 5,
                'ratio'      => $options['ratio'] ?? '1280:720',
            ]);
        if (! $response->successful()) {
            throw new \RuntimeException('RunwayML error: ' . $this->formatHttpError($response->status(), $response->body()));
        }
        $url = $this->pollRunway($apiKey, $response->json('id'), 300);
        $url = is_array($url) ? ($url[0] ?? (string) $url) : (string) $url;
        return ['type' => 'video', 'url' => $url, 'content' => "Video generated via RunwayML: {$url}"];
    }

    protected function pollRunway(string $apiKey, string $taskId, int $maxSeconds): mixed
    {
        $deadline = time() + $maxSeconds;
        while (time() < $deadline) {
            sleep(8);
            $poll   = Http::withHeaders([
                    'Authorization'   => 'Bearer ' . $apiKey,
                    'X-Runway-Version' => '2024-11-06',
                ])
                ->acceptJson()->timeout(20)
                ->get("https://api.dev.runwayml.com/v1/tasks/{$taskId}");
            $status = $poll->json('status');
            if ($status === 'SUCCEEDED') {
                return $poll->json('output');
            }
            if (in_array($status, ['FAILED', 'CANCELED'], true)) {
                throw new \RuntimeException("RunwayML task {$status}: " . ($poll->json('failure', 'Unknown error')));
            }
        }
        throw new \RuntimeException('RunwayML task timed out after ' . $maxSeconds . 's.');
    }

    protected function formatHttpError(int $status, string $body): string
    {
        return sprintf('API error %d: %s', $status, Str::limit(trim($body), 180));
    }
}
<?php

namespace App\Services\Providers;

use App\Helpers\I18n;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Phase 6 — reusable LLM translator service.
 *
 * Historically the i18n:translate command had this logic inlined. This class
 * extracts a thin (sourceLang, targetLang, text) -> string API so the Phase 6
 * video pipeline (ProduceLocalizedVideoJob) and other callers can translate
 * arbitrary content (scripts, captions, descriptions) without duplicating the
 * HTTP plumbing.
 *
 * Despite the class name (kept for forward-compatibility with a future
 * Anthropic adapter), the default driver is Groq llama-3.3-70b-versatile,
 * matching the i18n:translate command's existing wiring. When GROQ_API_KEY
 * is empty the service returns the source text verbatim (passthrough) so
 * tests and CI stay green.
 */
class AnthropicTranslator
{
    private const GROQ_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

    private const GROQ_MODEL = 'llama-3.3-70b-versatile';

    public function __construct(private readonly ?string $apiKey = null) {}

    /**
     * Translate a single string. Returns the source text unchanged when:
     *  - source == target locale
     *  - GROQ_API_KEY is empty (passthrough)
     *  - the API call fails (logged warning, soft fallback)
     */
    public function translate(string $sourceLang, string $targetLang, string $text): string
    {
        if ($sourceLang === $targetLang || trim($text) === '') {
            return $text;
        }

        $apiKey = $this->apiKey ?? (string) config('services.groq.api_key', '');
        if ($apiKey === '') {
            return $text; // passthrough — lead will set key in prod
        }

        $targetName = I18n::SUPPORTED_LANGUAGES[$targetLang] ?? $targetLang;
        $sourceName = I18n::SUPPORTED_LANGUAGES[$sourceLang] ?? $sourceLang;

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(60)
                ->post(self::GROQ_ENDPOINT, [
                    'model' => self::GROQ_MODEL,
                    'temperature' => 0.1,
                    'max_tokens' => 2048,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are a professional translator. Translate the user's text from {$sourceName} to {$targetName}. Preserve any :placeholder tokens, HTML, and markdown verbatim. Reply with ONLY the translated text — no commentary, no quotes, no code fences.",
                        ],
                        ['role' => 'user', 'content' => $text],
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('AnthropicTranslator non-200', [
                    'status' => $response->status(),
                    'target' => $targetLang,
                ]);

                return $text;
            }

            $out = (string) data_get($response->json(), 'choices.0.message.content', '');
            $out = trim($out);

            return $out !== '' ? $out : $text;
        } catch (\Throwable $e) {
            Log::warning('AnthropicTranslator failed', [
                'target' => $targetLang,
                'error' => $e->getMessage(),
            ]);

            return $text;
        }
    }
}

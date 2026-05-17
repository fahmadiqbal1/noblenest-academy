<?php

namespace App\Services;

use App\Models\AuditLogEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Phase 6 — content-safety filter for AI-generated and user-authored
 * scripts before they're sent to the avatar/translation pipeline.
 *
 * Two layers:
 *   1. Static blocklist from config/content_safety.php (case-insensitive
 *      word-boundary match).
 *   2. Optional LLM classifier (Groq llama-3.3-70b) for nuanced cases,
 *      gated by config('content_safety.use_llm_classifier') AND a
 *      present GROQ_API_KEY.
 *
 * Blocked content is logged to AuditLogEntry (action='content_safety_block')
 * so admins can review patterns.
 */
class ContentSafetyService
{
    /** Backwards-compat: Phase 1 pass-through query scope. */
    public function applySafetyFilter(Builder $query, array $context = []): Builder
    {
        return $query;
    }

    /** Backwards-compat: simple per-item check used by older callers. */
    public function isSafe(mixed $content, array $context = []): bool
    {
        if (! is_string($content)) {
            return true;
        }

        return ! $this->containsUnsafeContent($content, $context['locale'] ?? 'en');
    }

    /** @var array<int,string>|null */
    private ?array $reasons = null;

    public function containsUnsafeContent(string $script, string $locale = 'en'): bool
    {
        $this->reasons = [];

        $hits = $this->blocklistHits($script);
        if (! empty($hits)) {
            $this->reasons = array_map(fn ($w) => "blocklist:{$w}", $hits);
            $this->auditBlock();

            return true;
        }

        if (config('content_safety.use_llm_classifier') && config('services.groq.api_key')) {
            $classification = $this->llmClassify($script, $locale);
            if ($classification !== null && $classification['safe'] === false) {
                $this->reasons = ['llm:'.($classification['reason'] ?? 'unsafe')];
                $this->auditBlock();

                return true;
            }
        }

        return false;
    }

    /** @return array<int,string> */
    public function reasons(): array
    {
        return $this->reasons ?? [];
    }

    /** @return array<int,string> */
    private function blocklistHits(string $script): array
    {
        $blocklist = config('content_safety.blocklist', []);
        if (empty($blocklist)) {
            return [];
        }
        $lower = mb_strtolower($script);
        $hits = [];
        foreach ($blocklist as $word) {
            if (preg_match('/\b'.preg_quote((string) $word, '/').'\b/u', $lower)) {
                $hits[] = (string) $word;
            }
        }

        return $hits;
    }

    /** @return array{safe:bool,reason:string}|null */
    private function llmClassify(string $script, string $locale): ?array
    {
        try {
            $resp = Http::withToken((string) config('services.groq.api_key'))
                ->timeout(15)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.3-70b-versatile',
                    'temperature' => 0.0,
                    'max_tokens' => 256,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a child-safety content classifier for an LMS serving children aged 0-10. Reply with a single JSON object {"safe": true|false, "reason": "<one short sentence>"}.'],
                        ['role' => 'user', 'content' => "Locale: {$locale}\n\nScript:\n".$script],
                    ],
                ]);
            if (! $resp->successful()) {
                Log::warning('ContentSafety LLM classifier error', ['status' => $resp->status()]);

                return null;
            }
            $text = (string) $resp->json('choices.0.message.content', '');
            $decoded = json_decode($text, true);
            if (! is_array($decoded) || ! isset($decoded['safe'])) {
                return null;
            }

            return ['safe' => (bool) $decoded['safe'], 'reason' => (string) ($decoded['reason'] ?? '')];
        } catch (\Throwable $e) {
            Log::warning('ContentSafety LLM classifier exception', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function auditBlock(): void
    {
        try {
            AuditLogEntry::create([
                'actor_user_id' => optional(auth()->user())->id,
                'action' => 'content_safety_block',
                'target_type' => 'script',
                'target_id' => null,
                'ip' => request()?->ip(),
                'user_agent' => substr((string) request()?->userAgent(), 0, 512),
            ]);
        } catch (\Throwable $e) {
            Log::warning('ContentSafety audit log failed', ['error' => $e->getMessage()]);
        }
    }
}

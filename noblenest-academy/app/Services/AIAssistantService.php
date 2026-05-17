<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\AIJob;
use App\Models\AIProviderConfig;
use App\Models\ChildProfile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AI Assistant Service for Noble Nest Academy
 *
 * Provides child-safe, age-appropriate AI-powered assistance.
 * Uses the AIProviderGateway for actual LLM communication.
 */
class AIAssistantService
{
    protected AIProviderGateway $gateway;

    protected ?AIProviderConfig $provider = null;

    /**
     * System prompt for child-safe educational AI assistant.
     */
    protected const SYSTEM_PROMPT = <<<'PROMPT'
You are a friendly, helpful AI assistant for Noble Nest Academy, an educational platform for young children (ages 0-10) and their parents.

CRITICAL SAFETY RULES:
- NEVER provide inappropriate content for children
- NEVER discuss violence, inappropriate relationships, or adult themes
- NEVER ask for or store personal information about children
- Always encourage learning, curiosity, and creativity
- Be patient, kind, and encouraging
- Use age-appropriate language
- When uncertain, err on the side of safety

YOUR ROLE:
- Help parents find appropriate activities for their children's age and skill level
- Suggest weekly learning plans based on child's age, interests, and preferred language
- Explain educational concepts in simple, engaging ways
- Recommend activities from our curriculum (literacy, numeracy, STEM, arts, social-emotional, physical development)
- Support multiple languages: English, French, Russian, Chinese, Spanish, Korean, Urdu, Arabic

RESPONSE STYLE:
- Keep responses concise and helpful (under 200 words unless more detail is requested)
- Use bullet points for lists
- Include specific activity suggestions when relevant
- Be warm and supportive to parents
PROMPT;

    public function __construct(AIProviderGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Get the active AI provider for chat.
     */
    protected function getProvider(): ?AIProviderConfig
    {
        if ($this->provider) {
            return $this->provider;
        }

        // Cache provider lookup for 5 minutes
        $this->provider = Cache::remember('ai_assistant_provider', 300, function () {
            return AIProviderConfig::query()
                ->where('is_active', true)
                ->where('connection_status', 'live')
                ->latest()
                ->first();
        });

        return $this->provider;
    }

    /**
     * Send a chat message and get AI response.
     *
     * @param  string  $userMessage  The user's message
     * @param  array  $context  Additional context (child_age, language, etc.)
     * @return array Response with 'reply', 'provider', 'suggestions'
     */
    public function chat(string $userMessage, array $context = []): array
    {
        $provider = $this->getProvider();

        // Fallback to mock if no provider configured
        if (! $provider) {
            return $this->mockResponse($userMessage, $context);
        }

        try {
            // Build context-aware prompt
            $prompt = $this->buildPrompt($userMessage, $context);

            $response = $this->gateway->chat($provider, $prompt, [
                'system_prompt' => self::SYSTEM_PROMPT,
                'temperature' => 0.7,
                'max_tokens' => 500,
                'timeout' => 30,
            ]);

            // Apply content filter
            $filteredContent = $this->filterContent($response['content']);

            return [
                'reply' => $filteredContent,
                'provider' => $provider->name,
                'model' => $response['model'] ?? null,
                'suggestions' => $this->generateSuggestions($userMessage, $context),
            ];
        } catch (\Exception $e) {
            Log::warning('AI Assistant error, falling back to mock', [
                'error' => $e->getMessage(),
                'provider' => $provider->name ?? 'unknown',
            ]);

            // Graceful fallback to mock
            return $this->mockResponse($userMessage, $context);
        }
    }

    /**
     * Build context-aware prompt with user info.
     */
    protected function buildPrompt(string $userMessage, array $context): string
    {
        $contextParts = [];

        if (! empty($context['child_age'])) {
            $contextParts[] = "Child's age: {$context['child_age']} months";
        }

        if (! empty($context['language'])) {
            $contextParts[] = "Preferred language: {$context['language']}";
        }

        if (! empty($context['interests'])) {
            $contextParts[] = "Child's interests: ".implode(', ', (array) $context['interests']);
        }

        if (! empty($context['recent_activities'])) {
            $contextParts[] = 'Recent activities completed: '.implode(', ', (array) $context['recent_activities']);
        }

        $contextString = ! empty($contextParts)
            ? '[Context: '.implode('; ', $contextParts)."]\n\n"
            : '';

        return $contextString.$userMessage;
    }

    /**
     * Filter AI response content for child safety.
     */
    protected function filterContent(string $content): string
    {
        // Remove any potential inappropriate content patterns
        $blockedPatterns = [
            '/\b(kill|die|death|murder|violence|weapon|gun|knife)\b/i',
            '/\b(sex|sexual|nude|naked|porn)\b/i',
            '/\b(drugs|cocaine|heroin|marijuana)\b/i',
            '/\b(hate|racist|sexist)\b/i',
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                Log::warning('AI response filtered for inappropriate content');

                return "I'm sorry, but I can only help with educational questions and activities. Let me suggest some fun learning activities instead!";
            }
        }

        return $content;
    }

    /**
     * Generate contextual follow-up suggestions.
     */
    protected function generateSuggestions(string $userMessage, array $context): array
    {
        $age = $context['child_age'] ?? null;

        $suggestions = [
            'Show me a weekly learning plan',
            'Recommend activities for {skill} skills',
            'What activities work best for my child\'s age?',
        ];

        if ($age !== null) {
            if ($age <= 12) {
                $suggestions = [
                    'Sensory play activities for infants',
                    'Music and movement for babies',
                    'First words learning activities',
                ];
            } elseif ($age <= 36) {
                $suggestions = [
                    'Color and shape recognition activities',
                    'Simple counting games for toddlers',
                    'Arts and crafts for 2-year-olds',
                ];
            } elseif ($age <= 60) {
                $suggestions = [
                    'Letter tracing and phonics',
                    'Basic math concepts for preschool',
                    'Science experiments for young learners',
                ];
            } else {
                $suggestions = [
                    'Reading comprehension activities',
                    'STEM projects for school-age children',
                    'Creative writing prompts',
                ];
            }
        }

        return $suggestions;
    }

    /**
     * Friendly mock response when AI is unavailable.
     */
    protected function mockResponse(string $userMessage, array $context): array
    {
        $age = $context['child_age'] ?? null;
        $ageInfo = $age !== null ? "Based on your child's age ({$age} months), " : '';

        $response = "Hello! I'm your Noble Nest Academy assistant. {$ageInfo}I'd love to help you find the perfect learning activities. ";

        if (! empty($userMessage)) {
            $response .= 'You asked about: "'.mb_substr($userMessage, 0, 100).'". ';
        }

        $response .= "To give you personalized recommendations, please tell me your child's age and interests. I can suggest activities in literacy, numeracy, STEM, arts, social-emotional learning, and physical development!";

        return [
            'reply' => $response,
            'provider' => 'mock',
            'suggestions' => $this->generateSuggestions($userMessage, $context),
        ];
    }

    /**
     * Check if AI service is available.
     */
    public function isAvailable(): bool
    {
        return $this->getProvider() !== null;
    }

    /**
     * Get the name of the active provider.
     */
    public function getProviderName(): ?string
    {
        return $this->getProvider()?->name;
    }

    /**
     * Generate a batch of activities for a content generation job.
     * Called by ProcessContentBatchJob when an AI provider is available.
     */
    public function generateBatch(
        AIJob $job,
        string $subject,
        string $ageTier,
        int $count,
        string $language,
        bool $isFree
    ): void {
        // Age-tier specific safety requirements for prompt
        $safetyGuidance = match ($ageTier) {
            'baby' => 'CRITICAL: Every activity MUST include safety_warnings about choking hazards and parent_involvement MUST be "collaborative".',
            'toddler' => 'Include safety_warnings if the activity involves water, scissors, or objects smaller than a golf ball.',
            'preschool' => 'Note safety_warnings for any sharp items, hot surfaces, or situations requiring close adult supervision.',
            'school' => 'Include safety_warnings if the activity involves tools, cooking, or outdoor hazards.',
            default => '',
        };

        $cognitiveOptions = implode(', ', [
            'attention', 'working_memory', 'inhibitory_control', 'cognitive_flexibility',
            'pattern_recognition', 'spatial_reasoning', 'sequential_thinking', 'metacognition', 'subitizing',
        ]);
        $domainOptions = implode(', ', [
            'fine_motor', 'gross_motor', 'language', 'numeracy', 'cognitive', 'social_emotional',
            'creative_arts', 'executive_function', 'sensory', 'emotional_regulation',
        ]);

        for ($i = 1; $i <= $count; $i++) {
            $prompt = <<<PROMPT
Create a {$ageTier}-tier {$subject} activity for children in {$language}.

Return ONLY a valid JSON object with ALL of these exact keys:
- title: (string) Engaging, age-appropriate activity name
- description: (string) 2-3 sentences describing the activity and its value
- instructions: (string) Step-by-step instructions
- duration_minutes: (integer) Between 5 and 30
- mess_level: (string) One of: "low", "medium", "high"
- safety_warnings: (array of strings) Safety warnings to show parents. Empty array [] if none.
- adaptations: (object) With exactly two keys: {"easier": "...", "harder": "..."}
- cognitive_domain: (string) Primary cognitive domain. Choose ONE from: {$cognitiveOptions}
- developmental_domains: (array of strings) All domains this activity targets. Choose from: {$domainOptions}
- materials_cost: (string) One of: "free", "low", "medium"
- parent_involvement: (string) One of: "independent", "guided", "collaborative"

{$safetyGuidance}

Return ONLY the JSON object, no markdown, no explanation.
PROMPT;

            $result = $this->chat($prompt, [
                'language' => $language,
                'subject' => $subject,
            ]);

            // Robust JSON parsing — strip markdown fences if AI wraps in them
            $raw = trim($result['reply'] ?? '');
            $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
            $raw = preg_replace('/\s*```$/', '', $raw);
            $parsed = json_decode($raw, true);

            if (! is_array($parsed)) {
                // Log and create a placeholder — do not crash the job
                Log::warning('AIAssistantService: JSON parse failed in generateBatch', [
                    'job_id' => $job->id,
                    'subject' => $subject,
                    'i' => $i,
                    'raw' => substr($raw, 0, 500),
                ]);
                $parsed = [];
            }

            try {
                Activity::create([
                    'title' => $parsed['title'] ?? "{$subject} Activity {$i} ({$ageTier})",
                    'description' => $parsed['description'] ?? null,
                    'instructions' => $parsed['instructions'] ?? null,
                    'duration_minutes' => (int) ($parsed['duration_minutes'] ?? 15),
                    'mess_level' => in_array($parsed['mess_level'] ?? '', ['low', 'medium', 'high'])
                                                ? $parsed['mess_level'] : 'low',
                    'safety_warnings' => $parsed['safety_warnings'] ?? [],
                    'adaptations' => $parsed['adaptations'] ?? null,
                    'cognitive_domain' => $parsed['cognitive_domain'] ?? null,
                    'developmental_domains' => $parsed['developmental_domains'] ?? [],
                    'materials_cost' => in_array($parsed['materials_cost'] ?? '', ['free', 'low', 'medium'])
                                                ? $parsed['materials_cost'] : 'free',
                    'parent_involvement' => in_array($parsed['parent_involvement'] ?? '', ['independent', 'guided', 'collaborative'])
                                                ? $parsed['parent_involvement'] : 'guided',
                    'subject' => $subject,
                    'language' => $language,
                    'is_free' => $isFree,
                    'published' => false,
                    'source_job_id' => $job->id,
                ]);
            } catch (\Throwable $e) {
                Log::error('AIAssistantService: Activity::create failed in generateBatch', [
                    'job_id' => $job->id,
                    'error' => $e->getMessage(),
                    'parsed' => $parsed,
                ]);
            }
        }
    }

    // ==================================================================
    // Phase 5 — parent-dashboard suggestions (Groq, no-PII)
    // ==================================================================

    private const GROQ_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

    private const GROQ_MODEL = 'llama-3.3-70b-versatile';

    /**
     * Suggest 3 next-best activities for a child. Cached per child for 1h.
     *
     * @return array<int, array{title: string, why: string, activity_id?: int|null}>
     */
    public function suggestForChild(ChildProfile $child): array
    {
        return Cache::remember("ai-suggest-{$child->id}", 3600, function () use ($child) {
            $apiKey = (string) config('services.groq.api_key', '');
            if ($apiKey === '') {
                return $this->stubSuggestions();
            }

            // ChildProfile doesn't currently declare a skillState() relation;
            // when added in a later phase, swap to $child->skillState()->first().
            $skill = null;

            $recent = $child->activityProgress()
                ->latest('completed_at')
                ->limit(5)
                ->get(['activity_id', 'status', 'score', 'completed_at'])
                ->toArray();

            $rawPayload = [
                'child' => [
                    'name' => $child->name,
                    'nickname' => $child->nickname,
                    'age_months' => $child->age_months,
                    'age_tier' => $child->age_tier,
                    'preferred_language' => $child->preferred_language,
                    'parental_consent_at' => $child->parental_consent_at,
                ],
                'skill_state' => $skill, // placeholder — wire ChildSkillState in a later phase
                'recent_progress' => $recent,
            ];

            $scrubbed = self::scrubPII($rawPayload);

            try {
                $resp = Http::withToken($apiKey)
                    ->timeout(15)
                    ->acceptJson()
                    ->post(self::GROQ_ENDPOINT, [
                        'model' => self::GROQ_MODEL,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'Suggest 3 next-best activities for a child given their skill state. '
                                    ."Do NOT include the child's name or any PII in your response. "
                                    .'Respond with ONLY a JSON array of objects with keys: title (string), why (string), activity_id (integer or null). '
                                    .'Return no prose, no markdown fences.',
                            ],
                            [
                                'role' => 'user',
                                'content' => json_encode($scrubbed, JSON_UNESCAPED_UNICODE),
                            ],
                        ],
                        'temperature' => 0.4,
                        'max_tokens' => 400,
                    ]);

                $content = (string) data_get($resp->json(), 'choices.0.message.content', '');
                $content = trim(preg_replace('/^```(?:json)?|```$/m', '', $content) ?? '');
                $parsed = json_decode($content, true);

                if (! is_array($parsed)) {
                    return $this->stubSuggestions();
                }

                $out = [];
                foreach (array_slice($parsed, 0, 3) as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $out[] = [
                        'title' => (string) ($row['title'] ?? 'Suggested activity'),
                        'why' => (string) ($row['why'] ?? ''),
                        'activity_id' => isset($row['activity_id']) && is_numeric($row['activity_id'])
                            ? (int) $row['activity_id'] : null,
                    ];
                }

                return $out ?: $this->stubSuggestions();
            } catch (\Throwable $e) {
                Log::warning('AIAssistantService::suggestForChild failed', [
                    'child_id' => $child->id,
                    'error' => $e->getMessage(),
                ]);

                return $this->stubSuggestions();
            }
        });
    }

    /**
     * Strip PII keys (recursively) from the payload before sending to Groq.
     *
     * @param  array<mixed>  $payload
     * @return array<mixed>
     */
    public static function scrubPII(array $payload): array
    {
        $pii = ['name', 'nickname', 'email', 'phone', 'address', 'ip', 'user_agent'];

        $walk = function ($value) use (&$walk, $pii) {
            if (! is_array($value)) {
                return $value;
            }
            $out = [];
            foreach ($value as $k => $v) {
                $keyLower = is_string($k) ? strtolower($k) : $k;
                if (is_string($keyLower)) {
                    if (in_array($keyLower, $pii, true)) {
                        continue;
                    }
                    if (str_starts_with($keyLower, 'parental_consent_')) {
                        continue;
                    }
                }
                $out[$k] = is_array($v) ? $walk($v) : $v;
            }

            return $out;
        };

        return $walk($payload);
    }

    /** @return array<int, array{title: string, why: string, activity_id: null}> */
    private function stubSuggestions(): array
    {
        return [
            ['title' => 'AI suggestions unavailable in this environment.', 'why' => '', 'activity_id' => null],
        ];
    }
}

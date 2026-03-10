<?php

namespace App\Services;

use App\Models\AIProviderConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
                ->where('enabled', true)
                ->where('health_status', 'live')
                ->orderByDesc('priority')
                ->first();
        });

        return $this->provider;
    }

    /**
     * Send a chat message and get AI response.
     * 
     * @param string $userMessage The user's message
     * @param array $context Additional context (child_age, language, etc.)
     * @return array Response with 'reply', 'provider', 'suggestions'
     */
    public function chat(string $userMessage, array $context = []): array
    {
        $provider = $this->getProvider();

        // Fallback to mock if no provider configured
        if (!$provider) {
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

        if (!empty($context['child_age'])) {
            $contextParts[] = "Child's age: {$context['child_age']} months";
        }

        if (!empty($context['language'])) {
            $contextParts[] = "Preferred language: {$context['language']}";
        }

        if (!empty($context['interests'])) {
            $contextParts[] = "Child's interests: " . implode(', ', (array) $context['interests']);
        }

        if (!empty($context['recent_activities'])) {
            $contextParts[] = "Recent activities completed: " . implode(', ', (array) $context['recent_activities']);
        }

        $contextString = !empty($contextParts) 
            ? "[Context: " . implode('; ', $contextParts) . "]\n\n"
            : '';

        return $contextString . $userMessage;
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
        $ageInfo = $age !== null ? "Based on your child's age ({$age} months), " : "";

        $response = "Hello! I'm your Noble Nest Academy assistant. {$ageInfo}I'd love to help you find the perfect learning activities. ";
        
        if (!empty($userMessage)) {
            $response .= "You asked about: \"" . mb_substr($userMessage, 0, 100) . "\". ";
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
}

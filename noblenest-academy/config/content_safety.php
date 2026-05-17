<?php

/**
 * Phase 6 — content safety blocklist.
 *
 * Static profanity / violence / adult-theme keywords used by
 * App\Services\ContentSafetyService::containsUnsafeContent().
 *
 * Keep these lowercased; the service does a case-insensitive word-boundary
 * match. Operators can extend this list without touching code.
 */

return [
    'blocklist' => [
        // violence / weapons
        'kill', 'murder', 'shoot', 'stab', 'strangle', 'bomb', 'gun', 'rifle',
        'knife', 'weapon', 'blood', 'gore',
        // adult / sexual
        'sex', 'sexual', 'porn', 'nude', 'naked', 'erotic',
        // substances
        'cocaine', 'heroin', 'meth', 'marijuana',
        // self-harm
        'suicide', 'self-harm', 'cutting',
        // hate
        'racist', 'nazi', 'lynch',
        // profanity (mild starter set — extend in prod)
        'fuck', 'shit', 'bitch', 'asshole',
    ],

    /**
     * If true and GROQ_API_KEY is set, ContentSafetyService will additionally
     * call a Groq classifier as a second-line check. Disable for cost control.
     */
    'use_llm_classifier' => true,
];

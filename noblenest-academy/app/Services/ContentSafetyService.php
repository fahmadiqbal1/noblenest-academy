<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

/**
 * Generic content-safety filter.
 *
 * In Phase 1 this is a thin pass-through — the v1 product surface
 * (activities, quizzes, badges) does not yet need conditional safety
 * filtering. The interface is preserved so callers (and future
 * verticals such as nutrition / health) can plug in safety rules
 * without refactoring sites.
 */
class ContentSafetyService
{
    /**
     * Apply safety filters to an existing query.
     *
     * @param  Builder  $query
     * @param  array<string,mixed>  $context  Optional caller-supplied context (e.g. age, flags).
     */
    public function applySafetyFilter(Builder $query, array $context = []): Builder
    {
        // No-op in v1. Hook future per-context rules here.
        return $query;
    }

    /**
     * Determine whether an individual content item is safe in the given context.
     *
     * @param  mixed  $content
     * @param  array<string,mixed>  $context
     */
    public function isSafe(mixed $content, array $context = []): bool
    {
        return true;
    }
}

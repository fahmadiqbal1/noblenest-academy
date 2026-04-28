<?php

namespace App\Services;

use App\Models\ContraindicationMatrix;
use App\Models\MaternalContent;
use App\Models\MaternalProfile;
use Illuminate\Database\Eloquent\Builder;

class MaternalContentFilterService
{
    /**
     * Return a query builder for content safe for this profile.
     * Excludes any content flagged in the contraindication_matrix
     * for the user's declared health conditions.
     */
    public function safeContentQuery(MaternalProfile $profile): Builder
    {
        $unsafeIds = $this->getUnsafeContentIds($profile);

        return MaternalContent::published()
            ->forStage($profile->stage)
            ->when($unsafeIds, fn (Builder $q) => $q->whereNotIn('id', $unsafeIds));
    }

    /**
     * Filter an existing query to exclude contraindicated content.
     */
    public function applySafetyFilter(Builder $query, MaternalProfile $profile): Builder
    {
        $unsafeIds = $this->getUnsafeContentIds($profile);

        return $query->when($unsafeIds, fn (Builder $q) => $q->whereNotIn('maternal_contents.id', $unsafeIds));
    }

    /**
     * Check whether a specific content item is safe for the profile.
     */
    public function isSafe(MaternalContent $content, MaternalProfile $profile): bool
    {
        $conditions = $profile->health_conditions ?? [];

        if (empty($conditions)) {
            return true;
        }

        return !ContraindicationMatrix::where('maternal_content_id', $content->id)
            ->whereIn('condition', $conditions)
            ->exists();
    }

    /**
     * Get all content IDs that are unsafe for the given profile's conditions.
     */
    private function getUnsafeContentIds(MaternalProfile $profile): array
    {
        $conditions = $profile->health_conditions ?? [];

        if (empty($conditions)) {
            return [];
        }

        return ContraindicationMatrix::whereIn('condition', $conditions)
            ->pluck('maternal_content_id')
            ->unique()
            ->values()
            ->all();
    }
}

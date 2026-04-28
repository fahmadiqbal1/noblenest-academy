<?php

namespace App\Policies;

use App\Models\ChildProfile;
use App\Models\User;

class ChildProfilePolicy
{
    /**
     * Parent may view their own child's profile.
     */
    public function view(User $user, ChildProfile $childProfile): bool
    {
        return $user->id === $childProfile->parent_id;
    }

    /**
     * Parent may update their own child's profile.
     */
    public function update(User $user, ChildProfile $childProfile): bool
    {
        return $user->id === $childProfile->parent_id;
    }

    /**
     * Parent may delete their own child's profile.
     */
    public function delete(User $user, ChildProfile $childProfile): bool
    {
        return $user->id === $childProfile->parent_id;
    }
}

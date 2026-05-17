<?php

namespace App\Policies;

use App\Models\ChildProfile;
use App\Models\User;

class ChildProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ChildProfile $childProfile): bool
    {
        return $user->isAdmin() || $user->id === $childProfile->parent_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isParent();
    }

    public function update(User $user, ChildProfile $childProfile): bool
    {
        return $user->isAdmin() || $user->id === $childProfile->parent_id;
    }

    public function delete(User $user, ChildProfile $childProfile): bool
    {
        return $user->isAdmin() || $user->id === $childProfile->parent_id;
    }
}

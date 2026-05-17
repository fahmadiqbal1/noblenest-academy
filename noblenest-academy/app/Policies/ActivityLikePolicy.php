<?php

namespace App\Policies;

use App\Models\ActivityLike;
use App\Models\User;

class ActivityLikePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ActivityLike $record): bool
    {
        return $user->isAdmin() || $record->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isParent();
    }

    public function update(User $user, ActivityLike $record): bool
    {
        return $user->isAdmin() || $record->user_id === $user->id;
    }

    public function delete(User $user, ActivityLike $record): bool
    {
        return $user->isAdmin() || $record->user_id === $user->id;
    }
}

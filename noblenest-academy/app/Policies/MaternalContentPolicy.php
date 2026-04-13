<?php

namespace App\Policies;

use App\Models\MaternalContent;
use App\Models\User;

class MaternalContentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasMaternalProfile() || $user->isAdmin();
    }

    public function view(User $user, MaternalContent $content): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $content->is_published && $content->moderation_status === 'approved';
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, MaternalContent $content): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, MaternalContent $content): bool
    {
        return $user->isAdmin();
    }
}

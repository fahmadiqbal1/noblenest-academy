<?php

namespace App\Policies;

use App\Models\MaternalProfile;
use App\Models\User;

class MaternalProfilePolicy
{
    public function view(User $user, MaternalProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }

    public function update(User $user, MaternalProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }

    public function delete(User $user, MaternalProfile $profile): bool
    {
        return $user->id === $profile->user_id;
    }
}

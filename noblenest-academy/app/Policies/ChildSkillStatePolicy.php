<?php

namespace App\Policies;

use App\Models\ChildSkillState;
use App\Models\User;

class ChildSkillStatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ChildSkillState $record): bool
    {
        return $user->isAdmin() || optional($record->childProfile)->parent_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ChildSkillState $record): bool
    {
        return $user->isAdmin() || optional($record->childProfile)->parent_id === $user->id;
    }

    public function delete(User $user, ChildSkillState $record): bool
    {
        return $user->isAdmin();
    }
}

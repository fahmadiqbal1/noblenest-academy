<?php

namespace App\Policies;

use App\Models\QuizAnswer;
use App\Models\User;

class QuizAnswerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, QuizAnswer $record): bool
    {
        return $user->isAdmin() || optional($record->attempt)->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isParent();
    }

    public function update(User $user, QuizAnswer $record): bool
    {
        return $user->isAdmin() || optional($record->attempt)->user_id === $user->id;
    }

    public function delete(User $user, QuizAnswer $record): bool
    {
        return $user->isAdmin() || optional($record->attempt)->user_id === $user->id;
    }
}

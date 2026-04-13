<?php

namespace App\Policies;

use App\Models\MaternalJournal;
use App\Models\User;

class MaternalJournalPolicy
{
    public function view(User $user, MaternalJournal $journal): bool
    {
        return $user->id === $journal->profile->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasMaternalProfile();
    }

    public function update(User $user, MaternalJournal $journal): bool
    {
        return $user->id === $journal->profile->user_id;
    }

    public function delete(User $user, MaternalJournal $journal): bool
    {
        return $user->id === $journal->profile->user_id;
    }
}

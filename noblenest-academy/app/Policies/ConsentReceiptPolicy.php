<?php

namespace App\Policies;

use App\Models\ConsentReceipt;
use App\Models\User;

class ConsentReceiptPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ConsentReceipt $record): bool
    {
        return $user->isAdmin() || $record->parent_user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isParent();
    }

    public function update(User $user, ConsentReceipt $record): bool
    {
        // Consent receipts are an immutable audit trail. Only withdrawal
        // (handled explicitly elsewhere) and admin correction are allowed.
        return $user->isAdmin();
    }

    public function delete(User $user, ConsentReceipt $record): bool
    {
        // Never delete a consent receipt — it is a COPPA audit record.
        return false;
    }
}

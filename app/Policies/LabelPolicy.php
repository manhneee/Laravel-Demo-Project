<?php

namespace App\Policies;

use App\Models\Label;
use App\Models\User;

class LabelPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return false;
    }

    public function view(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return false;
    }

    public function create(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return false;
    }

    public function update(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return false;
    }

    public function delete(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return false;
    }
}

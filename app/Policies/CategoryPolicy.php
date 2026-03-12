<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return false;
    }

    public function view(User $user, Category $category): bool
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

    public function update(User $user, Category $category): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Category $category): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return false;
    }
}

<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use App\Models\ArticleCategory;
use App\Models\User;

class ArticleCategoryPolicy
{
    use HandlesAuthorization;
    // Admin 
    public function before(User $user, string $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
        // proceed to other policies
        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, ArticleCategory $articleCategory): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ArticleCategory $articleCategory): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ArticleCategory $articleCategory): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ArticleCategory $articleCategory): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ArticleCategory $articleCategory): bool
    {
        return false;
    }
}
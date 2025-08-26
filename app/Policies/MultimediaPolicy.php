<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Multimedia;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MultimediaPolicy
{
    use HandlesAuthorization;

    // Override for the admins 
    public function before(User $user, string $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null; # continue to other policies 
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
    public function view(?User $user, Multimedia $multimedia): bool
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
    public function update(User $user, Multimedia $multimedia): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Multimedia $multimedia): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Multimedia $multimedia): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Multimedia $multimedia): bool
    {
        return $user->role === 'editor';
    }

    public function archive(User $user, Multimedia $multimedia)
    {
        return $user->role === 'editor';
    }
}

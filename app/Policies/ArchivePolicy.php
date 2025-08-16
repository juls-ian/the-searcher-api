<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Archive;
use App\Models\User;

class ArchivePolicy
{
    public function before(User $user, string $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }

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
    public function view(?User $user, Archive $archive): bool
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
    public function update(User $user, Archive $archive): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Archive $archive): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Archive $archive): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Archive $archive): bool
    {
        return $user->role === 'editor';
    }

    public function unarchive(User $user, Archive $archive)
    {
        return $user->role === 'editor';
    }
}
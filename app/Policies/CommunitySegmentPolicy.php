<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use App\Models\CommunitySegment;
use App\Models\User;

class CommunitySegmentPolicy
{
    use HandlesAuthorization;
    
    // Override abilities
    public function before(User $user, string $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null; # proceed to other policy methods
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
    public function view(?User $user, CommunitySegment $communitySegment): bool
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
    public function update(User $user, CommunitySegment $communitySegment): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CommunitySegment $communitySegment): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CommunitySegment $communitySegment): bool
    {
        return $user->role === 'editor';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CommunitySegment $communitySegment): bool
    {
        return false;
    }
}
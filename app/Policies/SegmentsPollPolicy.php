<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\SegmentsPoll;
use App\Models\User;

class SegmentsPollPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SegmentsPoll $segmentsPoll): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SegmentsPoll $segmentsPoll): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SegmentsPoll $segmentsPoll): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SegmentsPoll $segmentsPoll): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SegmentsPoll $segmentsPoll): bool
    {
        return false;
    }
}

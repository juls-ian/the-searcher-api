<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Str;


class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Runs before a new user is saved to database
     */
    public function creating(User $user)
    {
        $fullNameSlug = Str::slug($user->full_name);
        $penNameSlug = Str::slug($user->pen_name);
        $originalFullNameSlug = $fullNameSlug;
        $originalPenNameSlug = $penNameSlug;

        $fnCount = 1;
        $pnCount = 1;

        // Check if the slugs already exists in db. increments if so
        while (User::where('full_name_slug', $fullNameSlug)->exists()) {
            $fullNameSlug = $originalFullNameSlug . '-' . $fnCount++; # if it exist, appends the number 
        }

        while (User::where('pen_name_slug', $penNameSlug)->exists()) {
            $penNameSlug = $originalPenNameSlug . '-' . $pnCount++;
        }

        // Final values
        $user->full_name_slug = $fullNameSlug;
        $user->pen_name_slug = $penNameSlug;

    }

    /**
     * Runs before a new user is updated in database
     */
    public function updating(User $user)
    {
        // Full name modified? checker 
        if ($user->isDirty('full_name')) { # check if FN was modified 
            $slug = Str::slug($user->full_name);
            $originalSlug = $slug; # store orig slug
            $count = 1;

            while (
                User::where('full_name_slug', $slug) # check if slug exists
                    ->where('id', '!=', $user->id) # ensure not to compare slug to itself
                    ->exists()
            ) {
                $slug = $originalSlug . '-' . $count++;
            }

            $user->full_name_slug = $slug; # final value 
        }

        // Pen name modified? checker
        if ($user->isDirty('pen_name')) {
            $slug = Str::slug($user->pen_name);
            $originalSlug = $slug;
            $count = 1;

            while (
                User::where('pen_name_slug', $slug)
                    ->where('id', '!=', $user->id)
                    ->exists()
            ) {
                $slug = $originalSlug . '-' . $count++;
            }

            $user->pen_name_slug = $slug;

        }

    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
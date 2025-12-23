<?php

namespace App\Observers;

use App\Models\Family;
use Illuminate\Support\Facades\Auth;

class FamilyObserver
{
    /**
     * Handle the Family "saving" event.
     */
    public function saving(Family $family): void
    {
        if ($family->isDirty() && !$family->isDirty('created_at')) {
            $family->updated_by_user_id = Auth::id();
        }
    }

    /**
     * Handle the Family "deleting" event.
     */
    public function deleting(Family $family): void
    {
        // Note: Families don't use soft deletes currently
        // If soft deletes added later, track who deleted
    }

    /**
     * Handle the Family "created" event.
     */
    public function created(Family $family): void
    {
        //
    }

    /**
     * Handle the Family "updated" event.
     */
    public function updated(Family $family): void
    {
        //
    }

    /**
     * Handle the Family "deleted" event.
     */
    public function deleted(Family $family): void
    {
        //
    }

    /**
     * Handle the Family "restored" event.
     */
    public function restored(Family $family): void
    {
        //
    }

    /**
     * Handle the Family "force deleted" event.
     */
    public function forceDeleted(Family $family): void
    {
        //
    }
}

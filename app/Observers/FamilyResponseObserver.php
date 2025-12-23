<?php

namespace App\Observers;

use App\Models\FamilyResponse;
use Illuminate\Support\Facades\Auth;

class FamilyResponseObserver
{
    /**
     * Handle the FamilyResponse "saving" event.
     */
    public function saving(FamilyResponse $familyResponse): void
    {
        if ($familyResponse->isDirty() && !$familyResponse->isDirty('created_at')) {
            $familyResponse->updated_by_user_id = Auth::id();
        }
    }

    /**
     * Handle the FamilyResponse "deleting" event.
     */
    public function deleting(FamilyResponse $familyResponse): void
    {
        if ($familyResponse->isForceDeleting()) {
            return;
        }

        // Track who deleted
        $familyResponse->deleted_by_user_id = Auth::id();
        $familyResponse->saveQuietly();

        // Cascade soft delete all answers
        $familyResponse->answers()->each(function ($answer) {
            $answer->delete();
        });
    }

    /**
     * Handle the FamilyResponse "created" event.
     */
    public function created(FamilyResponse $familyResponse): void
    {
        //
    }

    /**
     * Handle the FamilyResponse "updated" event.
     */
    public function updated(FamilyResponse $familyResponse): void
    {
        //
    }

    /**
     * Handle the FamilyResponse "deleted" event.
     */
    public function deleted(FamilyResponse $familyResponse): void
    {
        //
    }

    /**
     * Handle the FamilyResponse "restored" event.
     */
    public function restored(FamilyResponse $familyResponse): void
    {
        //
    }

    /**
     * Handle the FamilyResponse "force deleted" event.
     */
    public function forceDeleted(FamilyResponse $familyResponse): void
    {
        //
    }
}

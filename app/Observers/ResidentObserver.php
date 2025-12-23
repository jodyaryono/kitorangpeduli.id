<?php

namespace App\Observers;

use App\Models\Resident;
use Illuminate\Support\Facades\Auth;

class ResidentObserver
{
    /**
     * Handle the Resident "saving" event.
     */
    public function saving(Resident $resident): void
    {
        if ($resident->isDirty() && !$resident->isDirty('created_at')) {
            $resident->updated_by_user_id = Auth::id();
        }
    }

    /**
     * Handle the Resident "created" event.
     */
    public function created(Resident $resident): void
    {
        //
    }

    /**
     * Handle the Resident "updated" event.
     */
    public function updated(Resident $resident): void
    {
        //
    }

    /**
     * Handle the Resident "deleted" event.
     */
    public function deleted(Resident $resident): void
    {
        //
    }

    /**
     * Handle the Resident "restored" event.
     */
    public function restored(Resident $resident): void
    {
        //
    }

    /**
     * Handle the Resident "force deleted" event.
     */
    public function forceDeleted(Resident $resident): void
    {
        //
    }
}

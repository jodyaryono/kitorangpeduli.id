<?php

namespace App\Observers;

use App\Models\Response;
use Illuminate\Support\Facades\Auth;

class ResponseObserver
{
    /**
     * Handle the Response "saving" event.
     */
    public function saving(Response $response): void
    {
        if ($response->isDirty() && !$response->isDirty('created_at')) {
            $response->updated_by_user_id = Auth::id();
        }
    }

    /**
     * Handle the Response "deleting" event.
     */
    public function deleting(Response $response): void
    {
        if ($response->isForceDeleting()) {
            return;
        }

        // Track who deleted
        $response->deleted_by_user_id = Auth::id();
        $response->saveQuietly();

        // Cascade soft delete all answers
        $response->answers()->each(function ($answer) {
            $answer->delete();
        });
    }

    /**
     * Handle the Response "created" event.
     */
    public function created(Response $response): void
    {
        //
    }

    /**
     * Handle the Response "updated" event.
     */
    public function updated(Response $response): void
    {
        //
    }

    /**
     * Handle the Response "deleted" event.
     */
    public function deleted(Response $response): void
    {
        //
    }

    /**
     * Handle the Response "restored" event.
     */
    public function restored(Response $response): void
    {
        //
    }

    /**
     * Handle the Response "force deleted" event.
     */
    public function forceDeleted(Response $response): void
    {
        //
    }
}

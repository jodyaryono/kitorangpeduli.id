<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        \App\Models\Family::observe(\App\Observers\FamilyObserver::class);
        \App\Models\Resident::observe(\App\Observers\ResidentObserver::class);
        \App\Models\Response::observe(\App\Observers\ResponseObserver::class);
        \App\Models\FamilyResponse::observe(\App\Observers\FamilyResponseObserver::class);
        \App\Models\Answer::observe(\App\Observers\AnswerObserver::class);
    }
}

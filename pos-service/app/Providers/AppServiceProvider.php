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
        // Ensure PHP timezone matches Laravel config
        date_default_timezone_set(config('app.timezone'));
        
        // Auto-load user profile data for all views
        \Illuminate\Support\Facades\View::composer(
            'layouts.app',
            \App\Http\ViewComposers\UserProfileComposer::class
        );
    }
}

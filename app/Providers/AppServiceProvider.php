<?php

namespace App\Providers;

use App\Http\Middleware\CheckEmailVerifiaction;
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
    // In app/Providers/AppServiceProvider.php

    public function boot(): void
    {
        $this->app['router']->aliasMiddleware('email_verified', CheckEmailVerifiaction::class);
    }
}

<?php

namespace App\Providers;

use Inertia\Inertia;
use Laravel\Sanctum\Sanctum;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;

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
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
          Inertia::share([
        'user' => fn () => Auth::user() ? Auth::user() : null,
        'info' => fn () => Auth::user() ? Auth::user()->info : null,
    ]);
         
    }
}

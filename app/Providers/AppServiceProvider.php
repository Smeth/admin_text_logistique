<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('superviseur', function ($user) {
            return $user->isSuperviseur();
        });

        Gate::define('responsable_agence', function ($user) {
            return $user->isResponsableAgence();
        });

        // Gate pour vérifier l'accès à une agence
        Gate::define('access-agence', function ($user, $agence) {
            return $user->peutAccederAgence($agence->id ?? $agence);
        });
    }
}

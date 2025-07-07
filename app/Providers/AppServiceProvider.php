<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL; // <-- Importa a classe URL
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
        // Garante que em produção (na Fly.io) todos os links usem https
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
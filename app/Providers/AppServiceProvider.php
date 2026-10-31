<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // Formularios públicos (contacto, libro de reclamaciones): frena bots y envíos repetidos
        RateLimiter::for('formularios-publicos', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(fn () => back()
                    ->withInput()
                    ->withErrors(['general' => 'Has enviado varios mensajes seguidos. Espera un minuto e inténtalo de nuevo.']));
        });
    }
}

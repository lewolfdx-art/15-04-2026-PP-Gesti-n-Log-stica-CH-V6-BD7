<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Rutas protegidas fuera de Filament (ej. comprobantes): sin sesión, al login del panel
        $middleware->redirectGuestsTo(fn () => route('filament.admin.auth.login'));

        // ngrok entrega las visitas desde esta misma PC: solo a esa conexión se le cree que la visita
        // vino por https, para que el CSS y los enlaces salgan con https (si no, el navegador los bloquea)
        $middleware->trustProxies(at: ['127.0.0.1', '::1']);

        // Por una dirección de ngrok solo se ve la web pública; el panel responde 404
        $middleware->prepend(\App\Http\Middleware\SoloWebEnVistaPrevia::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

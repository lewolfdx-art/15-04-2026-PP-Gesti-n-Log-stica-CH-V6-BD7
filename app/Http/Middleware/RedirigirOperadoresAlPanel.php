<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Autenticación del panel /admin: si un operador con sesión abierta entra aquí
 * (por ejemplo desde el botón "Login" de la web), lo lleva a /operador en vez de un 403.
 * Extiende el middleware de Filament porque Laravel ejecuta la autenticación antes que el resto.
 */
class RedirigirOperadoresAlPanel extends Authenticate
{
    protected function authenticate($request, array $guards): void
    {
        $usuario = Filament::auth()->user();
        $operador = Filament::getPanel('operador');

        if (
            $usuario instanceof FilamentUser
            && ! $usuario->canAccessPanel(Filament::getCurrentOrDefaultPanel())
            && $usuario->canAccessPanel($operador)
        ) {
            throw new HttpResponseException(redirect()->to($operador->getUrl()));
        }

        parent::authenticate($request, $guards);
    }
}

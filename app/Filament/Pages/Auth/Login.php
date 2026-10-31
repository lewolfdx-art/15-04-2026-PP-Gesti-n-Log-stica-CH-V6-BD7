<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Login único del sitio (botón "Login" de la web → /admin/login).
 * Los administradores entran al panel /admin y los operadores son enviados a /operador,
 * así nadie necesita recordar dos direcciones distintas.
 */
class Login extends BaseLogin
{
    protected function isUserAllowedToAccessPanel(Authenticatable $user): bool
    {
        return parent::isUserAllowedToAccessPanel($user) || $this->puedeEntrarComoOperador($user);
    }

    public function authenticate(): ?LoginResponse
    {
        $respuesta = parent::authenticate();
        $usuario = Filament::auth()->user();

        if ($respuesta && $usuario && ! parent::isUserAllowedToAccessPanel($usuario) && $this->puedeEntrarComoOperador($usuario)) {
            return new class implements LoginResponse
            {
                public function toResponse($request)
                {
                    return redirect()->to(Filament::getPanel('operador')->getUrl());
                }
            };
        }

        return $respuesta;
    }

    private function puedeEntrarComoOperador(Authenticatable $user): bool
    {
        return $user instanceof FilamentUser && $user->canAccessPanel(Filament::getPanel('operador'));
    }
}

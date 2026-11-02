<?php

namespace App\Filament\Concerns;

/**
 * Restringe un recurso o página del panel /admin a super_admin y admin.
 * El programador de vaciados (coordinador_de_vaciados) también entra al panel,
 * pero solo debe ver la Programación.
 */
trait SoloAdministradores
{
    public static function canAccess(): bool
    {
        return (auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false) && parent::canAccess();
    }
}

<?php

namespace App\Filament\Widgets\Concerns;

/**
 * Color de acento para las tarjetas de estadísticas del Escritorio
 * (borde superior y fondo del ícono). Estilos en resources/css/filament/admin/custom.css.
 */
trait ConAcento
{
    /**
     * @param  string  $color  cualquier color de App\Support\ColoresPanel (teal, violet, success...)
     */
    protected static function acento(string $color): array
    {
        return [
            'class' => 'cm-kpi',
            'style' => "--kpi: var(--{$color}-500); --kpi-suave: var(--{$color}-50); --kpi-oscuro: var(--{$color}-600)",
        ];
    }
}

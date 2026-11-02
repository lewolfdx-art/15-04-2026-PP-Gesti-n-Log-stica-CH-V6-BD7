<?php

namespace App\Support;

use Filament\Support\Colors\Color;

/**
 * Paleta de los paneles /admin y /operador.
 * Cada color queda disponible como ->color('nombre') en badges, botones, stats y gráficos,
 * y como variable CSS (--nombre-500, --nombre-100, ...).
 */
class ColoresPanel
{
    public static function paleta(): array
    {
        return [
            // Colores de la marca
            'primary' => Color::hex('#f5811e'),   // naranja Concretera Mantaro
            'marca'   => static::azulMarca(),    // azul oscuro del logo y la web (#16222e)

            // Colores de estado
            'gray'    => Color::Slate,
            'success' => Color::Emerald,
            'warning' => Color::Amber,
            'danger'  => Color::Rose,
            'info'    => Color::Sky,

            // Colores adicionales para distinguir categorías
            'violet'  => Color::Violet,
            'indigo'  => Color::Indigo,
            'teal'    => Color::Teal,
            'cyan'    => Color::Cyan,
            'lime'    => Color::Lime,
            'pink'    => Color::Pink,
            'fuchsia' => Color::Fuchsia,
            'purple'  => Color::Purple,
        ];
    }

    /**
     * #16222e es tan oscuro que Color::hex() lo toma como gris y pierde el azul;
     * esta escala conserva el tono (hue 250) con poca saturación. El 900 es el color de la marca.
     */
    private static function azulMarca(): array
    {
        $tonos = [
            50 => [0.977, 0.008], 100 => [0.950, 0.015], 200 => [0.905, 0.025], 300 => [0.840, 0.035],
            400 => [0.700, 0.045], 500 => [0.550, 0.050], 600 => [0.450, 0.050], 700 => [0.380, 0.045],
            800 => [0.310, 0.040], 900 => [0.252, 0.032], 950 => [0.200, 0.028],
        ];

        return array_map(fn (array $t): string => "oklch({$t[0]} {$t[1]} 250)", $tonos);
    }
}

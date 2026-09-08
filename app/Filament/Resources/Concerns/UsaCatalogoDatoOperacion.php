<?php

namespace App\Filament\Resources\Concerns;

use App\Models\DatoOperacion;

trait UsaCatalogoDatoOperacion
{
    /**
     * Opciones para un Select desde el catálogo DatoOperacion.
     * Fusiona los valores activos del catálogo con los valores ya usados
     * en la tabla del recurso (static::$model) para no romper los registros existentes.
     */
    private static function opcionesDatoOperacion(string $tipo, string $columna): array
    {
        $catalogo = DatoOperacion::query()
            ->where('tipo', $tipo)
            ->where('activo', true)
            ->orderBy('orden')
            ->pluck('valor', 'valor');

        $modelo = static::$model;
        $usados = $modelo::query()
            ->whereNotNull($columna)
            ->where($columna, '!=', '')
            ->distinct()
            ->pluck($columna)
            ->mapWithKeys(fn (string $valor) => [$valor => $valor]);

        return $catalogo
            ->merge($usados)
            ->sort()
            ->toArray();
    }
}

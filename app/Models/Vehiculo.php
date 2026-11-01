<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Mixers y bombas de la empresa. Un vehículo no puede estar en dos vaciados que se cruzan en horario.
 */
class Vehiculo extends Model
{
    protected $table = 'vehiculos';

    protected $fillable = ['codigo', 'tipo', 'placa', 'capacidad_m3', 'activo', 'observacion'];

    protected $casts = [
        'activo'       => 'boolean',
        'capacidad_m3' => 'decimal:2',
    ];

    public const TIPOS = [
        'mixer'              => 'Mixer',
        'bomba_telescopica'  => 'Bomba telescópica',
        'bomba_estacionaria' => 'Bomba estacionaria',
        'otro'               => 'Otro',
    ];

    public const TIPO_ICONOS = [
        'mixer'              => 'heroicon-o-truck',
        'bomba_telescopica'  => 'heroicon-o-arrow-up-right',
        'bomba_estacionaria' => 'heroicon-o-cog-6-tooth',
        'otro'               => 'heroicon-o-wrench-screwdriver',
    ];

    public function contratos(): BelongsToMany
    {
        return $this->belongsToMany(Contrato::class, 'contrato_vehiculo')->withTimestamps();
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function etiqueta(): string
    {
        $tipo = self::TIPOS[$this->tipo] ?? $this->tipo;

        return "{$this->codigo} · {$tipo}" . ($this->placa ? " ({$this->placa})" : '');
    }
}

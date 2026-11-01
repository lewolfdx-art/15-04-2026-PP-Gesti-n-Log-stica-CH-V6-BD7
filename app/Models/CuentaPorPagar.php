<?php

namespace App\Models;

use App\Observers\CuentaPorPagarObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(CuentaPorPagarObserver::class)]
class CuentaPorPagar extends Model
{
    protected $table = 'cuentas_por_pagar';

    protected $fillable = [
        'detalle',
        'tipo',
        'fecha',
        'mes',
        'año',
        'cantidad',
        'total',
        'pagado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'pagado' => 'boolean',
    ];

    /**
     * Texto legible del período de la cuenta (ej. "Fin de mes · Noviembre 2026")
     */
    public function periodoTexto(): string
    {
        $mesAño = trim(ucfirst((string) $this->mes) . ' ' . $this->año);

        return match ($this->tipo) {
            'fin_mes' => 'Fin de mes · ' . $mesAño,
            'mes'     => $mesAño,
            'viajes'  => 'Viajes' . ($this->fecha ? ' · ' . $this->fecha->format('d/m/Y') : ''),
            default   => $this->fecha?->format('d/m/Y') ?? ucfirst((string) $this->tipo),
        };
    }

    /**
     * Etiqueta del tipo de gasto
     */
    public static function tipoTexto(?string $tipo): string
    {
        return match ($tipo) {
            'fecha'   => 'Por fecha',
            'mes'     => 'Mensual',
            'fin_mes' => 'Fin de mes',
            'viajes'  => 'Viajes',
            default   => ucfirst((string) $tipo) ?: 'Sin tipo',
        };
    }
}
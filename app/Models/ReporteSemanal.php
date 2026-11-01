<?php

namespace App\Models;

use App\Observers\ReporteSemanalObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(ReporteSemanalObserver::class)]
class ReporteSemanal extends Model
{
    use HasFactory;

    protected $table = 'reportes_semanales';

    protected $fillable = [
        'tipo_periodo',
        'semana',
        'fecha_desde',
        'fecha_hasta',
        'quincena',
        'mes',
        'año',
        'fecha_desde_quincena',
        'fecha_hasta_quincena',
        'fecha_fin_mes',
        'proveedor',
        'detalle',
        'monto',
        'estado',
    ];

    protected $casts = [
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
        'fecha_desde_quincena' => 'date',
        'fecha_hasta_quincena' => 'date',
        'fecha_fin_mes' => 'date',
        'monto' => 'decimal:2',
    ];

    /**
     * Texto legible del período del reporte (ej. "Semana 43 · 20/10 – 26/10/2026")
     */
    public function periodoTexto(): string
    {
        $mesAño = $this->mes
            ? ucfirst(\Carbon\Carbon::create()->month((int) $this->mes)->locale('es')->monthName) . ' ' . $this->año
            : (string) $this->año;

        return match ($this->tipo_periodo) {
            'semanal'  => "Semana {$this->semana}" . ($this->fecha_desde && $this->fecha_hasta
                ? ' · ' . $this->fecha_desde->format('d/m') . ' – ' . $this->fecha_hasta->format('d/m/Y')
                : ''),
            'quincena' => ($this->quincena === 'PRIMERA' ? '1ra quincena' : '2da quincena') . ' · ' . $mesAño,
            'fin_mes'  => 'Fin de mes · ' . ($this->fecha_fin_mes?->format('d/m/Y') ?? $mesAño),
            default    => ucfirst((string) $this->tipo_periodo),
        };
    }

    /**
     * Etiqueta del tipo de período
     */
    public static function tipoPeriodoTexto(?string $tipo): string
    {
        return match ($tipo) {
            'semanal'  => 'Semanal',
            'quincena' => 'Quincenal',
            'fin_mes'  => 'Fin de mes',
            default    => ucfirst((string) $tipo) ?: 'Sin período',
        };
    }
}
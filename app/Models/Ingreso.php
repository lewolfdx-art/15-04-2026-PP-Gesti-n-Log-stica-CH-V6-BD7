<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Ingreso extends Model
{
    use HasFactory;

    protected $table = 'ingresos';

    protected $fillable = [
        'fecha',
        'modo_pago',
        'numero_contrato',
        'asesor',
        'detalle',
        'monto',
        'banco',
        'obs',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    // Opcional: Orden por defecto
    protected $attributes = [
        'fecha' => null,
    ];

    // ==================== PROCEDIMIENTOS ALMACENADOS ====================

    /**
     * Ejecuta un procedimiento almacenado con parámetros
     *
     * @param string $nombreProcedimiento
     * @param array $parametros
     * @return array
     */
    private static function ejecutarProcedimiento($nombreProcedimiento, $parametros = [])
    {
        $placeholders = implode(',', array_fill(0, count($parametros), '?'));
        return DB::select("CALL {$nombreProcedimiento}({$placeholders})", $parametros);
    }

    /**
     * Obtener ingresos por rango de fechas
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public static function spIngresosPorRangoFechas($fechaInicio, $fechaFin)
    {
        return self::ejecutarProcedimiento('sp_ingresos_por_rango_fechas', [$fechaInicio, $fechaFin]);
    }

    /**
     * Obtener ingresos por modo de pago
     *
     * @param string $modoPago
     * @return array
     */
    public static function spIngresosPorModoPago($modoPago)
    {
        return self::ejecutarProcedimiento('sp_ingresos_por_modo_pago', [$modoPago]);
    }

    /**
     * Obtener ingresos por asesor
     *
     * @param string $asesor
     * @return array
     */
    public static function spIngresosPorAsesor($asesor)
    {
        return self::ejecutarProcedimiento('sp_ingresos_por_asesor', [$asesor]);
    }

    /**
     * Obtener ingresos por banco
     *
     * @param string $banco
     * @return array
     */
    public static function spIngresosPorBanco($banco)
    {
        return self::ejecutarProcedimiento('sp_ingresos_por_banco', [$banco]);
    }

    /**
     * Obtener total de ingresos por mes y año
     *
     * @param int $mes
     * @param int $año
     * @return array
     */
    public static function spTotalIngresosPorMes($mes, $año)
    {
        return self::ejecutarProcedimiento('sp_total_ingresos_por_mes', [$mes, $año]);
    }

    /**
     * Obtener resumen de ingresos por modo de pago
     *
     * @return array
     */
    public static function spResumenIngresosPorModoPago()
    {
        return self::ejecutarProcedimiento('sp_resumen_ingresos_por_modo_pago');
    }

    /**
     * Obtener ingresos del mes actual
     *
     * @return array
     */
    public static function spIngresosMesActual()
    {
        return self::ejecutarProcedimiento('sp_ingresos_mes_actual');
    }

    /**
     * Obtener total de ingresos por asesor
     *
     * @return array
     */
    public static function spTotalIngresosPorAsesor()
    {
        return self::ejecutarProcedimiento('sp_total_ingresos_por_asesor');
    }

    /**
     * Obtener top 10 ingresos más altos
     *
     * @return array
     */
    public static function spTopIngresosMasAltos()
    {
        return self::ejecutarProcedimiento('sp_top_ingresos_mas_altos');
    }
}
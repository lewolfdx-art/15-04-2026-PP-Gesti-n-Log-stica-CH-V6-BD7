<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
     * Obtener reportes por rango de fechas
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public static function spReportesPorRangoFechas($fechaInicio, $fechaFin)
    {
        return self::ejecutarProcedimiento('sp_reportes_por_rango_fechas', [$fechaInicio, $fechaFin]);
    }

    /**
     * Obtener reportes por semana
     *
     * @param int $semana
     * @param int $año
     * @return array
     */
    public static function spReportesPorSemana($semana, $año)
    {
        return self::ejecutarProcedimiento('sp_reportes_por_semana', [$semana, $año]);
    }

    /**
     * Obtener reportes por mes
     *
     * @param int $mes
     * @param int $año
     * @return array
     */
    public static function spReportesPorMes($mes, $año)
    {
        return self::ejecutarProcedimiento('sp_reportes_por_mes', [$mes, $año]);
    }

    /**
     * Obtener reportes por quincena
     *
     * @param int $quincena
     * @param int $mes
     * @param int $año
     * @return array
     */
    public static function spReportesPorQuincena($quincena, $mes, $año)
    {
        return self::ejecutarProcedimiento('sp_reportes_por_quincena', [$quincena, $mes, $año]);
    }

    /**
     * Obtener reportes por proveedor
     *
     * @param string $proveedor
     * @return array
     */
    public static function spReportesPorProveedor($proveedor)
    {
        return self::ejecutarProcedimiento('sp_reportes_por_proveedor', [$proveedor]);
    }

    /**
     * Obtener total de reportes por tipo de período
     *
     * @return array
     */
    public static function spTotalReportesPorTipoPeriodo()
    {
        return self::ejecutarProcedimiento('sp_total_reportes_por_tipo_periodo');
    }

    /**
     * Obtener resumen de montos por estado
     *
     * @return array
     */
    public static function spResumenMontosPorEstado()
    {
        return self::ejecutarProcedimiento('sp_resumen_montos_por_estado');
    }

    /**
     * Obtener reportes del año actual
     *
     * @return array
     */
    public static function spReportesAnioActual()
    {
        return self::ejecutarProcedimiento('sp_reportes_anio_actual');
    }

    /**
     * Obtener top reportes por monto
     *
     * @param int $limite
     * @return array
     */
    public static function spTopReportesPorMonto($limite = 10)
    {
        return self::ejecutarProcedimiento('sp_top_reportes_por_monto', [$limite]);
    }
}
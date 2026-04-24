<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
     * Obtener cuentas por pagar por rango de fechas
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public static function spCuentasPorPagarPorRangoFechas($fechaInicio, $fechaFin)
    {
        return self::ejecutarProcedimiento('sp_cuentas_por_pagar_rango_fechas', [$fechaInicio, $fechaFin]);
    }

    /**
     * Obtener cuentas por pagar por tipo
     *
     * @param string $tipo
     * @return array
     */
    public static function spCuentasPorPagarPorTipo($tipo)
    {
        return self::ejecutarProcedimiento('sp_cuentas_por_pagar_por_tipo', [$tipo]);
    }

    /**
     * Obtener total de cuentas por pagar por mes y año
     *
     * @param int $mes
     * @param int $año
     * @return array
     */
    public static function spTotalCuentasPorPagarPorMes($mes, $año)
    {
        return self::ejecutarProcedimiento('sp_total_cuentas_por_pagar_mes', [$mes, $año]);
    }

    /**
     * Obtener cuentas por pagar pendientes (no pagadas)
     *
     * @return array
     */
    public static function spCuentasPorPagarPendientes()
    {
        return self::ejecutarProcedimiento('sp_cuentas_por_pagar_pendientes');
    }

    /**
     * Obtener resumen de cuentas por pagar agrupadas por tipo
     *
     * @return array
     */
    public static function spResumenCuentasPorPagarPorTipo()
    {
        return self::ejecutarProcedimiento('sp_resumen_cuentas_por_pagar_tipo');
    }

    /**
     * Marcar cuenta por pagar como pagada
     *
     * @param int $cuentaId
     * @return array
     */
    public static function spMarcarCuentaComoPagada($cuentaId)
    {
        return self::ejecutarProcedimiento('sp_marcar_cuenta_pagada', [$cuentaId]);
    }

    /**
     * Obtener cuentas por pagar por año
     *
     * @param int $año
     * @return array
     */
    public static function spCuentasPorPagarPorAño($año)
    {
        return self::ejecutarProcedimiento('sp_cuentas_por_pagar_por_año', [$año]);
    }

    /**
     * Obtener total general de cuentas por pagar (pagadas vs pendientes)
     *
     * @return array
     */
    public static function spTotalGeneralCuentasPorPagar()
    {
        return self::ejecutarProcedimiento('sp_total_general_cuentas_por_pagar');
    }
}
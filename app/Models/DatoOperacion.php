<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DatoOperacion extends Model
{
    protected $fillable = [
        'tipo', 
        'valor', 
        'descripcion', 
        'orden', 
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden'  => 'integer',
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
     * Obtener datos de operación por tipo
     *
     * @param string $tipo
     * @return array
     */
    public static function spDatosOperacionPorTipo($tipo)
    {
        return self::ejecutarProcedimiento('sp_datos_operacion_por_tipo', [$tipo]);
    }

    /**
     * Obtener datos de operación activos
     *
     * @return array
     */
    public static function spDatosOperacionActivos()
    {
        return self::ejecutarProcedimiento('sp_datos_operacion_activos');
    }

    /**
     * Obtener datos de operación ordenados
     *
     * @return array
     */
    public static function spDatosOperacionOrdenados()
    {
        return self::ejecutarProcedimiento('sp_datos_operacion_ordenados');
    }

    /**
     * Obtener valor específico por tipo y descripción
     *
     * @param string $tipo
     * @param string $descripcion
     * @return array
     */
    public static function spValorDatoOperacion($tipo, $descripcion)
    {
        return self::ejecutarProcedimiento('sp_valor_dato_operacion', [$tipo, $descripcion]);
    }

    /**
     * Obtener datos de operación por rango de orden
     *
     * @param int $ordenMin
     * @param int $ordenMax
     * @return array
     */
    public static function spDatosOperacionPorRangoOrden($ordenMin, $ordenMax)
    {
        return self::ejecutarProcedimiento('sp_datos_operacion_por_rango_orden', [$ordenMin, $ordenMax]);
    }

    /**
     * Activar o desactivar un dato de operación
     *
     * @param int $id
     * @param bool $activo
     * @return array
     */
    public static function spCambiarEstadoDatoOperacion($id, $activo)
    {
        return self::ejecutarProcedimiento('sp_cambiar_estado_dato_operacion', [$id, $activo]);
    }

    /**
     * Obtener tipos distintos de datos de operación
     *
     * @return array
     */
    public static function spTiposDatosOperacion()
    {
        return self::ejecutarProcedimiento('sp_tipos_datos_operacion');
    }
}
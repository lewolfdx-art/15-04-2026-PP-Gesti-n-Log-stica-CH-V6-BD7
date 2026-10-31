<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Contrato extends Model
{
    use HasFactory;

    protected $table = 'contratos';

    protected $fillable = [
        'fecha',
        'ubicacion_referencia',
        'estructura',
        'nombre_cliente',
        'nombre_vendedor',
        'numero_contrato',
        'tipo_concreto',
        'guia_remision',
        'factura',
        'bombeo',
        'bomba',
        'bomba_adicional',
        'volumen_guia',
        'volumen_real',
        'volumen_sobrante',
        'descuento_guias',
        'pu',
        'monto_total',
        'estado_pago',
        'observacion',
        'comision',
        'comision_igv',
        'gastos_alimentos_bomba',
        'descuentos',
        'venta_neta',
        'pu_real',
        'alimentos_dc',
        'total_para_empresa',
        'observaciones_adicionales',
        'forma_pago',
        'cancelacion',
        'cliente_id',
        'vendedor_id',
        'gasto_categoria_id',
        'activo',
        'latitud',
        'longitud',
        'descripcion_lugar',
        'fotos_lugar',
        'viabilidad',
        'viabilidad_comentario',
    ];

    public const VIABILIDADES = [
        'viable'        => '✅ Viable',
        'observaciones' => '⚠️ Viable con observaciones',
        'no_viable'     => '❌ No viable',
    ];

    public const VIABILIDAD_COLORES = [
        'viable'        => 'success',
        'observaciones' => 'warning',
        'no_viable'     => 'danger',
    ];

    protected $casts = [
        'fecha'                    => 'date',
        'bomba'                    => 'decimal:2',
        'bomba_adicional'          => 'decimal:2',
        'volumen_guia'             => 'decimal:2',
        'volumen_real'             => 'decimal:2',
        'volumen_sobrante'         => 'decimal:2',
        'descuento_guias'          => 'decimal:2',
        'pu'                       => 'decimal:2',
        'monto_total'              => 'decimal:2',
        'comision'                 => 'decimal:2',
        'comision_igv'             => 'decimal:2',
        'gastos_alimentos_bomba'   => 'decimal:2',
        'descuentos'               => 'decimal:2',
        'venta_neta'               => 'decimal:2',
        'pu_real'                  => 'decimal:2',
        'alimentos_dc'             => 'decimal:2',
        'total_para_empresa'       => 'decimal:2',
        'activo'                   => 'boolean',
        'latitud'                  => 'float',
        'longitud'                 => 'float',
        'fotos_lugar'              => 'array',
    ];

    /**
     * Operadores (trabajadores) asignados a la obra
     */
    public function operadores(): BelongsToMany
    {
        return $this->belongsToMany(Trabajador::class, 'contrato_operador')
            ->withPivot('notificado_at')
            ->withTimestamps();
    }

    public function tieneUbicacion(): bool
    {
        return $this->latitud !== null && $this->longitud !== null;
    }

    /**
     * Enlace de Google Maps con la ruta hasta la obra
     */
    public function urlComoLlegar(): ?string
    {
        return $this->tieneUbicacion()
            ? "https://www.google.com/maps/dir/?api=1&destination={$this->latitud},{$this->longitud}"
            : null;
    }

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
     * Obtener contratos por cliente
     *
     * @param int $clienteId
     * @return array
     */
    public static function spContratosPorCliente($clienteId)
    {
        return self::ejecutarProcedimiento('sp_contratos_por_cliente', [$clienteId]);
    }

    /**
     * Obtener contratos por vendedor
     *
     * @param int $vendedorId
     * @return array
     */
    public static function spContratosPorVendedor($vendedorId)
    {
        return self::ejecutarProcedimiento('sp_contratos_por_vendedor', [$vendedorId]);
    }

    /**
     * Obtener contratos por rango de fechas
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public static function spContratosPorRangoFechas($fechaInicio, $fechaFin)
    {
        return self::ejecutarProcedimiento('sp_contratos_por_rango_fechas', [$fechaInicio, $fechaFin]);
    }

    /**
     * Obtener total de ventas por vendedor en un mes
     *
     * @param int $vendedorId
     * @param int $mes
     * @param int $año
     * @return array
     */
    public static function spTotalVentasPorVendedor($vendedorId, $mes, $año)
    {
        return self::ejecutarProcedimiento('sp_total_ventas_por_vendedor', [$vendedorId, $mes, $año]);
    }

    /**
     * Obtener resumen de contratos (agrupado por estado de pago)
     *
     * @return array
     */
    public static function spResumenContratosPorEstado()
    {
        return self::ejecutarProcedimiento('sp_resumen_contratos_por_estado');
    }

    /**
     * Actualizar estado de pago de un contrato
     *
     * @param int $contratoId
     * @param string $estadoPago
     * @return array
     */
    public static function spActualizarEstadoPago($contratoId, $estadoPago)
    {
        return self::ejecutarProcedimiento('sp_actualizar_estado_pago', [$contratoId, $estadoPago]);
    }

    /**
     * Obtener comisiones por vendedor en un periodo
     *
     * @param int $vendedorId
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public static function spComisionesPorVendedor($vendedorId, $fechaInicio, $fechaFin)
    {
        return self::ejecutarProcedimiento('sp_comisiones_por_vendedor', [$vendedorId, $fechaInicio, $fechaFin]);
    }

    /**
     * Obtener contratos con detalles de cliente y vendedor
     *
     * @return array
     */
    public static function spContratosConDetalles()
    {
        return self::ejecutarProcedimiento('sp_contratos_con_detalles');
    }

    /**
     * Buscar contratos por número de contrato o factura
     *
     * @param string $termino
     * @return array
     */
    public static function spBuscarContratos($termino)
    {
        return self::ejecutarProcedimiento('sp_buscar_contratos', [$termino]);
    }

    /**
     * Obtener contratos pendientes de pago
     *
     * @return array
     */
    public static function spContratosPendientesPago()
    {
        return self::ejecutarProcedimiento('sp_contratos_pendientes_pago');
    }

    /**
     * Obtener ventas netas por cliente
     *
     * @param int $clienteId
     * @return array
     */
    public static function spVentasNetasPorCliente($clienteId)
    {
        return self::ejecutarProcedimiento('sp_ventas_netas_por_cliente', [$clienteId]);
    }
}
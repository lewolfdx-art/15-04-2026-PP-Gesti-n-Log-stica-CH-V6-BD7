<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Gasto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gastos';

    protected $fillable = [
        'fecha',
        'grupo',
        'subcategoria',
        'responsable',
        'detalle',
        'importe',
        'obs',
    ];

    protected $casts = [
        'fecha'   => 'date',
        'importe' => 'decimal:2',
    ];

    // ==================== RELACIONES ====================

    /**
     * Relación con la categoría de gasto (principal)
     */
    public function categoria()
    {
        return $this->belongsTo(GastoCategoria::class, 'gasto_categoria_id');
    }

    // ==================== ACCESSORS / MUTATORS ====================

    /**
     * Accesor para mostrar la categoría completa (ej: "MATERIA_PRIMA - CEMENTO GRANEL")
     */
    public function getCategoriaCompletaAttribute()
    {
        if ($this->categoria && $this->subcategoria) {
            return $this->categoria->nombre . ' - ' . $this->subcategoria;
        }
        return $this->categoria?->nombre ?? $this->subcategoria ?? 'Sin categoría';
    }

    /**
     * Formato bonito del importe con S/
     */
    public function getImporteFormateadoAttribute()
    {
        return 'S/ ' . number_format($this->importe, 2, '.', ',');
    }

    // ==================== SCOPES ====================

    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function scopeDelMes($query, $mes, $año = null)
    {
        $año = $año ?? now()->year;
        return $query->whereYear('fecha', $año)
                     ->whereMonth('fecha', $mes);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('gasto_categoria_id', $categoriaId);
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
     * Obtener gastos por rango de fechas
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public static function spGastosPorRangoFechas($fechaInicio, $fechaFin)
    {
        return self::ejecutarProcedimiento('sp_gastos_por_rango_fechas', [$fechaInicio, $fechaFin]);
    }

    /**
     * Obtener gastos por grupo
     *
     * @param string $grupo
     * @return array
     */
    public static function spGastosPorGrupo($grupo)
    {
        return self::ejecutarProcedimiento('sp_gastos_por_grupo', [$grupo]);
    }

    /**
     * Obtener gastos por responsable
     *
     * @param string $responsable
     * @return array
     */
    public static function spGastosPorResponsable($responsable)
    {
        return self::ejecutarProcedimiento('sp_gastos_por_responsable', [$responsable]);
    }

    /**
     * Obtener total de gastos por mes y año
     *
     * @param int $mes
     * @param int $año
     * @return array
     */
    public static function spTotalGastosPorMes($mes, $año)
    {
        return self::ejecutarProcedimiento('sp_total_gastos_por_mes', [$mes, $año]);
    }

    /**
     * Obtener resumen de gastos por grupo
     *
     * @return array
     */
    public static function spResumenGastosPorGrupo()
    {
        return self::ejecutarProcedimiento('sp_resumen_gastos_por_grupo');
    }

    /**
     * Obtener gastos del mes actual
     *
     * @return array
     */
    public static function spGastosMesActual()
    {
        return self::ejecutarProcedimiento('sp_gastos_mes_actual');
    }

    /**
     * Obtener gastos por categoría
     *
     * @param int $categoriaId
     * @return array
     */
    public static function spGastosPorCategoria($categoriaId)
    {
        return self::ejecutarProcedimiento('sp_gastos_por_categoria', [$categoriaId]);
    }

    /**
     * Obtener top 10 gastos más altos
     *
     * @return array
     */
    public static function spTopGastosMasAltos()
    {
        return self::ejecutarProcedimiento('sp_top_gastos_mas_altos');
    }
}
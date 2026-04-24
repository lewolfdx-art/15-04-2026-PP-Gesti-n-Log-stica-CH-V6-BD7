<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GastoCategoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'grupo', 'parent_id', 'orden', 'activo'
    ];

    public function parent()
    {
        return $this->belongsTo(GastoCategoria::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(GastoCategoria::class, 'parent_id');
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
     * Obtener categorías por grupo
     *
     * @param string $grupo
     * @return array
     */
    public static function spCategoriasPorGrupo($grupo)
    {
        return self::ejecutarProcedimiento('sp_categorias_por_grupo', [$grupo]);
    }

    /**
     * Obtener categorías activas
     *
     * @return array
     */
    public static function spCategoriasActivas()
    {
        return self::ejecutarProcedimiento('sp_categorias_activas');
    }

    /**
     * Obtener categorías principales (sin padre)
     *
     * @return array
     */
    public static function spCategoriasPrincipales()
    {
        return self::ejecutarProcedimiento('sp_categorias_principales');
    }

    /**
     * Obtener subcategorías por categoría padre
     *
     * @param int $parentId
     * @return array
     */
    public static function spSubcategoriasPorPadre($parentId)
    {
        return self::ejecutarProcedimiento('sp_subcategorias_por_padre', [$parentId]);
    }

    /**
     * Obtener grupos distintos de categorías
     *
     * @return array
     */
    public static function spGruposCategorias()
    {
        return self::ejecutarProcedimiento('sp_grupos_categorias');
    }

    /**
     * Obtener árbol completo de categorías
     *
     * @return array
     */
    public static function spArbolCategorias()
    {
        return self::ejecutarProcedimiento('sp_arbol_categorias');
    }

    /**
     * Contar gastos por categoría
     *
     * @param int $categoriaId
     * @return array
     */
    public static function spContarGastosPorCategoria($categoriaId)
    {
        return self::ejecutarProcedimiento('sp_contar_gastos_por_categoria', [$categoriaId]);
    }

    /**
     * Buscar categorías por nombre
     *
     * @param string $termino
     * @return array
     */
    public static function spBuscarCategorias($termino)
    {
        return self::ejecutarProcedimiento('sp_buscar_categorias', [$termino]);
    }
}
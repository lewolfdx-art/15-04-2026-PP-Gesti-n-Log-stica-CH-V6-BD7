<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
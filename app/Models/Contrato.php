<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

    protected $table = 'contratos';

    protected $fillable = [
        'fecha',
        'estructura',
        'nombre_cliente',        // por ahora guardamos como texto
        'nombre_vendedor',       // por ahora guardamos como texto
        'numero_contrato',
        'tipo_concreto',
        'guia_remision',
        'factura',
        'bombeo',
        'bomba_adicional',
        'volumen_real',
        'volumen_guia',
        'volumen_sobrante',
        'pu',
        'monto_total',
        'descuento_guias',
        'venta_neta',
        'pu_real',
        'estado_pago',
        'forma_pago',
        'cancelacion',
        'observacion',
        'observaciones_adicionales',
        'comision',
        'comision_igv',
        'total_para_empresa',
        'activo',
    ];

    protected $casts = [
        'fecha'                  => 'date',
        'pu'                     => 'decimal:2',
        'monto_total'            => 'decimal:2',
        'venta_neta'             => 'decimal:2',
        'pu_real'                => 'decimal:2',
        'bomba_adicional'        => 'decimal:2',
        'volumen_real'           => 'decimal:2',
        'volumen_guia'           => 'decimal:2',
        'volumen_sobrante'       => 'decimal:2',
        'comision'               => 'decimal:2',
        'comision_igv'           => 'decimal:2',
        'total_para_empresa'     => 'decimal:2',
        'activo'                 => 'boolean',
    ];

    // Relaciones comentadas por ahora (las activaremos después)
    /*
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
    */
}
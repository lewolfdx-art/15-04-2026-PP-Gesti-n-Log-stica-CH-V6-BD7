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
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ingreso extends Model
{
    use HasFactory;

    protected $table = 'ingresos';

    protected $fillable = [
        'fecha',
        'modo_pago',
        'numero_contrato',
        'asesor',
        'detalle',
        'monto',
        'banco',
        'obs',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];

    // Opcional: Orden por defecto
    protected $attributes = [
        'fecha' => null,
    ];
}
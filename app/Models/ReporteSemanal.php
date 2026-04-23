<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteSemanal extends Model
{
    use HasFactory;

    protected $table = 'reportes_semanales';

    protected $fillable = [
        'tipo_periodo',
        'semana',
        'fecha_desde',
        'fecha_hasta',
        'quincena',
        'mes',
        'anio',
        'fecha_desde_quincena',
        'fecha_hasta_quincena',
        'fecha_fin_mes',
        'proveedor',
        'detalle',
        'monto',
        'estado',
    ];

    protected $casts = [
        'fecha_desde' => 'date',
        'fecha_hasta' => 'date',
        'fecha_desde_quincena' => 'date',
        'fecha_hasta_quincena' => 'date',
        'fecha_fin_mes' => 'date',
        'monto' => 'decimal:2',
    ];
}
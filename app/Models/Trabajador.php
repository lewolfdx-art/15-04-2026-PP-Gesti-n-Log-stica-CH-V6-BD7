<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores';

    protected $fillable = [
        'tipo_cargo',
        'nombre_completo',
        'dni',
        'fecha_nacimiento',
        'descripcion',
        'orden',
        'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    // Validación adicional en el modelo
    public static function boot()
    {
        parent::boot();

        static::saving(function ($trabajador) {
            if ($trabajador->dni) {
                $trabajador->dni = preg_replace('/[^0-9]/', '', $trabajador->dni); // solo números
                if (strlen($trabajador->dni) !== 8) {
                    throw new \Exception('El DNI debe tener exactamente 8 dígitos.');
                }
            }
        });
    }
}
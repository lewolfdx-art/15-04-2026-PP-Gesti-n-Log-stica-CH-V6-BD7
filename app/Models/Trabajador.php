<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'user_id',
        'sueldo_mensual',
    ];

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoPersonal::class);
    }

    /**
     * Usuario con el que entra al panel de operadores (opcional)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obras (contratos) a las que está asignado como operador
     */
    public function obras(): BelongsToMany
    {
        return $this->belongsToMany(Contrato::class, 'contrato_operador')
            ->withPivot('notificado_at')
            ->withTimestamps();
    }

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
        'orden' => 'integer',
        'sueldo_mensual' => 'decimal:2',
    ];

    /**
     * Los asesores de ventas comparten la tabla con el personal,
     * pero se administran en su propio módulo (AsesorVentasResource).
     */
    public const CARGO_ASESOR = 'asesor_ventas';

    /**
     * Cargos del personal operativo y administrativo (sin asesores de ventas)
     */
    public const CARGOS_PERSONAL = [
        'operador_planta'            => 'Operador de Planta',
        'operador_retroexcavadora'   => 'Operador de Retroexcavadora',
        'ayudante_planta'            => 'Ayudante de Planta',
        'operador_bomba_telescopica' => 'Operador de Bomba Telescópica',
        'ayudante_manguerero_bt'     => 'Ayudante Manguerero B.T',
        'ayudante_vibrador_bt'       => 'Ayudante Vibrador B.T',
        'jefe_mantenimiento'         => 'Jefe de Mantenimiento',
        'operador_mixer'             => 'Operador de Mixer',
        'operador_trayler'           => 'Operador de Trayler',
        'ayudante_bomba'             => 'Ayudante de Bomba',
        'asistente_calidad'          => 'Asistente de Calidad',
        'administracion'             => 'Administración',
        'asistente_administrativo'   => 'Asistente Administrativo',
        'planeamiento'               => 'Planeamiento',
        'seguridad_noche'            => 'Seguridad de Noche',
        'contador'                   => 'Contador',
        'personal_curado'            => 'Personal de Curado',
    ];

    public function scopeAsesores(Builder $query): Builder
    {
        return $query->where('tipo_cargo', self::CARGO_ASESOR);
    }

    public function scopePersonal(Builder $query): Builder
    {
        return $query->where('tipo_cargo', '!=', self::CARGO_ASESOR);
    }

    public static function cargoTexto(?string $cargo): string
    {
        if ($cargo === self::CARGO_ASESOR) {
            return 'Asesor de Ventas';
        }

        return self::CARGOS_PERSONAL[$cargo] ?? ucwords(str_replace('_', ' ', (string) $cargo));
    }

    // Validación adicional en el modelo
    public static function boot()
    {
        parent::boot();

        static::saving(function ($trabajador) {
            if ($trabajador->dni) {
                $trabajador->dni = preg_replace('/[^0-9]/', '', $trabajador->dni); // solo números
                if (strlen($trabajador->dni) !== 8) {
                    throw new Exception('El DNI debe tener exactamente 8 dígitos.');
                }
            }
        });
    }
}
<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

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
    ];

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
        'operador_planta'            => '👷 Operador de Planta',
        'operador_retroexcavadora'   => '🚜 Operador de Retroexcavadora',
        'ayudante_planta'            => '👷 Ayudante de Planta',
        'operador_bomba_telescopica' => '🚧 Operador de Bomba Telescópica',
        'ayudante_manguerero_bt'     => '👷 Ayudante Manguerero B.T',
        'ayudante_vibrador_bt'       => '👷 Ayudante Vibrador B.T',
        'jefe_mantenimiento'         => '🛠️ Jefe de Mantenimiento',
        'operador_mixer'             => '🚛 Operador de Mixer',
        'operador_trayler'           => '🚛 Operador de Trayler',
        'ayudante_bomba'             => '👷 Ayudante de Bomba',
        'asistente_calidad'          => '📋 Asistente de Calidad',
        'administracion'             => '🏢 Administración',
        'asistente_administrativo'   => '📑 Asistente Administrativo',
        'planeamiento'               => '📊 Planeamiento',
        'seguridad_noche'            => '🌙 Seguridad de Noche',
        'contador'                   => '💰 Contador',
        'personal_curado'            => '👷 Personal de Curado',
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
            return '💼 Asesor de Ventas';
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
     * Obtener trabajadores por tipo de cargo
     *
     * @param string $tipoCargo
     * @return array
     */
    public static function spTrabajadoresPorTipoCargo($tipoCargo)
    {
        return self::ejecutarProcedimiento('sp_trabajadores_por_tipo_cargo', [$tipoCargo]);
    }

    /**
     * Obtener trabajadores activos
     *
     * @return array
     */
    public static function spTrabajadoresActivos()
    {
        return self::ejecutarProcedimiento('sp_trabajadores_activos');
    }

    /**
     * Obtener trabajadores ordenados
     *
     * @return array
     */
    public static function spTrabajadoresOrdenados()
    {
        return self::ejecutarProcedimiento('sp_trabajadores_ordenados');
    }

    /**
     * Buscar trabajadores por nombre o DNI
     *
     * @param string $termino
     * @return array
     */
    public static function spBuscarTrabajadores($termino)
    {
        return self::ejecutarProcedimiento('sp_buscar_trabajadores', [$termino]);
    }

    /**
     * Obtener trabajador por DNI
     *
     * @param string $dni
     * @return array
     */
    public static function spTrabajadorPorDni($dni)
    {
        return self::ejecutarProcedimiento('sp_trabajador_por_dni', [$dni]);
    }

    /**
     * Obtener tipos de cargo distintos
     *
     * @return array
     */
    public static function spTiposCargo()
    {
        return self::ejecutarProcedimiento('sp_tipos_cargo');
    }

    /**
     * Activar o desactivar trabajador
     *
     * @param int $id
     * @param bool $activo
     * @return array
     */
    public static function spCambiarEstadoTrabajador($id, $activo)
    {
        return self::ejecutarProcedimiento('sp_cambiar_estado_trabajador', [$id, $activo]);
    }

    /**
     * Obtener trabajadores con cumpleaños en el mes actual
     *
     * @return array
     */
    public static function spCumpleaniosMesActual()
    {
        return self::ejecutarProcedimiento('sp_cumpleanios_mes_actual');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'latitud',
        'longitud',
        'descripcion_lugar',
        'fotos_lugar',
        'viabilidad',
        'viabilidad_comentario',
        'cotizacion_id',
        'hora_inicio',
        'hora_fin',
    ];

    // Opciones compartidas por contratos y cotizaciones
    public const ESTRUCTURAS = [
        'CANAL' => 'CANAL', 'CIMIENTO' => 'CIMIENTO', 'COLUMNAS' => 'COLUMNAS',
        'FALSO PISO' => 'FALSO PISO', 'LOSA ALIGERADA' => 'LOSA ALIGERADA',
        'PAVIMENTO' => 'PAVIMENTO', 'PISO' => 'PISO',
        'PLACAS Y COLUMNAS' => 'PLACAS Y COLUMNAS', 'PLATEA' => 'PLATEA',
        'VEREDAS' => 'VEREDAS', 'VIGAS DE CIMENTACION' => 'VIGAS DE CIMENTACION',
        'ZAPATAS' => 'ZAPATAS', 'ZAPATAS C/CORTE' => 'ZAPATAS C/CORTE',
    ];

    public const TIPOS_CONCRETO = ['145' => '145', '175' => '175', '210' => '210', '245' => '245', '280' => '280'];

    public const BOMBEOS = ['C/BOMBA' => 'C/BOMBA', 'S/BOMBA' => 'S/BOMBA', 'ESTACIONARIA' => 'ESTACIONARIA'];

    public const VIABILIDADES = [
        'viable'        => 'Viable',
        'observaciones' => 'Viable con observaciones',
        'no_viable'     => 'No viable',
    ];

    public const VIABILIDAD_COLORES = [
        'viable'        => 'success',
        'observaciones' => 'warning',
        'no_viable'     => 'danger',
    ];

    public const VIABILIDAD_ICONOS = [
        'viable'        => 'heroicon-o-check-circle',
        'observaciones' => 'heroicon-o-exclamation-triangle',
        'no_viable'     => 'heroicon-o-x-circle',
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
        'latitud'                  => 'float',
        'longitud'                 => 'float',
        'fotos_lugar'              => 'array',
    ];

    /**
     * Operadores (trabajadores) asignados a la obra
     */
    public function operadores(): BelongsToMany
    {
        return $this->belongsToMany(Trabajador::class, 'contrato_operador')
            ->withPivot('notificado_at')
            ->withTimestamps();
    }

    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Pago al personal con el que se liquidó la comisión del asesor
     */
    public function pagoComision(): BelongsTo
    {
        return $this->belongsTo(PagoPersonal::class, 'pago_comision_id');
    }

    /**
     * Mixers y bombas asignados al vaciado
     */
    public function vehiculos(): BelongsToMany
    {
        return $this->belongsToMany(Vehiculo::class, 'contrato_vehiculo')->withTimestamps();
    }

    public function programadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'programado_por');
    }

    public function estaProgramado(): bool
    {
        return $this->programado_at !== null;
    }

    public function horarioTexto(): ?string
    {
        if (! $this->hora_inicio) {
            return null;
        }

        return substr($this->hora_inicio, 0, 5) . ($this->hora_fin ? ' – ' . substr($this->hora_fin, 0, 5) : '');
    }

    /**
     * Otros vaciados del mismo día cuyo horario se cruza y que usan alguno de esos vehículos.
     * Dos horarios se cruzan si uno empieza antes de que el otro termine.
     *
     * @return Collection<int, Contrato>
     */
    public static function conflictosDeVehiculos(?int $excluirId, string $fecha, string $inicio, string $fin, array $vehiculoIds): Collection
    {
        if ($vehiculoIds === []) {
            return new Collection();
        }

        return static::query()
            ->when($excluirId, fn (Builder $q) => $q->whereKeyNot($excluirId))
            ->whereDate('fecha', $fecha)
            ->whereNotNull('hora_inicio')
            ->where('hora_inicio', '<', $fin)
            ->where('hora_fin', '>', $inicio)
            ->whereHas('vehiculos', fn (Builder $q) => $q->whereIn('vehiculos.id', $vehiculoIds))
            ->with(['vehiculos' => fn ($q) => $q->whereIn('vehiculos.id', $vehiculoIds)])
            ->get();
    }

    public function tieneUbicacion(): bool
    {
        return $this->latitud !== null && $this->longitud !== null;
    }

    /**
     * Enlace de Google Maps con la ruta hasta la obra
     */
    public function urlComoLlegar(): ?string
    {
        return $this->tieneUbicacion()
            ? "https://www.google.com/maps/dir/?api=1&destination={$this->latitud},{$this->longitud}"
            : null;
    }
}
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

    protected static function booted(): void
    {
        // El formulario elige al vendedor por nombre; aquí se guarda también su id,
        // que es lo que usan las comisiones (el nombre puede corregirse después)
        static::saving(function (Contrato $contrato) {
            if ($contrato->isDirty('nombre_vendedor') || ! $contrato->vendedor_id) {
                $contrato->vendedor_id = Trabajador::asesores()
                    ->where('nombre_completo', $contrato->nombre_vendedor)
                    ->value('id') ?? $contrato->vendedor_id;
            }
        });

        // Al quedar PAGADO se registra en Ingresos lo que faltaba cobrar
        static::saved(function (Contrato $contrato) {
            if ($contrato->estado_pago === 'PAGADO' && ($contrato->wasRecentlyCreated || $contrato->wasChanged('estado_pago'))) {
                $contrato->registrarCancelacion();
            }
        });
    }

    /**
     * Ingresos de este contrato. En Ingresos el número a veces se escribió sin "C-".
     */
    public function ingresos(): Builder
    {
        $numero = (string) $this->numero_contrato;

        return Ingreso::query()->whereIn('numero_contrato', array_unique([$numero, preg_replace('/^C-/i', '', $numero)]));
    }

    public function totalCobrado(): float
    {
        return round((float) $this->ingresos()->sum('monto'), 2);
    }

    public function saldoPorCobrar(): float
    {
        return round(max(0, (float) $this->monto_total - $this->totalCobrado()), 2);
    }

    /**
     * Abono del cliente: se registra en Ingresos (ADEL-CONTRATO si es parcial, CAN-CONTRATO si completa el total).
     * Cuando el saldo llega a cero, el contrato pasa a PAGADO.
     */
    public function registrarPago(float $monto, string $fecha, ?string $formaPago = null, ?string $observacion = null): Ingreso
    {
        if (blank($this->numero_contrato)) {
            throw new \InvalidArgumentException('El contrato necesita un N.° para registrar pagos.');
        }

        $monto = round($monto, 2);
        $saldo = $this->saldoPorCobrar();

        if ($monto <= 0) {
            throw new \InvalidArgumentException('El pago debe ser mayor que cero.');
        }

        if ($monto - $saldo > 0.009) {
            throw new \InvalidArgumentException('El pago (S/ ' . number_format($monto, 2) . ') es mayor que el saldo (S/ ' . number_format($saldo, 2) . ').');
        }

        $completa = $saldo - $monto < 0.01;

        $ingreso = Ingreso::create([
            'fecha'           => $fecha,
            'modo_pago'       => $completa ? 'CAN-CONTRATO' : 'ADEL-CONTRATO',
            'numero_contrato' => $this->numero_contrato,
            'asesor'          => $this->nombre_vendedor,
            'detalle'         => ($completa ? 'Cancelación' : 'Abono') . " del contrato {$this->numero_contrato} · {$this->nombre_cliente}",
            'monto'           => $monto,
            'obs'             => collect([$formaPago, $observacion])->filter()->implode(' · ') ?: null,
        ]);

        if ($completa && $this->estado_pago !== 'PAGADO') {
            // El saldo ya es cero: registrarCancelacion() del evento saved no agrega nada más
            $this->update(['estado_pago' => 'PAGADO']);
        }

        return $ingreso;
    }

    /** Pagos registrados del contrato, del más antiguo al más reciente */
    public function pagos(): \Illuminate\Support\Collection
    {
        return $this->ingresos()->orderBy('fecha')->orderBy('id')->get();
    }

    /**
     * Registra el saldo pendiente como ingreso CAN-CONTRATO (cancelación del contrato).
     * Si ya está todo cobrado (por ejemplo, adelanto + otros pagos) no registra nada.
     */
    public function registrarCancelacion(?string $fecha = null): ?Ingreso
    {
        $saldo = $this->saldoPorCobrar();

        if ($saldo < 0.01 || blank($this->numero_contrato)) {
            return null;
        }

        return Ingreso::create([
            'fecha'           => $fecha ?? today()->toDateString(),
            'modo_pago'       => 'CAN-CONTRATO',
            'numero_contrato' => $this->numero_contrato,
            'asesor'          => $this->nombre_vendedor,
            'detalle'         => "Cancelación del contrato {$this->numero_contrato} · {$this->nombre_cliente}",
            'monto'           => $saldo,
            'obs'             => $this->forma_pago ? "Registrado automáticamente · {$this->forma_pago}" : 'Registrado automáticamente',
        ]);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'vendedor_id');
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

        // Solo la hora de inicio: el término depende de imprevistos y del concreto que se use en obra
        return substr($this->hora_inicio, 0, 5);
    }

    /**
     * Sin hora de término, un vaciado se considera "en curso" durante estas horas desde su inicio.
     * Dos vaciados del mismo vehículo que empiezan con menos diferencia son un cruce probable.
     */
    public const HORAS_ESTIMADAS_VACIADO = 3;

    /**
     * Otros vaciados del mismo día que usan alguno de esos vehículos.
     * Cada uno trae ->esCruce = true si empieza a menos de HORAS_ESTIMADAS_VACIADO de $inicio
     * (cruce probable); los demás son informativos (el vehículo tiene otra obra ese día).
     *
     * @return Collection<int, Contrato>
     */
    public static function conflictosDeVehiculos(?int $excluirId, string $fecha, string $inicio, array $vehiculoIds): Collection
    {
        if ($vehiculoIds === []) {
            return new Collection();
        }

        $minutos = fn (?string $hora): int => $hora ? ((int) substr($hora, 0, 2)) * 60 + (int) substr($hora, 3, 2) : 0;

        return static::query()
            ->when($excluirId, fn (Builder $q) => $q->whereKeyNot($excluirId))
            ->whereDate('fecha', $fecha)
            ->whereNotNull('hora_inicio')
            ->whereHas('vehiculos', fn (Builder $q) => $q->whereIn('vehiculos.id', $vehiculoIds))
            ->with(['vehiculos' => fn ($q) => $q->whereIn('vehiculos.id', $vehiculoIds)])
            ->orderBy('hora_inicio')
            ->get()
            ->each(function (Contrato $otro) use ($inicio, $minutos) {
                $otro->esCruce = abs($minutos($otro->hora_inicio) - $minutos($inicio)) < self::HORAS_ESTIMADAS_VACIADO * 60;
            });
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
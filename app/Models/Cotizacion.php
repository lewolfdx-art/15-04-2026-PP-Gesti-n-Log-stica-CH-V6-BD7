<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Cotización que el asesor registra en la obra (medidas, ubicación, fotos, precio propuesto).
 * Flujo: enviada → en_espera (adelanto confirmado: la fecha queda separada) → convertida (pasa a contrato),
 * o rechazada, o cancelada (el cliente desiste después de separar y se le devuelve el adelanto).
 * El asesor puede declarar el adelanto que recibió en la obra; la administración lo confirma.
 */
class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    /** Dos vaciados a menos de estas horas de diferencia el mismo día se consideran cruzados */
    public const HORAS_CRUCE = 2;

    protected $fillable = [
        'asesor_id', 'cliente_nombre', 'cliente_documento', 'cliente_telefono',
        'ubicacion_referencia', 'latitud', 'longitud', 'descripcion_lugar', 'fotos_lugar',
        'viabilidad', 'viabilidad_comentario', 'estructura', 'tipo_concreto', 'volumen_estimado',
        'bombeo', 'fecha_tentativa', 'hora_tentativa', 'precio_unitario_propuesto', 'observaciones',
        // Adelanto que el asesor recibió en la obra (por confirmar)
        'adelanto_declarado_monto', 'adelanto_declarado_forma_pago', 'adelanto_voucher',
    ];

    protected $casts = [
        'latitud'                   => 'float',
        'longitud'                  => 'float',
        'fotos_lugar'               => 'array',
        'volumen_estimado'          => 'decimal:2',
        'precio_unitario_propuesto' => 'decimal:2',
        'precio_unitario_aprobado'  => 'decimal:2',
        'adelanto_monto'            => 'decimal:2',
        'adelanto_declarado_monto'  => 'decimal:2',
        'fecha_tentativa'           => 'date',
        'adelanto_at'               => 'datetime',
        'adelanto_declarado_at'     => 'datetime',
        'devolucion_at'             => 'datetime',
        'enviada_at'                => 'datetime',
        'revisado_at'               => 'datetime',
    ];

    public const ESTADOS = [
        'enviada'    => 'Por revisar',
        'en_espera'  => 'Fecha separada (con adelanto)',
        'convertida' => 'Pasó a contrato',
        'rechazada'  => 'Rechazada',
        'cancelada'  => 'Cancelada (adelanto devuelto)',
    ];

    public const ESTADO_COLORES = [
        'enviada'    => 'warning',
        'en_espera'  => 'info',
        'convertida' => 'success',
        'rechazada'  => 'gray',
        'cancelada'  => 'danger',
    ];

    protected static function booted(): void
    {
        static::creating(function (Cotizacion $cotizacion) {
            $cotizacion->codigo ??= static::siguienteCodigo();
            $cotizacion->estado ??= 'enviada';
            $cotizacion->enviada_at ??= now();
        });

        // Hora en que el asesor declaró (o corrigió) el adelanto
        static::saving(function (Cotizacion $cotizacion) {
            if ($cotizacion->isDirty('adelanto_declarado_monto')) {
                $cotizacion->adelanto_declarado_at = (float) $cotizacion->adelanto_declarado_monto > 0 ? now() : null;
            }
        });
    }

    public static function siguienteCodigo(): string
    {
        $prefijo = 'COT-' . now()->year . '-';

        $ultimo = DB::table('cotizaciones')
            ->where('codigo', 'like', $prefijo . '%')
            ->lockForUpdate()
            ->max('codigo');

        $numero = $ultimo ? ((int) substr($ultimo, strlen($prefijo))) + 1 : 1;

        return $prefijo . str_pad((string) $numero, 5, '0', STR_PAD_LEFT);
    }

    public function asesor(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class, 'asesor_id');
    }

    public function revisadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    public function contrato(): HasOne
    {
        return $this->hasOne(Contrato::class);
    }

    public function scopePorRevisar(Builder $query): Builder
    {
        return $query->whereIn('estado', ['enviada', 'en_espera']);
    }

    public function tieneUbicacion(): bool
    {
        return $this->latitud !== null && $this->longitud !== null;
    }

    public function urlComoLlegar(): ?string
    {
        return $this->tieneUbicacion()
            ? "https://www.google.com/maps/dir/?api=1&destination={$this->latitud},{$this->longitud}"
            : null;
    }

    public function precioVigente(): ?float
    {
        $precio = $this->precio_unitario_aprobado ?? $this->precio_unitario_propuesto;

        return $precio !== null ? (float) $precio : null;
    }

    public function montoEstimado(): ?float
    {
        $precio = $this->precioVigente();

        return $precio !== null && $this->volumen_estimado !== null
            ? round($precio * (float) $this->volumen_estimado, 2)
            : null;
    }

    public function puedeModificarse(): bool
    {
        return in_array($this->estado, ['enviada', 'en_espera'], true);
    }

    /** El asesor anotó un adelanto que la administración todavía no confirma */
    public function tieneAdelantoPorConfirmar(): bool
    {
        return $this->estado === 'enviada' && (float) $this->adelanto_declarado_monto > 0;
    }

    /** Adelanto confirmado y con fecha: el horario del cliente está separado */
    public function estaSeparada(): bool
    {
        return $this->estado === 'en_espera' && $this->fecha_tentativa !== null;
    }

    public function scopeSeparadas(Builder $query): Builder
    {
        return $query->where('estado', 'en_espera')->whereNotNull('fecha_tentativa');
    }

    public function horaTexto(): ?string
    {
        return $this->hora_tentativa ? substr((string) $this->hora_tentativa, 0, 5) : null;
    }

    /**
     * Otros compromisos que se cruzan con la fecha y hora de esta cotización:
     * fechas separadas de otros clientes y vaciados ya programados ese día.
     *
     * @return array<int, string>
     */
    public function cruces(?string $fecha = null, ?string $hora = null): array
    {
        $fecha ??= $this->fecha_tentativa?->toDateString();
        $hora ??= $this->horaTexto();

        if (! $fecha) {
            return [];
        }

        $cerca = function (?string $otraHora) use ($hora): bool {
            // Sin hora en alguno de los dos no se puede descartar el cruce
            if (! $hora || ! $otraHora) {
                return true;
            }

            $diferencia = abs(strtotime("2000-01-01 {$hora}") - strtotime('2000-01-01 ' . substr($otraHora, 0, 5)));

            return $diferencia < self::HORAS_CRUCE * 3600;
        };

        $avisos = [];

        foreach (static::separadas()->whereDate('fecha_tentativa', $fecha)->whereKeyNot($this->getKey() ?? 0)->get() as $otra) {
            if ($cerca($otra->horaTexto())) {
                $avisos[] = "{$otra->codigo} · {$otra->cliente_nombre} separó ese día" . ($otra->horaTexto() ? " a las {$otra->horaTexto()}" : '');
            }
        }

        foreach (Contrato::whereDate('fecha', $fecha)->whereNotNull('programado_at')->get() as $contrato) {
            if ($cerca($contrato->hora_inicio)) {
                $avisos[] = "Vaciado programado de {$contrato->nombre_cliente}" . ($contrato->horarioTexto() ? " ({$contrato->horarioTexto()})" : '');
            }
        }

        return $avisos;
    }

    /**
     * Confirma el adelanto (el que declaró el asesor u otro que reciba la oficina):
     * la cotización pasa a "Fecha separada".
     */
    public function registrarAdelanto(float $monto, ?string $formaPago, ?User $usuario = null): void
    {
        if (! $this->puedeModificarse()) {
            throw new InvalidArgumentException('Esta cotización ya no se puede modificar.');
        }

        if ($monto <= 0) {
            throw new InvalidArgumentException('El adelanto debe ser mayor que cero.');
        }

        $this->forceFill([
            'estado'              => 'en_espera',
            'adelanto_monto'      => round($monto, 2),
            'adelanto_at'         => now(),
            'adelanto_forma_pago' => $formaPago,
            'revisado_por'        => $usuario?->getKey(),
            'revisado_at'         => now(),
        ])->save();
    }

    /**
     * El cliente desiste después de separar: se libera la fecha y se registra la devolución del adelanto.
     * El adelanto todavía no está en Ingresos (entra al pasar a contrato), así que la caja no se toca.
     */
    public function cancelarConDevolucion(string $motivo, ?string $formaDevolucion, ?User $usuario = null): void
    {
        if ($this->estado !== 'en_espera') {
            throw new InvalidArgumentException('Solo se cancelan cotizaciones con la fecha separada.');
        }

        $this->forceFill([
            'estado'                => 'cancelada',
            'motivo_cancelacion'    => $motivo,
            'devolucion_forma_pago' => $formaDevolucion,
            'devolucion_at'         => now(),
            'revisado_por'          => $usuario?->getKey(),
            'revisado_at'           => now(),
        ])->save();
    }

    public function rechazar(string $motivo, ?User $usuario = null): void
    {
        if (! $this->puedeModificarse()) {
            throw new InvalidArgumentException('Esta cotización ya no se puede modificar.');
        }

        $this->forceFill([
            'estado'         => 'rechazada',
            'motivo_rechazo' => $motivo,
            'revisado_por'   => $usuario?->getKey(),
            'revisado_at'    => now(),
        ])->save();
    }

    /**
     * Crea el contrato con los datos de la cotización. Si hubo adelanto, lo registra como Ingreso.
     */
    public function convertirEnContrato(array $datos, ?User $usuario = null): Contrato
    {
        if (! $this->puedeModificarse()) {
            throw new InvalidArgumentException('Esta cotización ya pasó a contrato o fue rechazada.');
        }

        return DB::transaction(function () use ($datos, $usuario) {
            $precio = round((float) ($datos['precio_unitario_aprobado'] ?? $this->precio_unitario_propuesto ?? 0), 2);
            $volumen = (float) ($this->volumen_estimado ?? 0);

            $contrato = Contrato::create([
                'cotizacion_id'         => $this->getKey(),
                'fecha'                 => $datos['fecha'] ?? $this->fecha_tentativa ?? today(),
                // Mismo formato que el formulario de contratos ("C-123"): así el adelanto queda ligado al contrato
                'numero_contrato'       => filled($datos['numero_contrato'] ?? null)
                    ? 'C-' . preg_replace('/^C-/i', '', trim((string) $datos['numero_contrato']))
                    : null,
                'nombre_cliente'        => $this->cliente_nombre,
                'nombre_vendedor'       => $this->asesor?->nombre_completo,
                'ubicacion_referencia'  => $this->ubicacion_referencia,
                'latitud'               => $this->latitud,
                'longitud'              => $this->longitud,
                'descripcion_lugar'     => $this->descripcion_lugar,
                'fotos_lugar'           => $this->fotos_lugar,
                'viabilidad'            => $this->viabilidad,
                'viabilidad_comentario' => $this->viabilidad_comentario,
                'estructura'            => $this->estructura,
                'tipo_concreto'         => $this->tipo_concreto,
                'bombeo'                => $this->bombeo,
                'volumen_guia'          => $volumen,
                'volumen_real'          => $volumen,
                'pu'                    => $precio,
                'monto_total'           => round($volumen * $precio, 2),
                'total_para_empresa'    => round($volumen * $precio, 2),
                'venta_neta'            => round($volumen * $precio, 2),
                'comision'              => 0,
                'estado_pago'           => 'DEBE',
                'observacion'           => $this->observaciones,
            ]);

            if ((float) $this->adelanto_monto > 0) {
                Ingreso::create([
                    'fecha'           => ($this->adelanto_at ?? now())->toDateString(),
                    'modo_pago'       => 'ADEL-CONTRATO',
                    'numero_contrato' => $contrato->numero_contrato,
                    'asesor'          => $this->asesor?->nombre_completo,
                    'detalle'         => "Adelanto de la cotización {$this->codigo} · {$this->cliente_nombre}",
                    'monto'           => $this->adelanto_monto,
                    'obs'             => $this->adelanto_forma_pago,
                ]);
            }

            $this->forceFill([
                'estado'                   => 'convertida',
                'precio_unitario_aprobado' => $precio,
                'revisado_por'             => $usuario?->getKey(),
                'revisado_at'              => now(),
            ])->save();

            return $contrato;
        });
    }
}

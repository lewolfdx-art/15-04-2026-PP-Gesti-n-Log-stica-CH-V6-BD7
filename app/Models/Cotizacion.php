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
 * Flujo: enviada → en_espera (el cliente dio adelanto) → convertida (pasa a contrato), o rechazada.
 */
class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'asesor_id', 'cliente_nombre', 'cliente_documento', 'cliente_telefono',
        'ubicacion_referencia', 'latitud', 'longitud', 'descripcion_lugar', 'fotos_lugar',
        'viabilidad', 'viabilidad_comentario', 'estructura', 'tipo_concreto', 'volumen_estimado',
        'bombeo', 'fecha_tentativa', 'hora_tentativa', 'precio_unitario_propuesto', 'observaciones',
    ];

    protected $casts = [
        'latitud'                   => 'float',
        'longitud'                  => 'float',
        'fotos_lugar'               => 'array',
        'volumen_estimado'          => 'decimal:2',
        'precio_unitario_propuesto' => 'decimal:2',
        'precio_unitario_aprobado'  => 'decimal:2',
        'adelanto_monto'            => 'decimal:2',
        'fecha_tentativa'           => 'date',
        'adelanto_at'               => 'datetime',
        'enviada_at'                => 'datetime',
        'revisado_at'               => 'datetime',
    ];

    public const ESTADOS = [
        'enviada'    => 'Por revisar',
        'en_espera'  => 'En espera (con adelanto)',
        'convertida' => 'Pasó a contrato',
        'rechazada'  => 'Rechazada',
    ];

    public const ESTADO_COLORES = [
        'enviada'    => 'warning',
        'en_espera'  => 'info',
        'convertida' => 'success',
        'rechazada'  => 'gray',
    ];

    protected static function booted(): void
    {
        static::creating(function (Cotizacion $cotizacion) {
            $cotizacion->codigo ??= static::siguienteCodigo();
            $cotizacion->estado ??= 'enviada';
            $cotizacion->enviada_at ??= now();
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

    public function registrarAdelanto(float $monto, ?string $formaPago, ?User $usuario = null): void
    {
        if (! $this->puedeModificarse()) {
            throw new InvalidArgumentException('Esta cotización ya no se puede modificar.');
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
                'numero_contrato'       => $datos['numero_contrato'] ?? null,
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

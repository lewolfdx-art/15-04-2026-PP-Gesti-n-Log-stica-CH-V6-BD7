<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Pago quincenal al personal: sueldo fijo para operadores y personal,
 * comisión por contratos para los asesores de ventas.
 */
class PagoPersonal extends Model
{
    protected $table = 'pagos_personal';

    protected $fillable = [
        'trabajador_id', 'tipo', 'anio', 'mes', 'quincena', 'periodo_desde', 'periodo_hasta',
        'monto_base', 'adicionales', 'concepto_adicionales', 'descuentos', 'concepto_descuentos',
        'total', 'forma_pago', 'observacion', 'pagado_at', 'registrado_por',
    ];

    protected $casts = [
        'periodo_desde' => 'date',
        'periodo_hasta' => 'date',
        'monto_base'    => 'decimal:2',
        'adicionales'   => 'decimal:2',
        'descuentos'    => 'decimal:2',
        'total'         => 'decimal:2',
        'pagado_at'     => 'datetime',
    ];

    public const TIPOS = [
        'sueldo'   => 'Sueldo',
        'comision' => 'Comisión por ventas',
    ];

    public const FORMAS_PAGO = [
        'EFECTIVO'      => 'Efectivo',
        'TRANSFERENCIA' => 'Transferencia / depósito',
        'YAPE_PLIN'     => 'Yape / Plin',
    ];

    protected static function booted(): void
    {
        static::creating(function (PagoPersonal $pago) {
            $pago->codigo ??= static::siguienteCodigo();
        });
    }

    public static function siguienteCodigo(): string
    {
        $prefijo = 'PP-' . now()->year . '-';

        $ultimo = DB::table('pagos_personal')
            ->where('codigo', 'like', $prefijo . '%')
            ->lockForUpdate()
            ->max('codigo');

        $numero = $ultimo ? ((int) substr($ultimo, strlen($prefijo))) + 1 : 1;

        return $prefijo . str_pad((string) $numero, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Rango de fechas de una quincena: 1 = del 1 al 15, 2 = del 16 a fin de mes
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function periodo(int $anio, int $mes, int $quincena): array
    {
        $inicioMes = Carbon::create($anio, $mes, 1)->startOfDay();

        return $quincena === 1
            ? [$inicioMes, $inicioMes->copy()->day(15)]
            : [$inicioMes->copy()->day(16), $inicioMes->copy()->endOfMonth()->startOfDay()];
    }

    public static function tipoPara(Trabajador $trabajador): string
    {
        return $trabajador->tipo_cargo === Trabajador::CARGO_ASESOR ? 'comision' : 'sueldo';
    }

    /**
     * Contratos del asesor hasta el fin del período cuya comisión aún no se pagó,
     * incluidas las de quincenas anteriores que quedaron pendientes.
     * Se busca por vendedor_id; los contratos viejos sin id, por el nombre.
     */
    public static function contratosPendientesDeComision(Trabajador $asesor, Carbon $hasta): Collection
    {
        return Contrato::query()
            ->where(fn ($query) => $query->where('vendedor_id', $asesor->getKey())
                ->orWhere(fn ($query) => $query->whereNull('vendedor_id')->where('nombre_vendedor', $asesor->nombre_completo)))
            ->whereDate('fecha', '<=', $hasta->toDateString())
            ->whereNull('pago_comision_id')
            ->where('comision', '>', 0)
            ->orderBy('fecha')
            ->get();
    }

    /**
     * Monto base sugerido: media sueldo mensual, o la suma de comisiones de la quincena
     */
    public static function montoBase(Trabajador $trabajador, int $anio, int $mes, int $quincena): float
    {
        if (static::tipoPara($trabajador) === 'sueldo') {
            return round(((float) $trabajador->sueldo_mensual) / 2, 2);
        }

        [, $hasta] = static::periodo($anio, $mes, $quincena);

        return round((float) static::contratosPendientesDeComision($trabajador, $hasta)->sum('comision'), 2);
    }

    /**
     * Registra el pago calculando todo en el servidor y, si es comisión,
     * marca los contratos como liquidados para que no se paguen dos veces.
     */
    public static function registrar(array $datos, ?User $usuario = null): self
    {
        return DB::transaction(function () use ($datos, $usuario) {
            $trabajador = Trabajador::findOrFail($datos['trabajador_id']);
            $anio = (int) $datos['anio'];
            $mes = (int) $datos['mes'];
            $quincena = (int) $datos['quincena'];
            $tipo = static::tipoPara($trabajador);
            [$desde, $hasta] = static::periodo($anio, $mes, $quincena);

            // Un sueldo por quincena. Las comisiones pueden ser varias (se liquidan por obra);
            // solo se repite el aviso si ya hubo una comisión a mano en la quincena.
            $existente = static::where(compact('anio', 'mes', 'quincena', 'tipo'))
                ->where('trabajador_id', $trabajador->getKey());

            $contratos = collect();
            if ($tipo === 'comision') {
                $pendientes = static::contratosPendientesDeComision($trabajador, $hasta);

                if ($pendientes->isNotEmpty()) {
                    // Solo los contratos marcados en el formulario; si no llega la lista, todos los pendientes
                    $elegidos = isset($datos['contratos_ids'])
                        ? $pendientes->whereIn('id', array_map('intval', (array) $datos['contratos_ids']))
                        : $pendientes;

                    if ($elegidos->isEmpty()) {
                        throw new InvalidArgumentException('Marca al menos un contrato para liquidar su comisión.');
                    }

                    // Bloquea y vuelve a comprobar que nadie los liquidó mientras tanto
                    $contratos = Contrato::whereKey($elegidos->modelKeys())->whereNull('pago_comision_id')->lockForUpdate()->get();

                    if ($contratos->count() !== $elegidos->count()) {
                        throw new InvalidArgumentException('Alguno de los contratos ya se liquidó en otro pago. Vuelve a abrir el formulario.');
                    }

                    $montoBase = round((float) $contratos->sum('comision'), 2);
                } else {
                    // Sin contratos pendientes se acepta un monto a mano (comisión acordada fuera del sistema)
                    $existente->whereDoesntHave('contratos');
                    $montoBase = round((float) ($datos['monto_base'] ?? 0), 2);
                }
            } else {
                // El sueldo de la quincena se puede ajustar (días no trabajados, ingreso a mitad de mes, etc.)
                $montoBase = round((float) ($datos['monto_base'] ?? static::montoBase($trabajador, $anio, $mes, $quincena)), 2);
            }

            if ($contratos->isEmpty() && ($codigo = $existente->value('codigo'))) {
                throw new InvalidArgumentException("Ya se registró el pago de esta quincena para {$trabajador->nombre_completo} ({$codigo}).");
            }

            $adicionales = round((float) ($datos['adicionales'] ?? 0), 2);
            $descuentos = round((float) ($datos['descuentos'] ?? 0), 2);
            $total = round($montoBase + $adicionales - $descuentos, 2);

            if ($montoBase <= 0) {
                throw new InvalidArgumentException($tipo === 'comision'
                    ? 'El asesor no tiene comisiones pendientes. Ingresa el monto a pagar.'
                    : 'El trabajador no tiene sueldo registrado. Ingresa el monto o regístralo en su ficha.');
            }

            if ($total < 0) {
                throw new InvalidArgumentException('Los descuentos no pueden ser mayores que el monto a pagar.');
            }

            $pago = static::create([
                'trabajador_id'        => $trabajador->getKey(),
                'tipo'                 => $tipo,
                'anio'                 => $anio,
                'mes'                  => $mes,
                'quincena'             => $quincena,
                'periodo_desde'        => $desde,
                'periodo_hasta'        => $hasta,
                'monto_base'           => $montoBase,
                'adicionales'          => $adicionales,
                'concepto_adicionales' => $datos['concepto_adicionales'] ?? null,
                'descuentos'           => $descuentos,
                'concepto_descuentos'  => $datos['concepto_descuentos'] ?? null,
                'total'                => $total,
                'forma_pago'           => $datos['forma_pago'] ?? null,
                'observacion'          => $datos['observacion'] ?? null,
                'pagado_at'            => ! empty($datos['marcar_pagado']) ? now() : null,
                'registrado_por'       => $usuario?->getKey(),
            ]);

            if ($contratos->isNotEmpty()) {
                Contrato::whereKey($contratos->modelKeys())->update(['pago_comision_id' => $pago->getKey()]);
            }

            return $pago;
        });
    }

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class, 'pago_comision_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereNull('pagado_at');
    }

    public function estaPagado(): bool
    {
        return $this->pagado_at !== null;
    }

    public function periodoTexto(): string
    {
        $mes = ucfirst(Carbon::create($this->anio, $this->mes, 1)->locale('es')->monthName);

        return ($this->quincena === 1 ? '1ra' : '2da') . " quincena de {$mes} {$this->anio} ("
            . $this->periodo_desde->format('d/m') . ' – ' . $this->periodo_hasta->format('d/m') . ')';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Hoja de reclamación del Libro de Reclamaciones virtual.
 * Las hojas no se editan ni se borran: solo se responden.
 */
class LibroReclamacion extends Model
{
    protected $table = 'libro_reclamacions';

    protected $fillable = [
        'claimant_name', 'document_type', 'document_number', 'email',
        'phone', 'address', 'menor_edad', 'apoderado_nombre', 'order_number',
        'bien_tipo', 'bien_descripcion', 'monto', 'tipo',
        'claim_description', 'petition',
    ];

    protected $casts = [
        'menor_edad'    => 'boolean',
        'monto'         => 'decimal:2',
        'fecha_limite'  => 'date',
        'respondido_at' => 'datetime',
    ];

    public const TIPOS = [
        'reclamo' => 'Reclamo',
        'queja'   => 'Queja',
    ];

    public const TIPOS_DESCRIPCION = [
        'reclamo' => 'Disconformidad relacionada a los productos o servicios.',
        'queja'   => 'Disconformidad no relacionada a los productos o servicios, o malestar respecto a la atención al público.',
    ];

    public const BIENES = [
        'producto' => 'Producto',
        'servicio' => 'Servicio',
    ];

    public const DOCUMENTOS = [
        'DNI'       => 'DNI',
        'CE'        => 'Carné de extranjería',
        'PASAPORTE' => 'Pasaporte',
        'RUC'       => 'RUC',
    ];

    public const ESTADOS = [
        'pending'  => 'Pendiente',
        'resolved' => 'Respondido',
    ];

    protected static function booted(): void
    {
        static::creating(function (LibroReclamacion $hoja) {
            $hoja->status ??= 'pending';
            $hoja->fecha_limite ??= now()->addWeekdays(config('empresa.plazo_respuesta_dias', 15));
            $hoja->codigo ??= static::siguienteCodigo();
        });
    }

    /**
     * Número correlativo por año: LR-2026-00001, LR-2026-00002, ...
     */
    public static function siguienteCodigo(): string
    {
        $prefijo = 'LR-' . now()->year . '-';

        $ultimo = DB::table('libro_reclamacions')
            ->where('codigo', 'like', $prefijo . '%')
            ->lockForUpdate()
            ->max('codigo');

        $numero = $ultimo ? ((int) substr($ultimo, strlen($prefijo))) + 1 : 1;

        return $prefijo . str_pad((string) $numero, 5, '0', STR_PAD_LEFT);
    }

    public function respondidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondido_por');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function estaRespondido(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Días hábiles que quedan para responder (negativo si ya venció)
     */
    public function diasHabilesRestantes(): ?int
    {
        if (! $this->fecha_limite || $this->estaRespondido()) {
            return null;
        }

        $hoy = today();

        return $this->fecha_limite->greaterThanOrEqualTo($hoy)
            ? $hoy->diffInWeekdays($this->fecha_limite)
            : -$this->fecha_limite->diffInWeekdays($hoy);
    }

    public function responder(string $respuesta, ?User $usuario = null): void
    {
        $this->forceFill([
            'status'         => 'resolved',
            'response'       => $respuesta,
            'respondido_at'  => now(),
            'respondido_por' => $usuario?->getKey(),
        ])->save();
    }
}

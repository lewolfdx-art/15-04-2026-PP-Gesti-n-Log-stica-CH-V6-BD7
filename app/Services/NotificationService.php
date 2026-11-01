<?php

namespace App\Services;

use App\Filament\Operador\Resources\MisCotizacionesResource;
use App\Filament\Operador\Resources\MisObrasResource;
use App\Filament\Resources\CotizacionResource;
use App\Filament\Resources\CuentaPorPagarResource;
use App\Filament\Resources\ProgramacionResource;
use App\Filament\Resources\LibroReclamacionResource;
use App\Filament\Resources\ReporteSemanalResource;
use App\Models\Contrato;
use App\Models\Cotizacion;
use App\Models\CuentaPorPagar;
use App\Models\LibroReclamacion;
use App\Models\ReporteSemanal;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    /**
     * Monto a partir del cual se envía la alerta crítica
     */
    public const UMBRAL_ALERTA = 50000;

    /**
     * Clave de caché que recuerda si la alerta crítica ya fue enviada
     */
    private const CACHE_ALERTA = 'alerta_critica_pagos_enviada';

    /**
     * Total general por pagar: cuentas no pagadas + reportes en DEBE o ADELANTO
     */
    public static function totalGeneralPorPagar(): float
    {
        return (float) CuentaPorPagar::where('pagado', false)->sum('total')
            + (float) ReporteSemanal::whereIn('estado', ['DEBE', 'ADELANTO'])->sum('monto');
    }

    /**
     * Envía la alerta crítica solo cuando el total cruza el umbral (una vez),
     * y la rearma cuando vuelve a bajar.
     */
    public static function checkAndNotifyPayables(): float
    {
        $totalGeneral = self::totalGeneralPorPagar();

        if ($totalGeneral <= self::UMBRAL_ALERTA) {
            Cache::forget(self::CACHE_ALERTA);

            return $totalGeneral;
        }

        if (Cache::has(self::CACHE_ALERTA)) {
            return $totalGeneral;
        }

        Cache::forever(self::CACHE_ALERTA, true);

        $notificacion = Notification::make()
            ->title('Alerta crítica de cuentas por pagar')
            ->body('El total general por pagar (' . self::soles($totalGeneral) . ') superó el umbral de ' . self::soles(self::UMBRAL_ALERTA) . '.')
            ->danger()
            ->icon('heroicon-o-exclamation-triangle')
            ->actions([
                Action::make('ver_cuentas')
                    ->label('Ver cuentas por pagar')
                    ->url(CuentaPorPagarResource::getUrl('index', isAbsolute: false))
                    ->button(),
                Action::make('ver_reportes')
                    ->label('Ver reportes semanales')
                    ->url(ReporteSemanalResource::getUrl('index', isAbsolute: false))
                    ->link(),
            ]);

        self::enviar($notificacion);

        return $totalGeneral;
    }

    /**
     * Notificación cuando se registra una nueva cuenta por pagar pendiente
     */
    public static function notifyNewPayable(CuentaPorPagar $cuenta): void
    {
        $notificacion = Notification::make()
            ->title('Nueva cuenta por pagar')
            ->body("{$cuenta->detalle} · " . self::soles($cuenta->total))
            ->icon('heroicon-o-document-text')
            ->iconColor('warning')
            ->actions([
                Action::make('ver')
                    ->label('Ver cuenta')
                    ->url(CuentaPorPagarResource::getUrl('edit', ['record' => $cuenta], isAbsolute: false))
                    ->button(),
            ]);

        self::enviar($notificacion);
    }

    /**
     * Notificación cuando se paga una cuenta
     */
    public static function notifyPayablePaid(CuentaPorPagar $cuenta): void
    {
        $totalPendiente = (float) CuentaPorPagar::where('pagado', false)->sum('total');

        $notificacion = Notification::make()
            ->title('Cuenta pagada')
            ->body("Se pagó: {$cuenta->detalle} (" . self::soles($cuenta->total) . ").\nPendiente restante: " . self::soles($totalPendiente))
            ->icon('heroicon-o-check-circle')
            ->iconColor('success');

        self::enviar($notificacion);
    }

    /**
     * Notificación para reportes semanales en DEBE o ADELANTO
     */
    public static function notifyReporteSemanal(ReporteSemanal $reporte): void
    {
        if (! in_array($reporte->estado, ['DEBE', 'ADELANTO'])) {
            return;
        }

        $notificacion = Notification::make()
            ->title("Reporte semanal · {$reporte->estado}")
            ->body("Proveedor: {$reporte->proveedor}\nMonto: " . self::soles($reporte->monto))
            ->icon('heroicon-o-calendar')
            ->iconColor($reporte->estado === 'DEBE' ? 'danger' : 'warning')
            ->actions([
                Action::make('ver')
                    ->label('Ver reporte')
                    ->url(ReporteSemanalResource::getUrl('edit', ['record' => $reporte], isAbsolute: false))
                    ->button(),
            ]);

        self::enviar($notificacion);
    }

    /**
     * Avisa a los operadores recién asignados a la obra de un contrato.
     * Solo a quienes tienen usuario activo y aún no fueron notificados de esta obra.
     */
    public static function notificarOperadores(Contrato $contrato): int
    {
        $pendientes = $contrato->operadores()
            ->wherePivotNull('notificado_at')
            ->with('user')
            ->get();

        $enviadas = 0;

        foreach ($pendientes as $trabajador) {
            $usuario = $trabajador->user;

            if (! $usuario || ! $usuario->is_active) {
                continue;
            }

            $notificacion = Notification::make()
                ->title('Nueva obra asignada')
                ->body(collect([
                    'Vaciado: ' . ($contrato->fecha?->format('d/m/Y') ?? 'sin fecha')
                        . ($contrato->horarioTexto() ? " · {$contrato->horarioTexto()}" : '')
                        . ($contrato->estructura ? " · {$contrato->estructura}" : ''),
                    $contrato->vehiculos->isNotEmpty() ? 'Vehículos: ' . $contrato->vehiculos->pluck('codigo')->implode(', ') : null,
                    $contrato->ubicacion_referencia ? "Lugar: {$contrato->ubicacion_referencia}" : null,
                    $contrato->viabilidad ? Contrato::VIABILIDADES[$contrato->viabilidad] ?? null : null,
                ])->filter()->implode("\n"))
                ->icon('heroicon-o-map-pin')
                ->iconColor('warning')
                ->actions([
                    Action::make('ver_obra')
                        ->label('Ver obra y mapa')
                        ->url(MisObrasResource::getUrl('view', ['record' => $contrato], isAbsolute: false, panel: 'operador'))
                        ->button(),
                ]);

            $usuario->notifyNow($notificacion->toDatabase());
            $contrato->operadores()->updateExistingPivot($trabajador->getKey(), ['notificado_at' => now()]);
            $enviadas++;
        }

        return $enviadas;
    }

    /**
     * Aviso a los administradores de una nueva hoja del Libro de Reclamaciones
     */
    public static function notificarNuevaReclamacion(LibroReclamacion $hoja): void
    {
        $notificacion = Notification::make()
            ->title("Nuevo {$hoja->tipo} en el Libro de Reclamaciones")
            ->body("{$hoja->codigo} · {$hoja->claimant_name}\nResponder a más tardar el " . $hoja->fecha_limite->format('d/m/Y'))
            ->icon('heroicon-o-book-open')
            ->iconColor('danger')
            ->actions([
                Action::make('ver')
                    ->label('Ver y responder')
                    ->url(LibroReclamacionResource::getUrl('view', ['record' => $hoja], isAbsolute: false))
                    ->button(),
            ]);

        self::enviar($notificacion);
    }

    /**
     * Aviso a los administradores: un asesor envió una cotización desde la obra
     */
    public static function notificarNuevaCotizacion(Cotizacion $cotizacion): void
    {
        $notificacion = Notification::make()
            ->title('Nueva cotización por revisar')
            ->body(collect([
                "{$cotizacion->codigo} · {$cotizacion->asesor?->nombre_completo}",
                "Cliente: {$cotizacion->cliente_nombre}",
                $cotizacion->volumen_estimado ? "{$cotizacion->volumen_estimado} m³ · {$cotizacion->estructura}" : null,
            ])->filter()->implode("\n"))
            ->icon('heroicon-o-clipboard-document-list')
            ->iconColor('warning')
            ->actions([
                Action::make('ver')
                    ->label('Revisar')
                    ->url(CotizacionResource::getUrl('view', ['record' => $cotizacion], isAbsolute: false))
                    ->button(),
            ]);

        self::enviar($notificacion);
    }

    /**
     * Aviso al asesor sobre el resultado de su cotización (adelanto, contrato o rechazo)
     */
    public static function notificarAsesor(Cotizacion $cotizacion, string $titulo, string $cuerpo): void
    {
        $usuario = $cotizacion->asesor?->user;

        if (! $usuario || ! $usuario->is_active) {
            return;
        }

        $usuario->notifyNow(Notification::make()
            ->title($titulo)
            ->body($cuerpo)
            ->icon('heroicon-o-clipboard-document-check')
            ->iconColor('info')
            ->actions([
                Action::make('ver')
                    ->label('Ver cotización')
                    ->url(MisCotizacionesResource::getUrl('view', ['record' => $cotizacion], isAbsolute: false, panel: 'operador'))
                    ->button(),
            ])
            ->toDatabase());
    }

    /**
     * Aviso a los programadores de vaciados: hay un contrato nuevo por programar
     */
    public static function notificarVaciadoPorProgramar(Contrato $contrato): void
    {
        $notificacion = Notification::make()
            ->title('Nuevo vaciado por programar')
            ->body(collect([
                "{$contrato->nombre_cliente} · {$contrato->volumen_guia} m³ · {$contrato->estructura}",
                'Fecha pedida: ' . ($contrato->fecha?->format('d/m/Y') ?? '—'),
                $contrato->ubicacion_referencia ? "Lugar: {$contrato->ubicacion_referencia}" : null,
            ])->filter()->implode("\n"))
            ->icon('heroicon-o-calendar-days')
            ->iconColor('warning')
            ->actions([
                Action::make('programar')
                    ->label('Programar')
                    ->url(ProgramacionResource::getUrl('index', isAbsolute: false))
                    ->button(),
            ]);

        foreach (User::role('coordinador_de_vaciados')->where('is_active', true)->get() as $usuario) {
            $usuario->notifyNow($notificacion->toDatabase());
        }
    }

    /**
     * Guarda la notificación al instante para cada administrador.
     * Filament la encola por defecto (ShouldQueue) y sin un worker nunca se entregaría.
     */
    private static function enviar(Notification $notificacion): void
    {
        foreach (self::destinatarios() as $usuario) {
            $usuario->notifyNow($notificacion->toDatabase());
        }
    }

    /**
     * Usuarios que reciben las notificaciones: administradores activos
     */
    private static function destinatarios(): Collection
    {
        return User::role(['super_admin', 'admin'])->where('is_active', true)->get();
    }

    private static function soles(float|string|null $monto): string
    {
        return 'S/ ' . number_format((float) $monto, 2);
    }
}

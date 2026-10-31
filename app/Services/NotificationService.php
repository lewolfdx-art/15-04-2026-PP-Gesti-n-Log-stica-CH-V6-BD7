<?php

namespace App\Services;

use App\Filament\Resources\CuentaPorPagarResource;
use App\Filament\Resources\ReporteSemanalResource;
use App\Models\CuentaPorPagar;
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
            ->title('🚨 Alerta crítica de cuentas por pagar')
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
            ->title('📝 Nueva cuenta por pagar')
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
            ->title('✅ Cuenta pagada')
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
            ->title("📊 Reporte semanal · {$reporte->estado}")
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

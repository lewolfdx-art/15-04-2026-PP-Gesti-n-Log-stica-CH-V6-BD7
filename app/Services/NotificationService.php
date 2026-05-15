<?php

namespace App\Services;

use App\Models\CuentaPorPagar;
use App\Models\ReporteSemanal;
use Filament\Notifications\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Verifica y envía notificaciones por cuentas por pagar
     */
    public static function checkAndNotifyPayables()
    {
        // Calcular TOTAL GENERAL POR PAGAR
        $totalCuentasPorPagar = CuentaPorPagar::where('pagado', false)->sum('total');
        $totalReportesSemanales = ReporteSemanal::whereIn('estado', ['DEBE', 'ADELANTO'])->sum('monto');
        $totalGeneral = $totalCuentasPorPagar + $totalReportesSemanales;
        
        // Obtener usuarios
        $usuarios = User::all();
        
        // Si hay deudas pendientes
        if ($totalGeneral > 0) {
            foreach ($usuarios as $usuario) {
                // Enviar notificación SIMPLE sin verificación
                Notification::make()
                    ->title('💰 Alerta de Cuentas por Pagar')
                    ->body("El TOTAL GENERAL POR PAGAR es de S/ " . number_format($totalGeneral, 2))
                    ->icon('heroicon-o-currency-dollar')
                    ->iconColor('danger')
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view_cuentas')
                            ->label('Ver Cuentas por Pagar')
                            ->url('/admin/cuentas-por-pagars')
                            ->button(),
                        \Filament\Notifications\Actions\Action::make('view_reportes')
                            ->label('Ver Reportes Semanales')
                            ->url('/admin/reporte-semanals')
                            ->button(),
                    ])
                    ->sendToDatabase($usuario);
            }
        }
        
        // Alerta crítica si supera umbral
        $umbralAlerta = 50000;
        if ($totalGeneral > $umbralAlerta) {
            foreach ($usuarios as $usuario) {
                Notification::make()
                    ->title('🚨 ALERTA CRÍTICA')
                    ->body("El TOTAL GENERAL POR PAGAR (S/ " . number_format($totalGeneral, 2) . ") ha superado el umbral de S/ " . number_format($umbralAlerta, 2))
                    ->persistent()
                    ->danger()
                    ->icon('heroicon-o-exclamation-triangle')
                    ->sendToDatabase($usuario);
            }
        }
        
        return $totalGeneral;
    }
    
    /**
     * Notificación cuando se crea una nueva cuenta por pagar
     */
    public static function notifyNewPayable($cuenta)
    {
        $totalPendiente = CuentaPorPagar::where('pagado', false)->sum('total');
        $usuarios = User::all();
        
        foreach ($usuarios as $usuario) {
            Notification::make()
                ->title('📝 Nueva Cuenta por Pagar')
                ->body("{$cuenta->detalle} - Total: S/ " . number_format($cuenta->total, 2))
                ->icon('heroicon-o-document-text')
                ->iconColor('warning')
                ->actions([
                    \Filament\Notifications\Actions\Action::make('view')
                        ->label('Ver Cuenta')
                        ->url('/admin/cuentas-por-pagars/' . $cuenta->id . '/edit')
                        ->button(),
                ])
                ->sendToDatabase($usuario);
        }
    }
    
    /**
     * Notificación cuando se paga una cuenta
     */
    public static function notifyPayablePaid($cuenta)
    {
        $totalPendiente = CuentaPorPagar::where('pagado', false)->sum('total');
        $usuarios = User::all();
        
        foreach ($usuarios as $usuario) {
            Notification::make()
                ->title('✅ Cuenta Pagada')
                ->body("Se ha pagado: {$cuenta->detalle} (S/ " . number_format($cuenta->total, 2) . ")\nTotal pendiente restante: S/ " . number_format($totalPendiente, 2))
                ->icon('heroicon-o-check-circle')
                ->iconColor('success')
                ->sendToDatabase($usuario);
        }
    }
    
    /**
     * Notificación para reportes semanales
     */
    public static function notifyReporteSemanal($reporte)
    {
        if (in_array($reporte->estado, ['DEBE', 'ADELANTO'])) {
            $totalPendiente = ReporteSemanal::whereIn('estado', ['DEBE', 'ADELANTO'])->sum('monto');
            $usuarios = User::all();
            
            $estadoTexto = $reporte->estado == 'DEBE' ? 'DEBE' : 'ADELANTO';
            $colorIcono = $reporte->estado == 'DEBE' ? 'danger' : 'warning';
            
            foreach ($usuarios as $usuario) {
                Notification::make()
                    ->title("📊 Reporte Semanal - {$estadoTexto}")
                    ->body("Proveedor: {$reporte->proveedor}\nMonto: S/ " . number_format($reporte->monto, 2))
                    ->icon('heroicon-o-calendar')
                    ->iconColor($colorIcono)
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label('Ver Reporte')
                            ->url('/admin/reporte-semanals/' . $reporte->id . '/edit')
                            ->button(),
                    ])
                    ->sendToDatabase($usuario);
            }
        }
    }
}
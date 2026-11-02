<?php
// app/Filament/Widgets/TotalGeneralPagarWidget.php

namespace App\Filament\Widgets;

use App\Models\CuentaPorPagar;
use App\Models\ReporteSemanal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalGeneralPagarWidget extends BaseWidget
{
    use \App\Filament\Widgets\Concerns\ConAcento;

    protected function getStats(): array
    {
        $totalCuentasPorPagar = CuentaPorPagar::where('pagado', false)->sum('total');
        $totalReportesSemanales = ReporteSemanal::whereIn('estado', ['DEBE', 'ADELANTO'])->sum('monto');
        $totalGeneral = $totalCuentasPorPagar + $totalReportesSemanales;
        
        $totalCuentasPagadas = CuentaPorPagar::where('pagado', true)->sum('total');
        $totalReportesPagados = ReporteSemanal::where('estado', 'CANCELADO')->sum('monto');
        $totalPagado = $totalCuentasPagadas + $totalReportesPagados;
        
        return [
            Stat::make('Total por Pagar', 'S/ ' . number_format($totalGeneral, 2))
                ->extraAttributes(self::acento('warning'), merge: true)
                ->description('Suma de Cuentas por Pagar + Reportes Semanales pendientes')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($totalGeneral > 50000 ? 'danger' : ($totalGeneral > 10000 ? 'warning' : 'success'))
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                ], merge: true),
            
            Stat::make('Total Pagado', 'S/ ' . number_format($totalPagado, 2))
                ->extraAttributes(self::acento('cyan'), merge: true)
                ->description('Historial de pagos realizados')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Cuentas Pendientes', CuentaPorPagar::where('pagado', false)->count())
                ->extraAttributes(self::acento('fuchsia'), merge: true)
                ->description('+ ' . ReporteSemanal::whereIn('estado', ['DEBE', 'ADELANTO'])->count() . ' reportes pendientes')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),
        ];
    }
    
    protected function getColumns(): int
    {
        return 3;
    }
}
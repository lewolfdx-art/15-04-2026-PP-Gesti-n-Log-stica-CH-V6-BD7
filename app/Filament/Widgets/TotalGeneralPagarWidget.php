<?php
// app/Filament/Widgets/TotalGeneralPagarWidget.php

namespace App\Filament\Widgets;

use App\Filament\Resources\CuentaPorPagarResource;
use App\Filament\Resources\ReporteSemanalResource;
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

        $cuentasPendientes = CuentaPorPagar::where('pagado', false)->count();
        $reportesPendientes = ReporteSemanal::whereIn('estado', ['DEBE', 'ADELANTO'])->count();
        
        return [
            Stat::make('Total por Pagar', 'S/ ' . number_format($totalGeneral, 2))
                ->extraAttributes(self::acento('warning'), merge: true)
                ->description('Cuentas por pagar sin pagar (S/ ' . number_format($totalCuentasPorPagar, 2) . ') + reportes semanales en DEBE o ADELANTO (S/ ' . number_format($totalReportesSemanales, 2) . ')')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color($totalGeneral > 50000 ? 'danger' : ($totalGeneral > 10000 ? 'warning' : 'success'))
                ->url(CuentaPorPagarResource::getUrl('index')),
            
            Stat::make('Total Pagado', 'S/ ' . number_format($totalPagado, 2))
                ->extraAttributes(self::acento('cyan'), merge: true)
                ->description('Cuentas por pagar pagadas (S/ ' . number_format($totalCuentasPagadas, 2) . ') + reportes semanales CANCELADO (S/ ' . number_format($totalReportesPagados, 2) . ')')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(ReporteSemanalResource::getUrl('index')),
            
            Stat::make('Cuentas Pendientes', $cuentasPendientes)
                ->extraAttributes(self::acento('fuchsia'), merge: true)
                ->description("De Cuentas por pagar sin pagar · + {$reportesPendientes} reporte(s) semanal(es) en DEBE o ADELANTO")
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($cuentasPendientes + $reportesPendientes > 0 ? 'warning' : 'success')
                ->url(CuentaPorPagarResource::getUrl('index')),
        ];
    }
    
    protected function getColumns(): int
    {
        return 3;
    }
}
<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AgendaVaciadosWidget;
use App\Filament\Widgets\BalanceMesWidget;
use App\Filament\Widgets\ContratosPorVendedorChart;
use App\Filament\Widgets\CotizacionesEstadoChart;
use App\Filament\Widgets\DatabaseBackupWidget;
use App\Filament\Widgets\IngresosEgresosChart;
use App\Filament\Widgets\ResumenHoyWidget;
use App\Filament\Widgets\TotalGeneralPagarWidget;
use App\Filament\Widgets\UsoVehiculosChart;
use App\Filament\Widgets\VentasPorMesChart;
use App\Support\RespaldoAutomatico;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use \App\Filament\Concerns\SoloAdministradores;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $title = 'Escritorio';
    protected static ?string $navigationLabel = 'Escritorio';

    public function mount(): void
    {
        RespaldoAutomatico::generarSiToca();
    }

    // Arriba, a todo el ancho: lo del día y la plata del mes
    protected function getHeaderWidgets(): array
    {
        return [
            DatabaseBackupWidget::class,
            ResumenHoyWidget::class,
            BalanceMesWidget::class,
            TotalGeneralPagarWidget::class,
        ];
    }

    // Cuerpo: agenda y plata a todo el ancho, luego los gráficos en dos columnas
    public function getWidgets(): array
    {
        return [
            AgendaVaciadosWidget::class,
            IngresosEgresosChart::class,
            VentasPorMesChart::class,
            ContratosPorVendedorChart::class,
            CotizacionesEstadoChart::class,
            UsoVehiculosChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2;
    }
}

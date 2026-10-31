<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TotalGeneralPagarWidget;
use App\Filament\Widgets\VentasPorMesChart;
use App\Filament\Widgets\ContratosPorVendedorChart;
use App\Filament\Widgets\TiposEstructuraChart;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DatabaseBackupWidget;

class Dashboard extends BaseDashboard
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard - Concretera Mantaro';
    protected static ?string $navigationLabel = 'Escritorio';

    // Widgets que van en la cabecera (arriba, ocupan todo el ancho)
    protected function getHeaderWidgets(): array
    {
        return [
            DatabaseBackupWidget::class,
            TotalGeneralPagarWidget::class,
        ];
    }

    // Widgets que van en el cuerpo (abajo, en columnas)
    public function getWidgets(): array
    {
        return [
            VentasPorMesChart::class,
            ContratosPorVendedorChart::class,
            TiposEstructuraChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1; //storage\app\backups
    }
}
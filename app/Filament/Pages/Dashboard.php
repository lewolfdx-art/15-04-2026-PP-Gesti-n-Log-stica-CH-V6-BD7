<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\TotalGeneralPagarWidget;
use App\Filament\Widgets\VentasPorMesChart;
use App\Filament\Widgets\ContratosPorVendedorChart;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DatabaseBackupWidget;

class Dashboard extends BaseDashboard
{
    use \App\Filament\Concerns\SoloAdministradores;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $title = 'Dashboard - Concretera Mantaro';
    protected static ?string $navigationLabel = 'Escritorio';

    // Widgets que van en la cabecera (arriba, ocupan todo el ancho)
    protected function getHeaderWidgets(): array
    {
        return [
            DatabaseBackupWidget::class, // 👈 AGREGAR AQUÍ
            TotalGeneralPagarWidget::class,
        ];
    }

    // Widgets que van en el cuerpo (abajo, en columnas)
    public function getWidgets(): array
    {
        return [
            VentasPorMesChart::class,
            ContratosPorVendedorChart::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 2; // 2 columnas para los widgets del cuerpo
    }
}

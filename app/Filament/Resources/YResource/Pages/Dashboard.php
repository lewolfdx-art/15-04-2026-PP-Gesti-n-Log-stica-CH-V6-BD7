<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\DatabaseBackupWidget;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard - Concretera Mantaro';
    protected static ?string $navigationLabel = 'Escritorio';

    // Widgets que van en la cabecera (arriba, ocupan todo el ancho)
    protected function getHeaderWidgets(): array
    {
        return [
            DatabaseBackupWidget::class,
            \App\Filament\Widgets\TotalGeneralPagarWidget::class,
        ];
    }

    // Widgets que van en el cuerpo (abajo, en columnas)
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\VentasPorMesChart::class,
            \App\Filament\Widgets\ContratosPorVendedorChart::class,
            \App\Filament\Widgets\TiposEstructuraChart::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1; //storage\app\backups
    }
}
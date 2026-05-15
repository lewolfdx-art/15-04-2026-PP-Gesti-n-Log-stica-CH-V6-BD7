<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard - Concretera Mantaro';
    protected static ?string $navigationLabel = 'Escritorio';

    
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\TotalGeneralPagarWidget::class,
            
        ];
    }

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
        return 2;
    }
}
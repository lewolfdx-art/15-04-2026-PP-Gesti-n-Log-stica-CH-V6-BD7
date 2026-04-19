<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $title = 'Dashboard - Concretera Huancayo';
    protected static ?string $navigationLabel = 'Escritorio';

    public function getColumns(): int | array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
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
}
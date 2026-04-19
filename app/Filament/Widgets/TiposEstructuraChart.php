<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Contrato;

class TiposEstructuraChart extends ChartWidget
{
    protected static ?string $heading = 'Estructuras más Vendidas';
    protected static ?string $maxHeight = '340px';

    protected function getData(): array
    {
        $data = Contrato::query()
            ->select('estructura')                    // ← Columna correcta
            ->selectRaw('COUNT(*) as total')
            ->groupBy('estructura')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label'           => 'Cantidad',
                    'data'            => $data->pluck('total'),
                    'backgroundColor' => [
                        '#22d3ee', '#67e8f9', '#a5f3fc', 
                        '#cffafe', '#164e63', '#0891b2'
                    ],
                ],
            ],
            'labels' => $data->pluck('estructura'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
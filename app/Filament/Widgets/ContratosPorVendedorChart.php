<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Contrato;

class ContratosPorVendedorChart extends ChartWidget
{
    protected static ?string $heading = 'Contratos por Asesor / Vendedor';
    protected static ?string $maxHeight = '340px';

    protected function getData(): array
    {
        $data = Contrato::query()
            ->select('vendedor_id')
            ->selectRaw('nombre_vendedor')                    // ← Columna que ya existe
            ->selectRaw('COUNT(*) as total')
            ->groupBy('vendedor_id', 'nombre_vendedor')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [
                [
                    'label'           => 'Cantidad de contratos',
                    'data'            => $data->pluck('total'),
                    'backgroundColor' => '#67e8f9',
                    'borderColor'     => '#22d3ee',
                    'borderWidth'     => 2,
                ],
            ],
            'labels' => $data->pluck('nombre_vendedor'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
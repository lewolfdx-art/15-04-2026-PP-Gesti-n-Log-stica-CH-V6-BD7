<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Contrato;

class VentasPorMesChart extends ChartWidget
{
    protected static ?string $heading = 'Ventas por Mes (m³)';
    protected static ?string $maxHeight = '340px';

    protected function getData(): array
    {
        $data = Trend::model(Contrato::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('volumen_real');     // ← Columna correcta

        return [
            'datasets' => [
                [
                    'label'           => 'Metros cúbicos vendidos',
                    'data'            => $data->map(fn (TrendValue $value) => (float) $value->aggregate),
                    'backgroundColor' => '#22d3ee',
                    'borderColor'     => '#06b6d4',
                    'borderWidth'     => 2,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';   // Puedes cambiar a 'bar' si prefieres barras
    }
}
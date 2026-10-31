<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Contrato;
use Carbon\Carbon;

class VentasPorMesChart extends ChartWidget
{
    protected ?string $heading = 'Ventas por Mes (m³)';
    protected ?string $maxHeight = '340px';

    protected function getData(): array
    {
        // Por fecha de vaciado: created_at es cuándo se registró en el sistema, no cuándo se vendió
        $data = Trend::model(Contrato::class)
            ->dateColumn('fecha')
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
            'labels' => $data->map(fn (TrendValue $value) => ucfirst(Carbon::parse($value->date . '-01')->locale('es')->shortMonthName)),
        ];
    }

    protected function getType(): string
    {
        return 'line';   // Puedes cambiar a 'bar' si prefieres barras
    }
}
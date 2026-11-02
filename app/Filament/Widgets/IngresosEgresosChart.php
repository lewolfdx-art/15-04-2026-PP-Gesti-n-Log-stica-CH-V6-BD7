<?php

namespace App\Filament\Widgets;

use App\Models\Gasto;
use App\Models\Ingreso;
use App\Models\PagoPersonal;
use Filament\Widgets\ChartWidget;

/**
 * Ingresos contra egresos (gastos + pagos al personal entregados) de los últimos 6 meses.
 * Usa la misma regla que el Balance del mes, así los números cuadran entre los dos.
 */
class IngresosEgresosChart extends ChartWidget
{
    protected ?string $heading = 'Ingresos vs Egresos · últimos 6 meses';
    protected ?string $description = 'Egresos = gastos + pagos al personal entregados';
    protected ?string $maxHeight = '320px';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    protected function getData(): array
    {
        $etiquetas = $ingresos = $egresos = [];

        for ($i = 5; $i >= 0; $i--) {
            $inicio = now()->subMonthsNoOverflow($i)->startOfMonth();
            $fin = $inicio->copy()->endOfMonth();

            $etiquetas[] = ucfirst(rtrim($inicio->locale('es')->shortMonthName, '.')) . ' ' . $inicio->format('y');
            $ingresos[] = round((float) Ingreso::whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])->sum('monto'), 2);
            $egresos[] = round(
                (float) Gasto::whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])->sum('importe')
                + (float) PagoPersonal::whereBetween('pagado_at', [$inicio, $fin])->sum('total'),
                2
            );
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Ingresos',
                    'data'            => $ingresos,
                    'backgroundColor' => '#22c55e',
                    'borderColor'     => '#16a34a',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
                [
                    'label'           => 'Egresos',
                    'data'            => $egresos,
                    'backgroundColor' => '#f87171',
                    'borderColor'     => '#dc2626',
                    'borderWidth'     => 1,
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $etiquetas,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true, 'position' => 'bottom']],
            'scales'  => ['y' => ['beginAtZero' => true]],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Cotizacion;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * Cómo van las cotizaciones de los asesores: por revisar, en espera, convertidas o rechazadas.
 */
class CotizacionesEstadoChart extends ChartWidget
{
    protected ?string $heading = 'Cotizaciones por estado';
    protected ?string $maxHeight = '300px';

    public ?string $filter = 'anio';

    // Mismo orden que Cotizacion::ESTADOS
    private const COLORES = [
        'enviada'    => '#f59e0b',
        'en_espera'  => '#3b82f6',
        'convertida' => '#22c55e',
        'rechazada'  => '#9ca3af',
    ];

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    protected function getFilters(): ?array
    {
        return [
            'mes'   => 'Este mes',
            'anio'  => 'Este año',
            'todas' => 'Todas',
        ];
    }

    public function getDescription(): ?string
    {
        $total = $this->consulta()->count();
        $convertidas = $this->consulta()->where('estado', 'convertida')->count();

        return $total > 0
            ? "{$total} cotizaciones · " . round($convertidas / $total * 100) . '% pasó a contrato'
            : 'Aún no hay cotizaciones en este periodo';
    }

    private function consulta(): Builder
    {
        return Cotizacion::query()
            ->when($this->filter === 'mes', fn (Builder $query) => $query->where('enviada_at', '>=', now()->startOfMonth()))
            ->when($this->filter === 'anio', fn (Builder $query) => $query->where('enviada_at', '>=', now()->startOfYear()));
    }

    protected function getData(): array
    {
        $conteo = $this->consulta()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return [
            'datasets' => [
                [
                    'label'           => 'Cotizaciones',
                    'data'            => array_map(fn (string $estado) => (int) ($conteo[$estado] ?? 0), array_keys(Cotizacion::ESTADOS)),
                    'backgroundColor' => array_map(fn (string $estado) => self::COLORES[$estado], array_keys(Cotizacion::ESTADOS)),
                    'borderWidth'     => 0,
                ],
            ],
            'labels' => array_values(Cotizacion::ESTADOS),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => true, 'position' => 'bottom']],
            'scales'  => ['x' => ['display' => false], 'y' => ['display' => false]],
            'cutout'  => '60%',
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

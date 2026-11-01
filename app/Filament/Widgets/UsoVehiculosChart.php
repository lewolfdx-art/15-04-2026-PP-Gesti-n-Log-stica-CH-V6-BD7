<?php

namespace App\Filament\Widgets;

use App\Models\Vehiculo;
use Filament\Widgets\ChartWidget;

/**
 * Vaciados por mixer y bomba: cuál trabaja de más y cuál está parado.
 * Incluye los vehículos activos aunque no tengan vaciados en el periodo.
 */
class UsoVehiculosChart extends ChartWidget
{
    protected ?string $heading = 'Uso de vehículos';
    protected ?string $description = 'Vaciados por vehículo · naranja = mixer, azul oscuro = bomba';
    protected ?string $maxHeight = '300px';

    public ?string $filter = 'mes';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'coordinador_de_vaciados']) ?? false;
    }

    protected function getFilters(): ?array
    {
        return [
            'mes'      => 'Este mes',
            'anterior' => 'Mes anterior',
            'anio'     => 'Este año',
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function rango(): array
    {
        return match ($this->filter) {
            'anterior' => [now()->subMonthNoOverflow()->startOfMonth()->toDateString(), now()->subMonthNoOverflow()->endOfMonth()->toDateString()],
            'anio'     => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
            default    => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
        };
    }

    protected function getData(): array
    {
        $rango = $this->rango();
        $enRango = fn ($query) => $query->whereBetween('fecha', $rango);

        $vehiculos = Vehiculo::query()
            ->where(fn ($query) => $query->where('activo', true)->orWhereHas('contratos', $enRango))
            ->withCount(['contratos as vaciados' => $enRango])
            ->withSum(['contratos as m3' => $enRango], 'volumen_guia')
            ->get()
            ->sortByDesc('vaciados')
            ->values();

        return [
            'datasets' => [
                [
                    'label'           => 'Vaciados',
                    'data'            => $vehiculos->pluck('vaciados')->all(),
                    'backgroundColor' => $vehiculos->map(fn (Vehiculo $vehiculo) => $vehiculo->tipo === 'mixer' ? '#f5811e' : '#16222e')->all(),
                    'borderRadius'    => 4,
                ],
            ],
            'labels' => $vehiculos->map(fn (Vehiculo $vehiculo) => $vehiculo->codigo . ' · ' . number_format((float) $vehiculo->m3, 1) . ' m³')->all(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins'   => ['legend' => ['display' => false]],
            'scales'    => ['x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]]],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}

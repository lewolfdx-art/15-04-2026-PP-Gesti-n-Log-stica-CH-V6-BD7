<?php

namespace App\Filament\Widgets;

use App\Models\Gasto;
use App\Models\Ingreso;
use App\Models\PagoPersonal;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Plata del mes en curso: lo que entró (Ingresos), lo que salió (Gastos + pagos al
 * personal entregados) y lo que queda, comparado con el mes anterior.
 */
class BalanceMesWidget extends StatsOverviewWidget
{
    use \App\Filament\Widgets\Concerns\ConAcento;

    protected static ?int $sort = 2;

    protected int | array | null $columns = 3;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    protected function getHeading(): ?string
    {
        return 'Balance de ' . ucfirst(now()->locale('es')->monthName);
    }

    protected function getDescription(): ?string
    {
        return 'Egresos = gastos + pagos al personal entregados. Las cuentas por pagar pendientes no cuentan hasta pagarse.';
    }

    /**
     * @return array{ingresos: float, egresos: float}
     */
    private function totales(Carbon $inicio, Carbon $fin): array
    {
        $ingresos = (float) Ingreso::whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])->sum('monto');
        $gastos = (float) Gasto::whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])->sum('importe');
        $pagos = (float) PagoPersonal::whereBetween('pagado_at', [$inicio, $fin])->sum('total');

        return ['ingresos' => $ingresos, 'egresos' => $gastos + $pagos];
    }

    private function variacion(float $actual, float $anterior): ?float
    {
        return $anterior > 0 ? round(($actual - $anterior) / $anterior * 100, 1) : null;
    }

    protected function getStats(): array
    {
        $mes = $this->totales(now()->startOfMonth(), now()->endOfMonth());
        $anterior = $this->totales(now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth());
        $nombreAnterior = now()->subMonthNoOverflow()->locale('es')->monthName;

        $resultado = $mes['ingresos'] - $mes['egresos'];
        $resultadoAnterior = $anterior['ingresos'] - $anterior['egresos'];

        $comparar = function (float $actual, float $previo, bool $subirEsBueno) use ($nombreAnterior): array {
            $cambio = $this->variacion($actual, $previo);

            if ($cambio === null) {
                return ["Sin datos de {$nombreAnterior} para comparar", 'heroicon-m-minus', 'gray'];
            }

            $sube = $cambio >= 0;

            return [
                ($sube ? '+' : '') . $cambio . "% vs {$nombreAnterior}",
                $sube ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down',
                $sube === $subirEsBueno ? 'success' : 'danger',
            ];
        };

        [$descIngresos, $iconoIngresos, $colorIngresos] = $comparar($mes['ingresos'], $anterior['ingresos'], true);
        [$descEgresos, $iconoEgresos, $colorEgresos] = $comparar($mes['egresos'], $anterior['egresos'], false);

        return [
            Stat::make('Ingresos', 'S/ ' . number_format($mes['ingresos'], 2))
                ->extraAttributes(self::acento('success'), merge: true)
                ->description($descIngresos)
                ->descriptionIcon($iconoIngresos)
                ->descriptionColor($colorIngresos)
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),

            Stat::make('Egresos', 'S/ ' . number_format($mes['egresos'], 2))
                ->extraAttributes(self::acento('danger'), merge: true)
                ->description($descEgresos)
                ->descriptionIcon($iconoEgresos)
                ->descriptionColor($colorEgresos)
                ->icon('heroicon-o-arrow-up-tray')
                ->color('danger'),

            Stat::make('Lo que queda', 'S/ ' . number_format($resultado, 2))
                ->extraAttributes(self::acento('primary'), merge: true)
                ->description($resultadoAnterior != 0
                    ? "En {$nombreAnterior}: S/ " . number_format($resultadoAnterior, 2)
                    : 'Ingresos menos egresos del mes')
                ->descriptionIcon($resultado >= 0 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->icon('heroicon-o-scale')
                ->color($resultado >= 0 ? 'success' : 'danger'),
        ];
    }
}

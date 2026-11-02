<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CotizacionResource;
use App\Filament\Resources\LibroReclamacionResource;
use App\Filament\Resources\ProgramacionResource;
use App\Models\Contrato;
use App\Models\Cotizacion;
use App\Models\LibroReclamacion;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Lo que hay que atender hoy: vaciados, cotizaciones, programación y reclamos.
 * Cada tarjeta lleva a su módulo.
 */
class ResumenHoyWidget extends StatsOverviewWidget
{
    use \App\Filament\Widgets\Concerns\ConAcento;

    protected static ?int $sort = 1;

    protected ?string $heading = 'Resumen de hoy';

    protected int | array | null $columns = 4;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    protected function getStats(): array
    {
        $hoy = Contrato::whereDate('fecha', today());
        $vaciadosHoy = (clone $hoy)->count();
        $m3Hoy = (float) (clone $hoy)->sum('volumen_guia');
        $programadosHoy = (clone $hoy)->whereNotNull('programado_at')->count();

        $porRevisar = Cotizacion::where('estado', 'enviada')->count();
        $enEspera = Cotizacion::where('estado', 'en_espera')->count();

        $porProgramar = Contrato::whereNull('programado_at')->whereDate('fecha', '>=', today())->count();
        $porProgramarSemana = Contrato::whereNull('programado_at')->whereBetween('fecha', [today(), today()->addDays(6)])->count();

        $reclamos = LibroReclamacion::pending()->count();
        $reclamosUrgentes = LibroReclamacion::pending()->whereDate('fecha_limite', '<=', today()->addWeekdays(3))->count();

        return [
            Stat::make('Vaciados de hoy', $vaciadosHoy)
                ->extraAttributes(self::acento('teal'), merge: true)
                ->description($vaciadosHoy > 0
                    ? number_format($m3Hoy, 1) . " m³ · {$programadosHoy} programado(s)"
                    : 'Sin vaciados para hoy')
                ->descriptionIcon('heroicon-m-cube')
                ->icon('heroicon-o-truck')
                ->color($vaciadosHoy > $programadosHoy ? 'warning' : 'success')
                ->url(ProgramacionResource::getUrl('index')),

            Stat::make('Cotizaciones por revisar', $porRevisar)
                ->extraAttributes(self::acento('violet'), merge: true)
                ->description("{$enEspera} en espera con adelanto")
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-clipboard-document-list')
                ->color($porRevisar > 0 ? 'warning' : 'gray')
                ->url(CotizacionResource::getUrl('index')),

            Stat::make('Vaciados por programar', $porProgramar)
                ->extraAttributes(self::acento('indigo'), merge: true)
                ->description("{$porProgramarSemana} en los próximos 7 días")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-calendar')
                ->color($porProgramarSemana > 0 ? 'danger' : ($porProgramar > 0 ? 'warning' : 'success'))
                ->url(ProgramacionResource::getUrl('index')),

            Stat::make('Reclamos por responder', $reclamos)
                ->extraAttributes(self::acento('pink'), merge: true)
                ->description($reclamosUrgentes > 0 ? "{$reclamosUrgentes} vence(n) en 3 días hábiles o menos" : 'Dentro del plazo')
                ->descriptionIcon($reclamosUrgentes > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->icon('heroicon-o-book-open')
                ->color($reclamosUrgentes > 0 ? 'danger' : ($reclamos > 0 ? 'warning' : 'success'))
                ->url(LibroReclamacionResource::getUrl('index')),
        ];
    }
}

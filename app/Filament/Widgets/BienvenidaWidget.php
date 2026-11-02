<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContratoResource;
use App\Filament\Resources\CotizacionResource;
use App\Filament\Resources\ProgramacionResource;
use App\Models\Contrato;
use App\Models\Cotizacion;
use Filament\Widgets\Widget;

/**
 * Franja de bienvenida del Escritorio: saludo según la hora de Lima, fecha y accesos rápidos.
 */
class BienvenidaWidget extends Widget
{
    protected string $view = 'filament.widgets.bienvenida-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    protected function getViewData(): array
    {
        $hora = now()->hour;
        $nombre = trim((string) auth()->user()?->name);

        return [
            'saludo'  => $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches'),
            'nombre'  => $nombre,
            'fecha'   => ucfirst(now()->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY')),
            'vaciadosHoy' => Contrato::whereDate('fecha', today())->count(),
            'porRevisar'  => Cotizacion::where('estado', 'enviada')->count(),
            'accesos' => [
                ['Nuevo contrato', 'heroicon-o-document-plus', ContratoResource::getUrl('create')],
                ['Cotizaciones', 'heroicon-o-clipboard-document-list', CotizacionResource::getUrl('index')],
                ['Programación', 'heroicon-o-calendar-days', ProgramacionResource::getUrl('index')],
            ],
        ];
    }
}

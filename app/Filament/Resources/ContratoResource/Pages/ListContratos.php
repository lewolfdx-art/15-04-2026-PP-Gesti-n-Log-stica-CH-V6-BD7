<?php

namespace App\Filament\Resources\ContratoResource\Pages;

use App\Filament\Resources\ContratoResource;
use App\Filament\Resources\CotizacionResource;
use App\Models\Cotizacion;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListContratos extends ListRecords
{
    protected static string $resource = ContratoResource::class;

    protected function getHeaderActions(): array
    {
        $pendientes = Cotizacion::porRevisar()->count();

        return [
            // Las cotizaciones de los asesores llegan antes que el contrato: acceso directo arriba de la lista
            Action::make('cotizaciones')
                ->label('Cotizaciones por revisar')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning')
                ->badge($pendientes)
                ->visible($pendientes > 0)
                ->url(CotizacionResource::getUrl('index', [
                    'tableFilters' => ['estado' => ['value' => null]],
                ])),
            CreateAction::make(),
        ];
    }
}

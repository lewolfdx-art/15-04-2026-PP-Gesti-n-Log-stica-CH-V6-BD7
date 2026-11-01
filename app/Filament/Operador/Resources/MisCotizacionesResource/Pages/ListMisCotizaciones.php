<?php

namespace App\Filament\Operador\Resources\MisCotizacionesResource\Pages;

use App\Filament\Operador\Resources\MisCotizacionesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMisCotizaciones extends ListRecords
{
    protected static string $resource = MisCotizacionesResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Nueva cotización')->icon('heroicon-o-plus')];
    }
}

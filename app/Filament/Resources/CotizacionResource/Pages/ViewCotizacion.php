<?php

namespace App\Filament\Resources\CotizacionResource\Pages;

use App\Filament\Resources\CotizacionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCotizacion extends ViewRecord
{
    protected static string $resource = CotizacionResource::class;

    public function getTitle(): string
    {
        return "Cotización {$this->record->codigo}";
    }

    protected function getHeaderActions(): array
    {
        return [
            CotizacionResource::accionAdelanto(),
            CotizacionResource::accionPasarAContrato(),
            CotizacionResource::accionRechazar(),
        ];
    }
}

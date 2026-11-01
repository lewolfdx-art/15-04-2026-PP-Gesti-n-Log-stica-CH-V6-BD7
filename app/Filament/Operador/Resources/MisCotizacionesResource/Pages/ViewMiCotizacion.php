<?php

namespace App\Filament\Operador\Resources\MisCotizacionesResource\Pages;

use App\Filament\Operador\Resources\MisCotizacionesResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMiCotizacion extends ViewRecord
{
    protected static string $resource = MisCotizacionesResource::class;

    public function getTitle(): string
    {
        return "Cotización {$this->record->codigo}";
    }

    protected function getHeaderActions(): array
    {
        return [EditAction::make()->label('Corregir')];
    }
}

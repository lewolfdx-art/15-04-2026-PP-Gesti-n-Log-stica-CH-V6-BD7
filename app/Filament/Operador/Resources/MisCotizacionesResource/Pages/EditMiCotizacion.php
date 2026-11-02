<?php

namespace App\Filament\Operador\Resources\MisCotizacionesResource\Pages;

use App\Filament\Operador\Resources\MisCotizacionesResource;
use Filament\Resources\Pages\EditRecord;

class EditMiCotizacion extends EditRecord
{
    protected static string $resource = MisCotizacionesResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}

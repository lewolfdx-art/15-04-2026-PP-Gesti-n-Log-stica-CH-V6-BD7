<?php

namespace App\Filament\Operador\Resources\MisCotizacionesResource\Pages;

use App\Filament\Operador\Resources\MisCotizacionesResource;
use Filament\Resources\Pages\EditRecord;

class EditMiCotizacion extends EditRecord
{
    protected static string $resource = MisCotizacionesResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return MisCotizacionesResource::limpiarAdelantoSiNoHay($data, $this->data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}

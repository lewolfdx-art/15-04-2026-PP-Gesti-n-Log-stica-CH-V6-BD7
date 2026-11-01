<?php

namespace App\Filament\Resources\LibroReclamacionResource\Pages;

use App\Filament\Resources\LibroReclamacionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewLibroReclamacion extends ViewRecord
{
    protected static string $resource = LibroReclamacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LibroReclamacionResource::accionResponder(),
        ];
    }
}

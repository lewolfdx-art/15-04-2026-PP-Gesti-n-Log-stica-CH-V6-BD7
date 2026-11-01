<?php

namespace App\Filament\Resources\PagoPersonalResource\Pages;

use App\Filament\Resources\PagoPersonalResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPagoPersonal extends ViewRecord
{
    protected static string $resource = PagoPersonalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PagoPersonalResource::accionComprobante(),
            PagoPersonalResource::accionMarcarPagado(),
        ];
    }
}

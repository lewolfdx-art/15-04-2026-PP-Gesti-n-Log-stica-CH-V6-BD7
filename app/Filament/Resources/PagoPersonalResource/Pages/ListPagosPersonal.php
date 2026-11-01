<?php

namespace App\Filament\Resources\PagoPersonalResource\Pages;

use App\Filament\Resources\PagoPersonalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPagosPersonal extends ListRecords
{
    protected static string $resource = PagoPersonalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Registrar pago'),
        ];
    }
}

<?php

namespace App\Filament\Resources\AsesorVentasResource\Pages;

use App\Filament\Resources\AsesorVentasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAsesorVentas extends ListRecords
{
    protected static string $resource = AsesorVentasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

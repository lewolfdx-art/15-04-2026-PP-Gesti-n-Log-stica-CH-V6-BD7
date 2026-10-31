<?php

namespace App\Filament\Resources\IngresoResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\IngresoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIngresos extends ListRecords
{
    protected static string $resource = IngresoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\SostenibilidadResource\Pages;

use App\Filament\Resources\SostenibilidadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSostenibilidads extends ListRecords
{
    protected static string $resource = SostenibilidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\TrabajadoresResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\TrabajadoresResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrabajadores extends ListRecords
{
    protected static string $resource = TrabajadoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

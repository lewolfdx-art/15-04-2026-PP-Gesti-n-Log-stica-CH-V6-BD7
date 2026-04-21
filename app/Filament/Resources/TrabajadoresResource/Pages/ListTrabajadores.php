<?php

namespace App\Filament\Resources\TrabajadoresResource\Pages;

use App\Filament\Resources\TrabajadoresResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrabajadores extends ListRecords
{
    protected static string $resource = TrabajadoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\InicioResource\Pages;

use App\Filament\Resources\InicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInicios extends ListRecords
{
    protected static string $resource = InicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

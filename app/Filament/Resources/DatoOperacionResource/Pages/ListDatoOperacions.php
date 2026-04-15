<?php

namespace App\Filament\Resources\DatoOperacionResource\Pages;

use App\Filament\Resources\DatoOperacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDatoOperacions extends ListRecords
{
    protected static string $resource = DatoOperacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

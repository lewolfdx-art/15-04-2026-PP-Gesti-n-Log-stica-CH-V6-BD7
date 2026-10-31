<?php

namespace App\Filament\Resources\ContratoResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ContratoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContratos extends ListRecords
{
    protected static string $resource = ContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

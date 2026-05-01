<?php

namespace App\Filament\Resources\LibroReclamacionResource\Pages;

use App\Filament\Resources\LibroReclamacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibroReclamacions extends ListRecords
{
    protected static string $resource = LibroReclamacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

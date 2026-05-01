<?php

namespace App\Filament\Resources\LibroReclamacionResource\Pages;

use App\Filament\Resources\LibroReclamacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLibroReclamacion extends EditRecord
{
    protected static string $resource = LibroReclamacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

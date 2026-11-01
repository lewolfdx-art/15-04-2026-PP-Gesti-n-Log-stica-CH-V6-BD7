<?php

namespace App\Filament\Resources\ProductoResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ProductoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProducto extends EditRecord
{
    protected static string $resource = ProductoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

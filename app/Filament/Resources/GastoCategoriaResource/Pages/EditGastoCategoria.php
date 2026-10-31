<?php

namespace App\Filament\Resources\GastoCategoriaResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\GastoCategoriaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGastoCategoria extends EditRecord
{
    protected static string $resource = GastoCategoriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
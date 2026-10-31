<?php

namespace App\Filament\Resources\TrabajadoresResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\TrabajadoresResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrabajadores extends EditRecord
{
    protected static string $resource = TrabajadoresResource::class;

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
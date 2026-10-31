<?php

namespace App\Filament\Resources\InicioResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\InicioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInicio extends EditRecord
{
    protected static string $resource = InicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

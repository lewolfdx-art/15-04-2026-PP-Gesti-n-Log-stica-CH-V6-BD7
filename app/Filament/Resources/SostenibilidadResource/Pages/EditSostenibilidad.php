<?php

namespace App\Filament\Resources\SostenibilidadResource\Pages;

use App\Filament\Resources\SostenibilidadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSostenibilidad extends EditRecord
{
    protected static string $resource = SostenibilidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

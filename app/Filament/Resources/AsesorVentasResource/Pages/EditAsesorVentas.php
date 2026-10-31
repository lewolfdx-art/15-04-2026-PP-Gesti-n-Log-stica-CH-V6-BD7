<?php

namespace App\Filament\Resources\AsesorVentasResource\Pages;

use App\Filament\Resources\AsesorVentasResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAsesorVentas extends EditRecord
{
    protected static string $resource = AsesorVentasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['nombre_completo'] = strtoupper(trim($data['nombre_completo']));

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

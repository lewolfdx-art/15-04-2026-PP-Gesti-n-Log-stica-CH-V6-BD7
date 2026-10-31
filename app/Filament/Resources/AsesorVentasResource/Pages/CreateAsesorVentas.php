<?php

namespace App\Filament\Resources\AsesorVentasResource\Pages;

use App\Filament\Resources\AsesorVentasResource;
use App\Models\Trabajador;
use Filament\Resources\Pages\CreateRecord;

class CreateAsesorVentas extends CreateRecord
{
    protected static string $resource = AsesorVentasResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Mismo formato que al crear un asesor desde Contratos o Ingresos
        $data['tipo_cargo'] = Trabajador::CARGO_ASESOR;
        $data['nombre_completo'] = strtoupper(trim($data['nombre_completo']));

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

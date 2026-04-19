<?php

namespace App\Filament\Resources\ContratoResource\Pages;

use App\Filament\Resources\ContratoResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContrato extends CreateRecord
{
    protected static string $resource = ContratoResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['bomba_adicional']       ??= 0;
        $data['volumen_sobrante']      ??= 0;
        $data['comision_igv']          ??= 0;
        $data['gastos_alimentos_bomba']??= 0;
        $data['descuentos']            ??= 0;
        $data['descuento_guias']       ??= 0;
        $data['bomba']                 ??= 0;
        $data['volumen_guia']          ??= 0;
        $data['volumen_real']          ??= 0;
        $data['comision']              ??= 0;

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
<?php

namespace App\Filament\Resources\ContratoResource\Pages;

use App\Filament\Resources\ContratoResource;
use App\Filament\Resources\ContratoResource\Widgets\PagosContratoWidget;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewContrato extends ViewRecord
{
    protected static string $resource = ContratoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContratoResource::accionRegistrarPago(),
            EditAction::make()->label('Editar'),
        ];
    }

    // Total, pagado, saldo y lista de pagos arriba del contrato
    protected function getHeaderWidgets(): array
    {
        return [PagosContratoWidget::class];
    }
}

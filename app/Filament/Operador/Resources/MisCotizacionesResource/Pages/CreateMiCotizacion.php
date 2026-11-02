<?php

namespace App\Filament\Operador\Resources\MisCotizacionesResource\Pages;

use App\Filament\Operador\Resources\MisCotizacionesResource;
use App\Services\NotificationService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateMiCotizacion extends CreateRecord
{
    protected static string $resource = MisCotizacionesResource::class;

    protected static ?string $title = 'Nueva cotización';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $asesorId = MisCotizacionesResource::asesorId();

        if (! $asesorId) {
            Notification::make()
                ->title('Tu usuario no está vinculado a un asesor de ventas')
                ->body('Pide al administrador que te dé acceso desde Asesores de ventas.')
                ->danger()
                ->send();
            $this->halt();
        }

        $data['asesor_id'] = $asesorId;

        return MisCotizacionesResource::limpiarAdelantoSiNoHay($data, $this->data);
    }

    protected function afterCreate(): void
    {
        NotificationService::notificarNuevaCotizacion($this->record);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return "Cotización {$this->record->codigo} enviada a la oficina";
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}

<?php

namespace App\Filament\Resources\PagoPersonalResource\Pages;

use App\Filament\Resources\PagoPersonalResource;
use App\Models\PagoPersonal;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class CreatePagoPersonal extends CreateRecord
{
    protected static string $resource = PagoPersonalResource::class;

    protected static ?string $title = 'Registrar pago';

    /**
     * El monto se recalcula en el servidor (comisiones de contratos, total, quincena repetida)
     */
    protected function handleRecordCreation(array $data): Model
    {
        try {
            return PagoPersonal::registrar($data, auth()->user());
        } catch (InvalidArgumentException $e) {
            Notification::make()->title('No se pudo registrar el pago')->body($e->getMessage())->danger()->send();
            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}

<?php

namespace App\Filament\Operador\Resources\MisObrasResource\Pages;

use App\Filament\Operador\Resources\MisObrasResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewMiObra extends ViewRecord
{
    protected static string $resource = MisObrasResource::class;

    public function getTitle(): string
    {
        return 'Obra del ' . $this->record->fecha?->format('d/m/Y');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('como_llegar')
                ->label('Cómo llegar')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url($this->record->urlComoLlegar(), shouldOpenInNewTab: true)
                ->visible($this->record->tieneUbicacion()),
        ];
    }
}

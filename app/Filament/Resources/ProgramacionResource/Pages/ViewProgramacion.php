<?php

namespace App\Filament\Resources\ProgramacionResource\Pages;

use App\Filament\Resources\ProgramacionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewProgramacion extends ViewRecord
{
    protected static string $resource = ProgramacionResource::class;

    public function getTitle(): string
    {
        return "Vaciado · {$this->record->nombre_cliente}";
    }

    protected function getHeaderActions(): array
    {
        return [ProgramacionResource::accionProgramar()];
    }
}

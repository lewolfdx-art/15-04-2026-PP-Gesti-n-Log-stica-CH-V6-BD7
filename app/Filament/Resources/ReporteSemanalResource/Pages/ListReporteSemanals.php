<?php

namespace App\Filament\Resources\ReporteSemanalResource\Pages;

use App\Filament\Resources\ReporteSemanalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReporteSemanals extends ListRecords
{
    protected static string $resource = ReporteSemanalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
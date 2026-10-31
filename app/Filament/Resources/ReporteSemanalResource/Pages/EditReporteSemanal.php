<?php

namespace App\Filament\Resources\ReporteSemanalResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\ReporteSemanalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReporteSemanal extends EditRecord
{
    protected static string $resource = ReporteSemanalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
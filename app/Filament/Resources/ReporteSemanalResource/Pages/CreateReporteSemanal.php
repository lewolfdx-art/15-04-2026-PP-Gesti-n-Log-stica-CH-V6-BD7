<?php

namespace App\Filament\Resources\ReporteSemanalResource\Pages;

use App\Filament\Resources\ReporteSemanalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReporteSemanal extends CreateRecord
{
    protected static string $resource = ReporteSemanalResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

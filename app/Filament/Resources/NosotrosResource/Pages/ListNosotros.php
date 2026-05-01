<?php

namespace App\Filament\Resources\NosotrosResource\Pages;

use App\Filament\Resources\NosotrosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNosotros extends ListRecords
{
    protected static string $resource = NosotrosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

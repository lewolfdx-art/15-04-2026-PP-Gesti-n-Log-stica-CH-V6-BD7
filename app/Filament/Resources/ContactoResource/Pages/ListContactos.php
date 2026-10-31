<?php

namespace App\Filament\Resources\ContactoResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ContactoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactos extends ListRecords
{
    protected static string $resource = ContactoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

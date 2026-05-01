<?php

namespace App\Filament\Resources\NosotrosResource\Pages;

use App\Filament\Resources\NosotrosResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNosotros extends EditRecord
{
    protected static string $resource = NosotrosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\GastoResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\TextSize;
use App\Filament\Resources\GastoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Components\TextEntry;   // ← Agregado para usar TextEntrySize

class ViewGasto extends ViewRecord
{
    protected static string $resource = GastoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar Gasto'),
            
            DeleteAction::make()
                ->label('Eliminar')
                ->color('danger'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Gasto')
                    ->schema([
                        TextEntry::make('fecha')
                            ->label('Fecha')
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar'),

                        TextEntry::make('categoria.nombre')
                            ->label('Categoría'),

                        TextEntry::make('subcategoria')
                            ->label('Subcategoría'),

                        TextEntry::make('responsable')
                            ->label('Responsable'),
                    ])
                    ->columns(2),

                Section::make('Detalle')
                    ->schema([
                        TextEntry::make('detalle')
                            ->label('Detalle del gasto')
                            ->columnSpanFull(),

                        TextEntry::make('importe')
                            ->label('Importe')
                            ->money('PEN')
                            ->size(TextSize::Large),   // ← Corregido

                        TextEntry::make('obs')
                            ->label('Observación / Forma de pago'),
                    ])
                    ->columns(2),

                Section::make('Información del sistema')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Fecha de registro')
                            ->dateTime('d/m/Y H:i:s'),

                        TextEntry::make('updated_at')
                            ->label('Última actualización')
                            ->dateTime('d/m/Y H:i:s'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
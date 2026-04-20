<?php

namespace App\Filament\Resources\GastoResource\Pages;

use App\Filament\Resources\GastoResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;   // ← Agregado para usar TextEntrySize

class ViewGasto extends ViewRecord
{
    protected static string $resource = GastoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar Gasto'),
            
            Actions\DeleteAction::make()
                ->label('Eliminar')
                ->color('danger'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información del Gasto')
                    ->schema([
                        Infolists\Components\TextEntry::make('fecha')
                            ->label('Fecha')
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar'),

                        Infolists\Components\TextEntry::make('categoria.nombre')
                            ->label('Categoría'),

                        Infolists\Components\TextEntry::make('subcategoria')
                            ->label('Subcategoría'),

                        Infolists\Components\TextEntry::make('responsable')
                            ->label('Responsable'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detalle')
                    ->schema([
                        Infolists\Components\TextEntry::make('detalle')
                            ->label('Detalle del gasto')
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('importe')
                            ->label('Importe')
                            ->money('PEN')
                            ->size(TextEntry\TextEntrySize::Large),   // ← Corregido

                        Infolists\Components\TextEntry::make('obs')
                            ->label('Observación / Forma de pago'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Información del sistema')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Fecha de registro')
                            ->dateTime('d/m/Y H:i:s'),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Última actualización')
                            ->dateTime('d/m/Y H:i:s'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
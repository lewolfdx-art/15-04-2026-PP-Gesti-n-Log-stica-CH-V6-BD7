<?php

namespace App\Filament\Resources\ReporteSemanalResource\Pages;

use App\Filament\Resources\ReporteSemanalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class ViewReporteSemanal extends ViewRecord
{
    protected static string $resource = ReporteSemanalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información de la Semana')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('semana')
                                    ->label('Número de Semana')
                                    ->formatStateUsing(fn ($state) => "Semana {$state}"),
                                
                                TextEntry::make('fecha_desde')
                                    ->label('Fecha Desde')
                                    ->date('d/m/Y'),
                                
                                TextEntry::make('fecha_hasta')
                                    ->label('Fecha Hasta')
                                    ->date('d/m/Y'),
                            ]),
                    ]),
                
                Section::make('Datos del Pago')
                    ->schema([
                        TextEntry::make('proveedor')
                            ->label('Proveedor'),
                        
                        TextEntry::make('detalle')
                            ->label('Detalle'),
                        
                        TextEntry::make('monto')
                            ->label('Monto')
                            ->money('PEN'),
                        
                        TextEntry::make('estado')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'CANCELADO' => 'success',
                                'DEBE' => 'danger',
                                'ADELANTO' => 'warning',
                                default => 'gray',
                            }),
                    ]),
            ]);
    }
}
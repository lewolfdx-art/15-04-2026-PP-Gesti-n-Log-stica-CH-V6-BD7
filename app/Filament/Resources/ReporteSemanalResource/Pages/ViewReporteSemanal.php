<?php

namespace App\Filament\Resources\ReporteSemanalResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Filament\Resources\ReporteSemanalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;

class ViewReporteSemanal extends ViewRecord
{
    protected static string $resource = ReporteSemanalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
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
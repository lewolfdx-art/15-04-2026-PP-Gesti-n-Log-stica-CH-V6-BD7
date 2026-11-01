<?php

namespace App\Filament\Resources\ProgramacionResource\Pages;

use App\Filament\Resources\ProgramacionResource;
use App\Models\Contrato;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProgramacion extends ListRecords
{
    protected static string $resource = ProgramacionResource::class;

    public function getTabs(): array
    {
        return [
            'por_programar' => Tab::make('Por programar')
                ->icon('heroicon-o-clock')
                ->badge(Contrato::whereNull('programado_at')->whereDate('fecha', '>=', today())->count() ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $q) => $q->whereNull('programado_at')->whereDate('fecha', '>=', today())),
            'programados' => Tab::make('Programados')
                ->icon('heroicon-o-calendar-days')
                ->modifyQueryUsing(fn (Builder $q) => $q->whereNotNull('programado_at')->whereDate('fecha', '>=', today())),
            'todos' => Tab::make('Todos'),
        ];
    }
}

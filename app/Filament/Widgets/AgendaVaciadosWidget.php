<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProgramacionResource;
use App\Models\Contrato;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * Vaciados de hoy y los próximos 6 días, en orden de fecha y hora.
 * Al tocar uno se abre en la Programación.
 */
class AgendaVaciadosWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'coordinador_de_vaciados']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Agenda de vaciados · próximos 7 días')
            ->query(fn () => Contrato::query()
                ->whereBetween('fecha', [today()->toDateString(), today()->addDays(6)->toDateString()])
                ->with(['vehiculos', 'operadores'])
                ->orderBy('fecha')
                ->orderByRaw('hora_inicio IS NULL, hora_inicio'))
            ->columns([
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->formatStateUsing(fn (Contrato $record): string => ucfirst($record->fecha->locale('es')->isoFormat('ddd D/MM')))
                    ->description(fn (Contrato $record): ?string => $record->horarioTexto())
                    ->weight('bold'),
                TextColumn::make('nombre_cliente')->label('Cliente')->limit(26),
                TextColumn::make('ubicacion_referencia')->label('Lugar')->limit(30)->placeholder('—'),
                TextColumn::make('volumen_guia')->label('m³')->numeric(1),
                TextColumn::make('vehiculos.codigo')->label('Vehículos')->badge()->color('info')->placeholder('—'),
                TextColumn::make('operadores.nombre_completo')->label('Operadores')->badge()->color('gray')->placeholder('—'),
                TextColumn::make('programado_at')
                    ->label('Estado')
                    ->badge()
                    ->state(fn (Contrato $record): string => $record->estaProgramado() ? 'Programado' : 'Por programar')
                    ->color(fn (Contrato $record): string => $record->estaProgramado() ? 'success' : 'warning')
                    ->icon(fn (Contrato $record): string => $record->estaProgramado() ? 'heroicon-o-check-circle' : 'heroicon-o-clock'),
            ])
            ->recordUrl(fn (Contrato $record): string => ProgramacionResource::getUrl('view', ['record' => $record]))
            ->paginated(false)
            ->emptyStateIcon('heroicon-o-calendar')
            ->emptyStateHeading('No hay vaciados en los próximos 7 días');
    }
}

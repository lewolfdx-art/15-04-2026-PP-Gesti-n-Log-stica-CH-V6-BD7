<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CotizacionResource;
use App\Models\Cotizacion;
use App\Models\PagoPersonal;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

/**
 * Clientes que separaron fecha con adelanto y aún no son contrato: ese horario ya está comprometido.
 * Se muestra en el Escritorio y en Programación de vaciados.
 */
class FechasSeparadasWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'coordinador_de_vaciados']) ?? false;
    }

    public function table(Table $table): Table
    {
        $esAdmin = auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;

        return $table
            ->heading('Fechas separadas con adelanto')
            ->description('Clientes que ya separaron su horario y aún no pasan a contrato. No comprometas esos horarios con otro cliente.')
            ->query(fn () => Cotizacion::separadas()
                ->whereDate('fecha_tentativa', '>=', today())
                ->with('asesor')
                ->orderBy('fecha_tentativa')
                ->orderBy('hora_tentativa'))
            ->columns([
                TextColumn::make('fecha_tentativa')
                    ->label('Fecha')
                    ->formatStateUsing(fn (Cotizacion $record): string => ucfirst($record->fecha_tentativa->locale('es')->isoFormat('ddd D/MM')))
                    ->description(fn (Cotizacion $record): ?string => $record->horaTexto())
                    ->weight('bold'),
                TextColumn::make('cliente_nombre')->label('Cliente')->limit(26),
                TextColumn::make('ubicacion_referencia')->label('Lugar')->limit(30)->placeholder('—'),
                TextColumn::make('volumen_estimado')->label('m³')->numeric(1),
                TextColumn::make('adelanto_monto')
                    ->label('Adelanto')
                    ->money('PEN')
                    ->description(fn (Cotizacion $record): ?string => PagoPersonal::FORMAS_PAGO[$record->adelanto_forma_pago] ?? null),
                TextColumn::make('asesor.nombre_completo')->label('Asesor')->placeholder('—'),
            ])
            ->recordUrl($esAdmin ? fn (Cotizacion $record): string => CotizacionResource::getUrl('view', ['record' => $record]) : null)
            ->paginated(false)
            ->emptyStateIcon('heroicon-o-calendar')
            ->emptyStateHeading('No hay fechas separadas pendientes');
    }
}

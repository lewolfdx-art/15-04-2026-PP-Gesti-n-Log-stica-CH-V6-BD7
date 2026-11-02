<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoPersonalResource\Pages\CreatePagoPersonal;
use App\Filament\Resources\PagoPersonalResource\Pages\ListPagosPersonal;
use App\Filament\Resources\PagoPersonalResource\Pages\ViewPagoPersonal;
use App\Models\Contrato;
use App\Models\PagoPersonal;
use App\Models\Trabajador;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

/**
 * Pagos quincenales: sueldo fijo para el personal y comisión por contratos para los asesores.
 * Los montos se calculan en PagoPersonal::registrar(); el formulario solo muestra la vista previa.
 */
class PagoPersonalResource extends Resource
{
    use \App\Filament\Concerns\SoloAdministradores;

    protected static ?string $model = PagoPersonal::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Personal';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pagos al personal';
    protected static ?string $modelLabel = 'Pago';
    protected static ?string $pluralModelLabel = 'Pagos al personal';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'pagos-personal';

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    /**
     * Solo se puede anular un pago que aún no se entregó (libera los contratos)
     */
    public static function canDelete(Model $record): bool
    {
        return ! $record->estaPagado();
    }

    public static function getNavigationBadge(): ?string
    {
        $pendientes = PagoPersonal::pendientes()->count();

        return $pendientes > 0 ? (string) $pendientes : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pagos registrados aún no entregados';
    }

    private static function meses(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $m) => [$m => ucfirst(Carbon::create(2000, $m, 1)->locale('es')->monthName)])
            ->all();
    }

    private static function recalcularMonto(Get $get, Set $set): void
    {
        $trabajador = Trabajador::find($get('trabajador_id'));

        if ($trabajador && $get('anio') && $get('mes') && $get('quincena')) {
            $set('monto_base', PagoPersonal::montoBase($trabajador, (int) $get('anio'), (int) $get('mes'), (int) $get('quincena')));

            // Asesor: arranca con todos los contratos pendientes marcados
            if (PagoPersonal::tipoPara($trabajador) === 'comision') {
                [, $hasta] = PagoPersonal::periodo((int) $get('anio'), (int) $get('mes'), (int) $get('quincena'));
                $set('contratos_ids', PagoPersonal::contratosPendientesDeComision($trabajador, $hasta)->modelKeys());
            }
        }
    }

    /**
     * Total de comisiones = suma de los contratos marcados
     */
    private static function sumarContratosMarcados(Get $get, Set $set): void
    {
        $ids = array_map('intval', (array) $get('contratos_ids'));

        $set('monto_base', round((float) Contrato::whereKey($ids)->whereNull('pago_comision_id')->sum('comision'), 2));
    }

    public static function form(Schema $schema): Schema
    {
        $recalcular = fn (Get $get, Set $set) => static::recalcularMonto($get, $set);
        $trabajador = fn (Get $get): ?Trabajador => Trabajador::find($get('trabajador_id'));
        $esComision = fn (Get $get): bool => ($t = $trabajador($get)) && PagoPersonal::tipoPara($t) === 'comision';
        // Con contratos pendientes el total sale de ellos; sin contratos se escribe a mano
        $comisionCalculada = function (Get $get) use ($trabajador, $esComision): bool {
            if (! $esComision($get) || ! $get('anio') || ! $get('mes') || ! $get('quincena')) {
                return false;
            }

            [, $hasta] = PagoPersonal::periodo((int) $get('anio'), (int) $get('mes'), (int) $get('quincena'));

            return PagoPersonal::contratosPendientesDeComision($trabajador($get), $hasta)->isNotEmpty();
        };

        return $schema
            ->components([
                Section::make('Trabajador y quincena')
                    ->schema([
                        Select::make('trabajador_id')
                            ->label('Trabajador')
                            ->options(fn () => Trabajador::where('activo', true)->orderBy('nombre_completo')->get()
                                ->mapWithKeys(fn (Trabajador $t) => [$t->id => $t->nombre_completo . ' · ' . Trabajador::cargoTexto($t->tipo_cargo)]))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated($recalcular)
                            ->columnSpanFull(),

                        Select::make('anio')
                            ->label('Año')
                            ->options(fn () => collect(range(now()->year - 1, now()->year + 1))->mapWithKeys(fn ($a) => [$a => $a])->all())
                            ->default(now()->year)
                            ->required()
                            ->live()
                            ->afterStateUpdated($recalcular),

                        Select::make('mes')
                            ->label('Mes')
                            ->options(static::meses())
                            ->default(now()->month)
                            ->required()
                            ->live()
                            ->afterStateUpdated($recalcular),

                        Select::make('quincena')
                            ->label('Quincena')
                            ->options([1 => '1ra (del 1 al 15)', 2 => '2da (del 16 a fin de mes)'])
                            ->default(now()->day <= 15 ? 1 : 2)
                            ->required()
                            ->live()
                            ->afterStateUpdated($recalcular),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Cálculo del pago')
                    ->visible(fn (Get $get): bool => filled($get('trabajador_id')))
                    ->schema([
                        TextEntry::make('tipo_info')
                            ->hiddenLabel()
                            ->state(function (Get $get) use ($trabajador): HtmlString {
                                $t = $trabajador($get);
                                if (! $t) {
                                    return new HtmlString('');
                                }

                                $esComision = PagoPersonal::tipoPara($t) === 'comision';
                                $icono = svg($esComision ? 'heroicon-o-briefcase' : 'heroicon-o-user', 'h-5 w-5 shrink-0 text-primary-500')->toHtml();

                                return new HtmlString('<span style="display:inline-flex;align-items:flex-start;gap:6px">' . $icono . '<span>' . ($esComision
                                    ? '<b>Asesor de ventas</b>: se paga la comisión de sus contratos que aún no fueron pagados, incluidos los de quincenas anteriores.'
                                    : '<b>Sueldo fijo</b>: ' . ($t->sueldo_mensual
                                        ? 'sueldo mensual S/ ' . number_format((float) $t->sueldo_mensual, 2) . ', la quincena es la mitad. Puedes ajustarla.'
                                        : '<span style="color:#dc2626">no tiene sueldo registrado en su ficha</span>; ingresa el monto de la quincena.')) . '</span></span>');
                            })
                            ->columnSpanFull(),

                        CheckboxList::make('contratos_ids')
                            ->label('Contratos con comisión pendiente')
                            ->helperText('Marca solo las obras que vas a liquidar ahora. Las demás siguen pendientes para el próximo pago.')
                            ->visible($comisionCalculada)
                            ->options(function (Get $get) use ($trabajador): array {
                                [, $hasta] = PagoPersonal::periodo((int) $get('anio'), (int) $get('mes'), (int) $get('quincena'));

                                return PagoPersonal::contratosPendientesDeComision($trabajador($get), $hasta)
                                    ->mapWithKeys(fn (Contrato $c) => [$c->id => ($c->numero_contrato ?: 'Sin N.°') . ' · ' . $c->nombre_cliente
                                        . ' · S/ ' . number_format((float) $c->comision, 2)])
                                    ->all();
                            })
                            ->descriptions(function (Get $get) use ($trabajador): array {
                                [, $hasta] = PagoPersonal::periodo((int) $get('anio'), (int) $get('mes'), (int) $get('quincena'));

                                return PagoPersonal::contratosPendientesDeComision($trabajador($get), $hasta)
                                    ->mapWithKeys(fn (Contrato $c) => [$c->id => 'Vaciado ' . $c->fecha?->format('d/m/Y') . ' · Pago del cliente: ' . ($c->estado_pago ?: '—')])
                                    ->all();
                            })
                            ->bulkToggleable()
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => static::sumarContratosMarcados($get, $set))
                            ->columnSpanFull(),

                        TextEntry::make('sin_contratos')
                            ->hiddenLabel()
                            ->visible(fn (Get $get): bool => $esComision($get) && ! $comisionCalculada($get))
                            ->state(fn (Get $get): HtmlString => new HtmlString('<span style="color:#6b7280">No tiene contratos con comisión pendiente hasta el '
                                . PagoPersonal::periodo((int) $get('anio'), (int) $get('mes'), (int) $get('quincena'))[1]->format('d/m/Y')
                                . '. Si se le debe algo, escribe el monto abajo.</span>'))
                            ->columnSpanFull(),

                        TextInput::make('monto_base')
                            ->label(fn (Get $get) => $esComision($get) ? 'Total de comisiones' : 'Sueldo de la quincena')
                            ->helperText(fn (Get $get): ?string => $comisionCalculada($get) ? 'Suma de los contratos marcados.' : null)
                            ->prefix('S/')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->readOnly($comisionCalculada)
                            ->live(onBlur: true),

                        TextInput::make('adicionales')
                            ->label('Adicionales')
                            ->prefix('S/')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true),

                        TextInput::make('concepto_adicionales')
                            ->label('Motivo de adicionales')
                            ->placeholder('Ej: horas extra, movilidad')
                            ->maxLength(255),

                        TextInput::make('descuentos')
                            ->label('Descuentos')
                            ->prefix('S/')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->live(onBlur: true),

                        TextInput::make('concepto_descuentos')
                            ->label('Motivo de descuentos')
                            ->placeholder('Ej: adelanto del 10/10')
                            ->maxLength(255),

                        TextEntry::make('total_preview')
                            ->label('Total a pagar')
                            ->state(fn (Get $get): string => 'S/ ' . number_format(
                                (float) $get('monto_base') + (float) $get('adicionales') - (float) $get('descuentos'), 2))
                            ->size('lg')
                            ->weight('bold')
                            ->color('success'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make('Entrega')
                    ->visible(fn (Get $get): bool => filled($get('trabajador_id')))
                    ->schema([
                        Select::make('forma_pago')
                            ->label('Forma de pago')
                            ->options(PagoPersonal::FORMAS_PAGO)
                            ->default('EFECTIVO'),

                        Toggle::make('marcar_pagado')
                            ->label('Ya se entregó el pago')
                            ->helperText('Si lo dejas apagado, queda como pendiente de entrega.')
                            ->default(true)
                            ->inline(false),

                        Textarea::make('observacion')
                            ->label('Observación')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function accionComprobante(): Action
    {
        return Action::make('comprobante')
            ->label('Comprobante')
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->url(fn (PagoPersonal $record): string => route('pagos-personal.comprobante', $record), shouldOpenInNewTab: true);
    }

    public static function accionMarcarPagado(): Action
    {
        return Action::make('marcar_pagado')
            ->label('Marcar entregado')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (PagoPersonal $record): bool => ! $record->estaPagado())
            ->schema([
                Select::make('forma_pago')
                    ->label('Forma de pago')
                    ->options(PagoPersonal::FORMAS_PAGO)
                    ->default(fn (PagoPersonal $record) => $record->forma_pago ?? 'EFECTIVO')
                    ->required(),
            ])
            ->action(function (PagoPersonal $record, array $data): void {
                $record->update(['pagado_at' => now(), 'forma_pago' => $data['forma_pago']]);

                Notification::make()->title("Pago {$record->codigo} marcado como entregado")->success()->send();
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')
                    ->label('N.°')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('trabajador.nombre_completo')
                    ->label('Trabajador')
                    ->searchable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => PagoPersonal::TIPOS[$state] ?? $state)
                    ->color(fn (string $state): string => $state === 'comision' ? 'info' : 'gray'),

                TextColumn::make('periodo')
                    ->label('Quincena')
                    ->state(fn (PagoPersonal $record): string => $record->periodoTexto()),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('PEN')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('pagado_at')
                    ->label('Estado')
                    ->badge()
                    ->state(fn (PagoPersonal $record): string => $record->estaPagado() ? 'Entregado' : 'Pendiente')
                    ->color(fn (PagoPersonal $record): string => $record->estaPagado() ? 'success' : 'warning')
                    ->description(fn (PagoPersonal $record): ?string => $record->pagado_at?->format('d/m/Y')),

                TextColumn::make('forma_pago')
                    ->label('Forma de pago')
                    ->formatStateUsing(fn (?string $state): string => PagoPersonal::FORMAS_PAGO[$state] ?? '—')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('tipo')->label('Tipo')->options(PagoPersonal::TIPOS),
                SelectFilter::make('trabajador_id')->label('Trabajador')->relationship('trabajador', 'nombre_completo')->searchable(),
                TernaryFilter::make('entregado')
                    ->label('Estado')
                    ->trueLabel('Entregados')
                    ->falseLabel('Pendientes')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('pagado_at'),
                        false: fn (Builder $query) => $query->whereNull('pagado_at'),
                    ),
            ])
            ->recordActions([
                static::accionComprobante(),
                static::accionMarcarPagado(),
                ViewAction::make(),
                DeleteAction::make()
                    ->label('Anular')
                    ->modalHeading('Anular pago')
                    ->modalDescription('Solo se pueden anular pagos no entregados. Las comisiones vuelven a quedar pendientes.'),
            ])
            ->emptyStateHeading('Sin pagos registrados')
            ->emptyStateDescription('Registra el sueldo o la comisión de la quincena con el botón de arriba.');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pago')
                    ->schema([
                        TextEntry::make('codigo')->label('N.°')->weight('bold'),
                        TextEntry::make('trabajador.nombre_completo')->label('Trabajador'),
                        TextEntry::make('tipo')->label('Tipo')->formatStateUsing(fn (string $state): string => PagoPersonal::TIPOS[$state] ?? $state),
                        TextEntry::make('periodo')->label('Quincena')->state(fn (PagoPersonal $record): string => $record->periodoTexto()),
                        TextEntry::make('monto_base')->label('Monto base')->money('PEN'),
                        TextEntry::make('adicionales')->label('Adicionales')->money('PEN')
                            ->helperText(fn (PagoPersonal $record): ?string => $record->concepto_adicionales),
                        TextEntry::make('descuentos')->label('Descuentos')->money('PEN')
                            ->helperText(fn (PagoPersonal $record): ?string => $record->concepto_descuentos),
                        TextEntry::make('total')->label('Total')->money('PEN')->weight('bold')->color('success'),
                        TextEntry::make('forma_pago')->label('Forma de pago')->formatStateUsing(fn (?string $state): string => PagoPersonal::FORMAS_PAGO[$state] ?? '—'),
                        TextEntry::make('pagado_at')->label('Entregado el')->dateTime('d/m/Y H:i')->placeholder('Pendiente de entrega'),
                        TextEntry::make('registradoPor.name')->label('Registrado por')->placeholder('—'),
                        TextEntry::make('observacion')->label('Observación')->placeholder('—')->columnSpanFull(),
                    ])
                    ->columns(['default' => 2, 'lg' => 4])
                    ->columnSpanFull(),

                Section::make('Contratos incluidos')
                    ->visible(fn (PagoPersonal $record): bool => $record->tipo === 'comision')
                    ->schema([
                        RepeatableEntry::make('contratos')
                            ->hiddenLabel()
                            ->table([
                                RepeatableEntry\TableColumn::make('Fecha'),
                                RepeatableEntry\TableColumn::make('N.° contrato'),
                                RepeatableEntry\TableColumn::make('Cliente'),
                                RepeatableEntry\TableColumn::make('Pago del cliente'),
                                RepeatableEntry\TableColumn::make('Comisión'),
                            ])
                            ->schema([
                                TextEntry::make('fecha')->date('d/m/Y'),
                                TextEntry::make('numero_contrato')->placeholder('—'),
                                TextEntry::make('nombre_cliente'),
                                TextEntry::make('estado_pago')->badge()->color(fn (?string $state): string => $state === 'PAGADO' ? 'success' : 'danger'),
                                TextEntry::make('comision')->money('PEN'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPagosPersonal::route('/'),
            'create' => CreatePagoPersonal::route('/create'),
            'view'   => ViewPagoPersonal::route('/{record}'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Carbon\Carbon;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use App\Filament\Resources\ReporteSemanalResource\Pages\ListReporteSemanals;
use App\Filament\Resources\ReporteSemanalResource\Pages\CreateReporteSemanal;
use App\Filament\Resources\ReporteSemanalResource\Pages\EditReporteSemanal;
use App\Filament\Resources\ReporteSemanalResource\Pages\ViewReporteSemanal;
use App\Filament\Resources\ReporteSemanalResource\Pages;
use App\Models\ReporteSemanal;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ReporteSemanalResource extends Resource
{
    protected static ?string $model = ReporteSemanal::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Reporte Semanal';
    protected static ?string $pluralLabel = 'Reportes Semanales';
    protected static string | \UnitEnum | null $navigationGroup = 'Finanzas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo_periodo')
                    ->label('Tipo de Periodo')
                    ->options([
                        'semanal' => 'Semanal',
                        'quincena' => 'Quincena',
                        'fin_mes' => 'Fin de Mes',
                    ])
                    ->required()
                    ->default('semanal')
                    ->live()
                    ->native(false),

                Select::make('semana')
                    ->label('Numero de Semana')
                    ->options([
                        1 => 'Semana 1',
                        2 => 'Semana 2',
                        3 => 'Semana 3',
                        4 => 'Semana 4',
                        5 => 'Semana 5',
                    ])
                    ->visible(fn (Get $get) => $get('tipo_periodo') === 'semanal')
                    ->required(fn (Get $get) => $get('tipo_periodo') === 'semanal')
                    ->live(),

                    DatePicker::make('fecha_desde')
                    ->label('Fecha Desde')
                    ->required(fn (Get $get) => $get('tipo_periodo') === 'semanal')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->maxDate(now())
                    ->closeOnDateSelection() // 👈 mejora UX
                    ->visible(fn (Get $get) => $get('tipo_periodo') === 'semanal')
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if ($state && $get('semana')) {
                            $fechaDesde = Carbon::parse($state);
                            $fechaHasta = $fechaDesde->copy()->addDays(6);
                            $set('fecha_hasta', $fechaHasta->format('Y-m-d'));
                        }
                    }),
                
                DatePicker::make('fecha_hasta')
                    ->label('Fecha Hasta')
                    ->required(fn (Get $get) => $get('tipo_periodo') === 'semanal')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->maxDate(now())
                    ->closeOnDateSelection()
                    ->visible(fn (Get $get) => $get('tipo_periodo') === 'semanal'),

                Select::make('quincena')
                    ->label('Quincena')
                    ->options([
                        'PRIMERA' => 'Primera Quincena (1-15)',
                        'SEGUNDA' => 'Segunda Quincena (16-30/31)',
                    ])
                    ->visible(fn (Get $get) => $get('tipo_periodo') === 'quincena')
                    ->required(fn (Get $get) => $get('tipo_periodo') === 'quincena')
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        if ($state && $get('mes') && $get('año')) {
                            $mes = $get('mes');
                            $año = $get('año');
                            $fecha = Carbon::createFromDate($año, $mes, 1);
                            
                            if ($state === 'PRIMERA') {
                                $set('fecha_desde_quincena', $fecha->format('Y-m-d'));
                                $set('fecha_hasta_quincena', $fecha->copy()->day(15)->format('Y-m-d'));
                            } else {
                                $ultimoDia = $fecha->copy()->endOfMonth()->day;
                                $set('fecha_desde_quincena', $fecha->copy()->day(16)->format('Y-m-d'));
                                $set('fecha_hasta_quincena', $fecha->copy()->day($ultimoDia)->format('Y-m-d'));
                            }
                        }
                    }),

                Select::make('mes')
                    ->label('Mes')
                    ->options([
                        1 => 'Enero',
                        2 => 'Febrero',
                        3 => 'Marzo',
                        4 => 'Abril',
                        5 => 'Mayo',
                        6 => 'Junio',
                        7 => 'Julio',
                        8 => 'Agosto',
                        9 => 'Septiembre',
                        10 => 'Octubre',
                        11 => 'Noviembre',
                        12 => 'Diciembre',
                    ])
                    ->visible(fn (Get $get) => in_array($get('tipo_periodo'), ['quincena', 'fin_mes']))
                    ->required(fn (Get $get) => in_array($get('tipo_periodo'), ['quincena', 'fin_mes']))
                    ->live()
                    ->native(false),

                TextInput::make('año')
                    ->label('Año')
                    ->numeric()
                    ->minValue(2020)
                    ->maxValue(2030)
                    ->default(now()->year)
                    ->visible(fn (Get $get) => in_array($get('tipo_periodo'), ['quincena', 'fin_mes']))
                    ->required(fn (Get $get) => in_array($get('tipo_periodo'), ['quincena', 'fin_mes']))
                    ->live(),

                DatePicker::make('fecha_desde_quincena')
                    ->label('Fecha Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->visible(fn (Get $get) => $get('tipo_periodo') === 'quincena')
                    ->disabled(),

                DatePicker::make('fecha_hasta_quincena')
                    ->label('Fecha Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->visible(fn (Get $get) => $get('tipo_periodo') === 'quincena')
                    ->disabled(),

                DatePicker::make('fecha_fin_mes')
                    ->label('Fecha de Corte')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->default(now()->endOfMonth())
                    ->visible(fn (Get $get) => $get('tipo_periodo') === 'fin_mes')
                    ->required(fn (Get $get) => $get('tipo_periodo') === 'fin_mes'),

                TextInput::make('proveedor')
                    ->label('Proveedor')
                    ->required()
                    ->maxLength(255),

                TextInput::make('detalle')
                    ->label('Detalle')
                    ->required()
                    ->maxLength(255),

                TextInput::make('monto')
                    ->label('Monto (S/)')
                    ->required()
                    ->numeric()
                    ->prefix('S/')
                    ->step(0.01),

                Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'CANCELADO' => 'Cancelado',
                        'DEBE' => 'Debe',
                        'ADELANTO' => 'Adelanto',
                    ])
                    ->required()
                    ->default('DEBE')
                    ->native(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo_periodo')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'semanal' => 'info',
                        'quincena' => 'success',
                        'fin_mes' => 'warning',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'semanal' => 'Semanal',
                        'quincena' => 'Quincena',
                        'fin_mes' => 'Fin de Mes',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
    
                TextColumn::make('semana')
                    ->label('Semana')
                    ->formatStateUsing(fn ($state) => $state ? "Semana {$state}" : '-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
    
                TextColumn::make('quincena')
                    ->label('Quincena')
                    ->formatStateUsing(fn ($state) => $state === 'PRIMERA' ? '1ra Quincena' : ($state === 'SEGUNDA' ? '2da Quincena' : '-'))
                    ->toggleable(isToggledHiddenByDefault: false),
    
                TextColumn::make('mes')
                    ->label('Mes/Año')
                    ->formatStateUsing(function ($record) {
                        if ($record->mes && $record->año) {
                            $meses = [
                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                            ];
                            return $meses[$record->mes] . ' ' . $record->año;
                        }
                        return '-';
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
    
                TextColumn::make('proveedor')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
    
                TextColumn::make('detalle')
                    ->label('Detalle')
                    ->limit(30)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
    
                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
    
                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'CANCELADO',
                        'danger' => 'DEBE',
                        'warning' => 'ADELANTO',
                    ])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            // Los filtros se leen del componente Livewire para que el resumen se actualice al filtrar
            ->header(function ($livewire) {
                $tableFilters = $livewire->tableFilters ?? [];

                $filtros = collect(['tipo_periodo', 'estado'])
                    ->mapWithKeys(fn ($campo) => [$campo => $tableFilters[$campo]['value'] ?? null])
                    ->filter(fn ($valor) => filled($valor))
                    ->all();

                return view('filament.components.resumen-pagos', self::getResumenPagos($filtros));
            })
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('tipo_periodo')
                    ->label('Tipo de Periodo')
                    ->options([
                        'semanal' => 'Semanal',
                        'quincena' => 'Quincena',
                        'fin_mes' => 'Fin de Mes',
                    ]),
                
                SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'CANCELADO' => 'Cancelado',
                        'DEBE' => 'Debe',
                        'ADELANTO' => 'Adelanto',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReporteSemanals::route('/'),
            'create' => CreateReporteSemanal::route('/create'),
            'edit' => EditReporteSemanal::route('/{record}/edit'),
            'view' => ViewReporteSemanal::route('/{record}'),
        ];
    }

    /**
     * Datos para el componente compartido resumen-pagos:
     * totales, pendiente por tipo de período y últimos pagos realizados.
     * Pendiente = DEBE + ADELANTO; pagado = CANCELADO.
     * La fecha de pago se toma de updated_at (no existe columna propia).
     */
    public static function getResumenPagos(array $filtros = []): array
    {
        $estadoFiltrado = $filtros['estado'] ?? null;
        $base = fn () => ReporteSemanal::query()->where(collect($filtros)->except('estado')->all());

        $pendientes = $base()
            ->whereIn('estado', $estadoFiltrado ? array_intersect([$estadoFiltrado], ['DEBE', 'ADELANTO']) : ['DEBE', 'ADELANTO'])
            ->get(['tipo_periodo', 'monto']);

        $desglose = $pendientes
            ->groupBy('tipo_periodo')
            ->mapWithKeys(fn ($items, $tipo) => [ReporteSemanal::tipoPeriodoTexto($tipo) => (float) $items->sum('monto')])
            ->sortDesc()
            ->all();

        $mostrarPagados = ! $estadoFiltrado || $estadoFiltrado === 'CANCELADO';

        $historial = $mostrarPagados
            ? $base()
                ->where('estado', 'CANCELADO')
                ->latest('updated_at')
                ->limit(15)
                ->get()
                ->map(fn (ReporteSemanal $reporte) => [
                    'fecha'     => $reporte->updated_at,
                    'titulo'    => trim((string) $reporte->proveedor),
                    'subtitulo' => $reporte->detalle,
                    'periodo'   => $reporte->periodoTexto(),
                    'monto'     => (float) $reporte->monto,
                ])
            : collect();

        return [
            'total_pendiente'     => (float) $pendientes->sum('monto'),
            'cantidad_pendientes' => $pendientes->count(),
            'pagado_mes'          => $mostrarPagados
                ? (float) $base()->where('estado', 'CANCELADO')
                    ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->sum('monto')
                : 0.0,
            'desglose_titulo'     => 'Pendiente por tipo de período',
            'desglose'            => $desglose,
            'columna_detalle'     => 'Proveedor',
            'historial'           => $historial,
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReporteSemanalResource\Pages;
use App\Models\ReporteSemanal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Builder;

class ReporteSemanalResource extends Resource
{
    protected static ?string $model = ReporteSemanal::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Reporte Semanal';
    protected static ?string $pluralLabel = 'Reportes Semanales';
    protected static ?string $navigationGroup = 'Finanzas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->visible(fn (Forms\Get $get) => $get('tipo_periodo') === 'semanal')
                    ->required(fn (Forms\Get $get) => $get('tipo_periodo') === 'semanal')
                    ->live(),

                DatePicker::make('fecha_desde')
                    ->label('Fecha Desde')
                    ->required(fn (Forms\Get $get) => $get('tipo_periodo') === 'semanal')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->maxDate(now())
                    ->visible(fn (Forms\Get $get) => $get('tipo_periodo') === 'semanal')
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        if ($state && $get('semana')) {
                            $fechaDesde = \Carbon\Carbon::parse($state);
                            $fechaHasta = $fechaDesde->copy()->addDays(6);
                            $set('fecha_hasta', $fechaHasta->format('Y-m-d'));
                        }
                    }),

                DatePicker::make('fecha_hasta')
                    ->label('Fecha Hasta')
                    ->required(fn (Forms\Get $get) => $get('tipo_periodo') === 'semanal')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->maxDate(now())
                    ->visible(fn (Forms\Get $get) => $get('tipo_periodo') === 'semanal'),

                Select::make('quincena')
                    ->label('Quincena')
                    ->options([
                        'PRIMERA' => 'Primera Quincena (1-15)',
                        'SEGUNDA' => 'Segunda Quincena (16-30/31)',
                    ])
                    ->visible(fn (Forms\Get $get) => $get('tipo_periodo') === 'quincena')
                    ->required(fn (Forms\Get $get) => $get('tipo_periodo') === 'quincena')
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        if ($state && $get('mes') && $get('anio')) {
                            $mes = $get('mes');
                            $anio = $get('anio');
                            $fecha = \Carbon\Carbon::createFromDate($anio, $mes, 1);
                            
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

                TextInput::make('mes')
                    ->label('Mes')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(12)
                    ->visible(fn (Forms\Get $get) => in_array($get('tipo_periodo'), ['quincena', 'fin_mes']))
                    ->required(fn (Forms\Get $get) => in_array($get('tipo_periodo'), ['quincena', 'fin_mes']))
                    ->live(),

                TextInput::make('anio')
                    ->label('Anio')
                    ->numeric()
                    ->minValue(2020)
                    ->maxValue(2030)
                    ->default(now()->year)
                    ->visible(fn (Forms\Get $get) => in_array($get('tipo_periodo'), ['quincena', 'fin_mes']))
                    ->required(fn (Forms\Get $get) => in_array($get('tipo_periodo'), ['quincena', 'fin_mes']))
                    ->live(),

                DatePicker::make('fecha_desde_quincena')
                    ->label('Fecha Desde')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->visible(fn (Forms\Get $get) => $get('tipo_periodo') === 'quincena')
                    ->disabled(),

                DatePicker::make('fecha_hasta_quincena')
                    ->label('Fecha Hasta')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->visible(fn (Forms\Get $get) => $get('tipo_periodo') === 'quincena')
                    ->disabled(),

                DatePicker::make('fecha_fin_mes')
                    ->label('Fecha de Corte')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->default(now()->endOfMonth())
                    ->visible(fn (Forms\Get $get) => $get('tipo_periodo') === 'fin_mes')
                    ->required(fn (Forms\Get $get) => $get('tipo_periodo') === 'fin_mes'),

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
                    }),
    
                TextColumn::make('semana')
                    ->label('Semana')
                    ->formatStateUsing(fn ($state) => $state ? "Semana {$state}" : '-')
                    ->sortable(),
    
                TextColumn::make('quincena')
                    ->label('Quincena')
                    ->formatStateUsing(fn ($state) => $state === 'PRIMERA' ? '1ra Quincena' : ($state === 'SEGUNDA' ? '2da Quincena' : '-')),
    
                TextColumn::make('mes')
                    ->label('Mes/Anio')
                    ->formatStateUsing(fn ($record) => $record->mes && $record->anio ? "{$record->mes}/{$record->anio}" : '-'),
    
                TextColumn::make('proveedor')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable(),
    
                TextColumn::make('detalle')
                    ->label('Detalle')
                    ->limit(30)
                    ->searchable(),
    
                TextColumn::make('monto')
                    ->label('Monto')
                    ->money('PEN')
                    ->sortable(),
    
                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'success' => 'CANCELADO',
                        'danger' => 'DEBE',
                        'warning' => 'ADELANTO',
                    ])
                    ->sortable(),
            ])
            ->header(function () {
                $totales = self::getTotalesAgrupados();
                return view('filament.components.resumen-totales', ['totales' => $totales]);
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReporteSemanals::route('/'),
            'create' => Pages\CreateReporteSemanal::route('/create'),
            'edit' => Pages\EditReporteSemanal::route('/{record}/edit'),
            'view' => Pages\ViewReporteSemanal::route('/{record}'),
        ];
    }

    // Método auxiliar para calcular totales agrupados
    public static function getTotalesAgrupados(): array
    {
        $query = ReporteSemanal::query();
        
        // Aplicar los mismos filtros que tiene la tabla
        if (request()->has('tableFilters')) {
            $filters = request()->input('tableFilters');
            
            if (isset($filters['tipo_periodo']['value'])) {
                $query->where('tipo_periodo', $filters['tipo_periodo']['value']);
            }
            
            if (isset($filters['estado']['value'])) {
                $query->where('estado', $filters['estado']['value']);
            }
        }
        
        // Filtrar solo registros no pagados (DEBE y ADELANTO)
        $query->whereIn('estado', ['DEBE', 'ADELANTO']);
        
        $registros = $query->get();
        
        $totales = [
            'semanal' => [],
            'quincenal' => [],
            'fin_mes' => [],
        ];
        
        foreach ($registros as $registro) {
            if ($registro->tipo_periodo === 'semanal') {
                $key = "Semana {$registro->semana} ({$registro->fecha_desde} - {$registro->fecha_hasta})";
                if (!isset($totales['semanal'][$key])) {
                    $totales['semanal'][$key] = 0;
                }
                $totales['semanal'][$key] += $registro->monto;
            } elseif ($registro->tipo_periodo === 'quincena') {
                $key = $registro->quincena === 'PRIMERA' ? 'Primera Quincena' : 'Segunda Quincena';
                $key .= " ({$registro->mes}/{$registro->anio})";
                if (!isset($totales['quincenal'][$key])) {
                    $totales['quincenal'][$key] = 0;
                }
                $totales['quincenal'][$key] += $registro->monto;
            } elseif ($registro->tipo_periodo === 'fin_mes') {
                $key = "Fin de Mes - {$registro->mes}/{$registro->anio}";
                if (!isset($totales['fin_mes'][$key])) {
                    $totales['fin_mes'][$key] = 0;
                }
                $totales['fin_mes'][$key] += $registro->monto;
            }
        }
        
        return $totales;
    }
}
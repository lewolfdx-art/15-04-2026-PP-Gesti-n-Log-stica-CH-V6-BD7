<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuentaPorPagarResource\Pages;
use App\Models\CuentaPorPagar;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class CuentaPorPagarResource extends Resource
{
    protected static ?string $model = CuentaPorPagar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Cuentas por Pagar';
    protected static ?string $modelLabel = 'Cuenta por Pagar';
    protected static ?string $pluralModelLabel = 'Cuentas por Pagar';
    protected static ?string $navigationGroup = 'Finanzas';

    public static function form(Form $form): Form
    {
        return $form->schema([

            TextInput::make('detalle')
                ->label('Detalle')
                ->required()
                ->columnSpanFull(),

            Select::make('tipo')
                ->label('Tipo')
                ->options([
                    'fecha'   => 'Fecha',
                    'mes'     => 'Mes',
                    'fin_mes' => 'Fin de Mes',
                    'viajes'  => 'Viajes',
                ])
                ->reactive()
                ->required(),

            DatePicker::make('fecha')
                ->label('Fecha')
                ->visible(fn ($get) => $get('tipo') === 'fecha'),

            Select::make('mes')
                ->label('Mes')
                ->options([
                    'enero'      => 'Enero',
                    'febrero'    => 'Febrero',
                    'marzo'      => 'Marzo',
                    'abril'      => 'Abril',
                    'mayo'       => 'Mayo',
                    'junio'      => 'Junio',
                    'julio'      => 'Julio',
                    'agosto'     => 'Agosto',
                    'septiembre' => 'Septiembre',
                    'octubre'    => 'Octubre',
                    'noviembre'  => 'Noviembre',
                    'diciembre'  => 'Diciembre',
                ])
                ->required(),

            Select::make('año')
                ->label('Año')
                ->options(function () {
                    $currentYear = now()->year;
                    return collect(range($currentYear - 2, $currentYear + 3))
                        ->mapWithKeys(fn ($y) => [$y => $y])
                        ->toArray();
                })
                ->default(now()->year)
                ->required(),

            TextInput::make('cantidad')
                ->label('Cantidad')
                ->placeholder('Ej: 4 LETRA, 2 cuotas, 1')
                ->maxLength(255),

            TextInput::make('total')
                ->label('Total')
                ->numeric()
                ->prefix('S/')
                ->required(),

            Toggle::make('pagado')
                ->label('Pagado')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('detalle')
                    ->label('Detalle')
                    ->searchable()
                    ->wrap()
                    ->toggleable(),

                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'fecha' => 'Fecha',
                        'mes' => 'Mes',
                        'fin_mes' => 'Fin de Mes',
                        'viajes' => 'Viajes',
                        default => $state,
                    })
                    ->color(fn (string $state): string => 
                        match($state) {
                            'fecha'   => 'info',
                            'mes'     => 'success',
                            'fin_mes' => 'warning',
                            'viajes'  => 'primary',
                            default   => 'gray',
                        }
                    )
                    ->toggleable(),

                TextColumn::make('mes')
                    ->label('Mes / Año')
                    ->formatStateUsing(fn ($state, $record) =>
                        $state ? ucfirst($state) . ' ' . $record->año : '-'
                    )
                    ->toggleable(),

                TextColumn::make('cantidad')
                    ->label('Cantidad')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('PEN')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('pagado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Pagado' : 'Debe')
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->toggleable(),

                ToggleColumn::make('pagado')
                    ->label('Pagar')
                    ->toggleable(),
            ])
            ->header(function () {
                $tableFilters = request()->get('tableFilters', []);

                $filtros = [];
                if (isset($tableFilters['tipo']['value'])) {
                    $filtros['tipo'] = $tableFilters['tipo']['value'];
                }
                if (isset($tableFilters['mes']['value'])) {
                    $filtros['mes'] = $tableFilters['mes']['value'];
                }
                if (isset($tableFilters['año']['value'])) {
                    $filtros['año'] = $tableFilters['año']['value'];
                }

                $data = self::getTotalesYHistorial($filtros);

                return view('filament.components.resumen-cuentas-por-pagar', [
                    'total_por_pagar' => $data['total_por_pagar'],
                    'historial'       => $data['historial'],
                ]);
            })
            ->defaultSort('created_at', 'desc')
            ->searchable()
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo de Gasto')
                    ->options([
                        'fecha' => 'Fecha',
                        'mes' => 'Mes',
                        'fin_mes' => 'Fin de Mes',
                        'viajes' => 'Viajes',
                    ]),

                SelectFilter::make('mes')
                    ->label('Mes')
                    ->options([
                        'enero'      => 'Enero',
                        'febrero'    => 'Febrero',
                        'marzo'      => 'Marzo',
                        'abril'      => 'Abril',
                        'mayo'       => 'Mayo',
                        'junio'      => 'Junio',
                        'julio'      => 'Julio',
                        'agosto'     => 'Agosto',
                        'septiembre' => 'Septiembre',
                        'octubre'    => 'Octubre',
                        'noviembre'  => 'Noviembre',
                        'diciembre'  => 'Diciembre',
                    ]),

                SelectFilter::make('año')
                    ->label('Año')
                    ->options(function () {
                        $currentYear = now()->year;
                        return collect(range($currentYear - 2, $currentYear + 3))
                            ->mapWithKeys(fn ($y) => [$y => $y])
                            ->toArray();
                    }),

                TernaryFilter::make('pagado')
                    ->label('Estado')
                    ->trueLabel('Pagado')
                    ->falseLabel('Debe'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped(); // Esto mantiene las filas sin efecto hover, solo con rayas alternadas
    }

    /**
     * Calcula el total por pagar y el historial de pagos
     */
    public static function getTotalesYHistorial(array $filtros = []): array
    {
        // ==================== TOTAL POR PAGAR ====================
        $queryPendientes = CuentaPorPagar::query()->where('pagado', false);

        if (!empty($filtros)) {
            if (isset($filtros['tipo'])) {
                $queryPendientes->where('tipo', $filtros['tipo']);
            }
            if (isset($filtros['mes'])) {
                $queryPendientes->where('mes', $filtros['mes']);
            }
            if (isset($filtros['año'])) {
                $queryPendientes->where('año', $filtros['año']);
            }
        }

        $total_por_pagar = $queryPendientes->sum('total') ?? 0;

        // ==================== HISTORIAL DE PAGOS REALIZADOS ====================
        $queryHistorial = CuentaPorPagar::query()
            ->where('pagado', true)
            ->orderBy('updated_at', 'desc')
            ->limit(15);

        // Aplicar los mismos filtros al historial (excepto el de pagado)
        if (!empty($filtros)) {
            if (isset($filtros['tipo'])) {
                $queryHistorial->where('tipo', $filtros['tipo']);
            }
            if (isset($filtros['mes'])) {
                $queryHistorial->where('mes', $filtros['mes']);
            }
            if (isset($filtros['año'])) {
                $queryHistorial->where('año', $filtros['año']);
            }
        }

        $historial = $queryHistorial->get();

        return [
            'total_por_pagar' => $total_por_pagar,
            'historial'       => $historial,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCuentaPorPagars::route('/'),
            'create' => Pages\CreateCuentaPorPagar::route('/create'),
            'edit'   => Pages\EditCuentaPorPagar::route('/{record}/edit'),
        ];
    }
}
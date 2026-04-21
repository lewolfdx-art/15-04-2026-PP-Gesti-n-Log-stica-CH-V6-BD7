<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IngresoResource\Pages;
use App\Models\Ingreso;
use App\Models\DatoOperacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class IngresoResource extends Resource
{
    protected static ?string $model = Ingreso::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Reportes Financieros';
    protected static ?string $navigationLabel = 'Control de Ingresos';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Información Principal')
                            ->schema([
                                Forms\Components\DatePicker::make('fecha')
                                ->label('FECHA A VACIAR')
                                ->default(now())
                                ->required(),

                                Forms\Components\Select::make('modo_pago')
                                    ->label('Modo pago')
                                    ->required()
                                    ->options([
                                        'CAN-CONTRATO' => 'CAN-CONTRATO',
                                        'ADEL-CONTRATO' => 'ADEL-CONTRATO',
                                        'IN-OTROS'     => 'IN-OTROS',
                                    ]),

                                Forms\Components\TextInput::make('numero_contrato')
                                    ->label('N° Contrato')
                                    ->prefix('C-')
                                    ->placeholder('S/C1857'),

                                Forms\Components\Select::make('asesor')
                                    ->label('Asesor')
                                    ->options(function () {
                                        return \App\Models\Trabajador::where('tipo_cargo', 'asesor_ventas')
                                            ->where('activo', true)
                                            ->orderBy('orden')
                                            ->pluck('nombre_completo', 'nombre_completo');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->createOptionForm([
                                        Forms\Components\Select::make('tipo_cargo')
                                            ->label('Cargo / Tipo')
                                            ->options([
                                                'asesor_ventas' => '💼 Asesor de Ventas',
                                            ])
                                            ->default('asesor_ventas')
                                            ->required()
                                            ->disabled(),

                                        Forms\Components\TextInput::make('nombre_completo')
                                            ->label('Nombre Completo del Asesor')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('dni')
                                            ->label('DNI')
                                            ->required()
                                            ->mask('99999999')
                                            ->placeholder('12345678')
                                            ->rule('digits:8')
                                            ->unique('trabajadores', 'dni'),

                                        Forms\Components\DatePicker::make('fecha_nacimiento')
                                            ->label('Fecha de Nacimiento')
                                            ->default(now()->subYears(25))
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d')
                                            ->maxDate(now()),

                                        Forms\Components\TextInput::make('orden')
                                            ->label('Orden (posición en la lista)')
                                            ->numeric()
                                            ->default(999),

                                        Forms\Components\Textarea::make('descripcion')
                                            ->label('Descripción / Nota')
                                            ->rows(2),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        $nuevoTrabajador = \App\Models\Trabajador::create([
                                            'tipo_cargo'       => 'asesor_ventas',
                                            'nombre_completo'  => strtoupper(trim($data['nombre_completo'])),
                                            'dni'              => $data['dni'] ?? null,
                                            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
                                            'descripcion'      => $data['descripcion'] ?? null,
                                            'orden'            => $data['orden'] ?? 999,
                                            'activo'           => true,
                                        ]);

                                        return $nuevoTrabajador->nombre_completo;
                                    })
                                    ->createOptionAction(fn ($action) => $action->label('+ Crear nuevo asesor')),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Detalle y Monto')
                            ->schema([
                                Forms\Components\Textarea::make('detalle')
                                    ->label('Detalle')
                                    ->required()
                                    ->rows(5)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('monto')
                                    ->label('Monto')
                                    ->required()
                                    ->numeric()
                                    ->prefix('S/')
                                    ->rule('min:0'),

                                Forms\Components\Select::make('banco')
                                    ->label('Banco')
                                    ->options([
                                        'EFECTIVO' => 'EFECTIVO',
                                        'CTA.CTE'  => 'CTA.CTE',
                                    ])
                                    ->nullable()
                                    ->placeholder('Seleccione...'),

                                Forms\Components\Select::make('obs')
                                    ->label('OBS.')
                                    ->options([
                                        'EFECTIVO' => 'EFECTIVO',
                                        'CTA.CTE'  => 'CTA.CTE',
                                    ])
                                    ->nullable()
                                    ->placeholder('OBS.'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('modo_pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CAN-CONTRATO'  => 'success',
                        'ADEL-CONTRATO' => 'warning',
                        'IN-OTROS'      => 'info',
                    })
                    ->toggleable(),

                TextColumn::make('numero_contrato')
                    ->label('N° Contrato')
                    ->toggleable(),

                TextColumn::make('asesor')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('detalle')
                    ->wrap()
                    ->limit(60)
                    ->toggleable(),

                TextColumn::make('monto')
                    ->money('PEN')
                    ->alignEnd()
                    ->toggleable()
                    ->summarize(Sum::make()->label('TOTAL')),

                TextColumn::make('banco')
                    ->toggleable(),

                TextColumn::make('obs')
                    ->label('OBS.')
                    ->toggleable(),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde')->label('Desde'),
                        Forms\Components\DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn($q) => $q->whereDate('fecha', '>=', $data['desde']))
                            ->when($data['hasta'], fn($q) => $q->whereDate('fecha', '<=', $data['hasta']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) $indicators[] = 'Desde: ' . $data['desde'];
                        if ($data['hasta'] ?? null) $indicators[] = 'Hasta: ' . $data['hasta'];
                        return $indicators;
                    }),

                SelectFilter::make('modo_pago')
                    ->label('Modo de Pago')
                    ->options([
                        'CAN-CONTRATO'  => 'CAN-CONTRATO',
                        'ADEL-CONTRATO' => 'ADEL-CONTRATO',
                        'IN-OTROS'      => 'IN-OTROS',
                    ])
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('asesor')
                    ->label('Asesor')
                    ->options(fn () => \App\Models\Trabajador::where('tipo_cargo', 'asesor_ventas')
                        ->where('activo', true)
                        ->orderBy('orden')
                        ->pluck('nombre_completo', 'nombre_completo')
                    )
                    ->multiple()
                    ->searchable(),

                SelectFilter::make('banco')
                    ->label('Banco')
                    ->options([
                        'EFECTIVO' => 'EFECTIVO',
                        'CTA.CTE'  => 'CTA.CTE',
                    ])
                    ->multiple(),

                SelectFilter::make('obs')
                    ->label('OBS.')
                    ->options([
                        'EFECTIVO' => 'EFECTIVO',
                        'CTA.CTE'  => 'CTA.CTE',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('tiene_contrato')
                    ->label('Con N° Contrato')
                    ->nullable()
                    ->placeholder('Todos')
                    ->trueLabel('Con contrato')
                    ->falseLabel('Sin contrato')
                    ->queries(
                        true:  fn ($query) => $query->whereNotNull('numero_contrato')->where('numero_contrato', '!=', ''),
                        false: fn ($query) => $query->where(fn($q) => $q->whereNull('numero_contrato')->orWhere('numero_contrato', '')),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->filtersFormColumns(1)
            ->filtersFormMaxHeight('400px')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListIngresos::route('/'),
            'create' => Pages\CreateIngreso::route('/create'),
            'edit'   => Pages\EditIngreso::route('/{record}/edit'),
        ];
    }
}
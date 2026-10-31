<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Models\Trabajador;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\IngresoResource\Pages\ListIngresos;
use App\Filament\Resources\IngresoResource\Pages\CreateIngreso;
use App\Filament\Resources\IngresoResource\Pages\EditIngreso;
use App\Filament\Resources\IngresoResource\Pages;
use App\Models\Ingreso;
use App\Models\DatoOperacion;
use Filament\Forms;
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

    protected static string | \UnitEnum | null $navigationGroup = 'Finanzas';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationLabel = 'Ingresos';
    protected static ?string $modelLabel = 'Ingreso';
    protected static ?string $pluralModelLabel = 'Ingresos';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'ingresos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->tabs([
                        Tab::make('Información Principal')
                            ->schema([
                                DatePicker::make('fecha')
                                ->label('FECHA A VACIAR')
                                ->default(now())
                                ->required(),

                                Select::make('modo_pago')
                                    ->label('Modo pago')
                                    ->required()
                                    ->options([
                                        'CAN-CONTRATO' => 'CAN-CONTRATO',
                                        'ADEL-CONTRATO' => 'ADEL-CONTRATO',
                                        'IN-OTROS'     => 'IN-OTROS',
                                    ]),

                                TextInput::make('numero_contrato')
                                    ->label('N° Contrato')
                                    ->prefix('C-')
                                    ->placeholder('S/C1857'),

                                Select::make('asesor')
                                    ->label('Asesor')
                                    ->options(function () {
                                        return Trabajador::asesores()
                                            ->where('activo', true)
                                            ->orderBy('orden')
                                            ->pluck('nombre_completo', 'nombre_completo');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->createOptionForm([
                                        Select::make('tipo_cargo')
                                            ->label('Cargo / Tipo')
                                            ->options([
                                                'asesor_ventas' => '💼 Asesor de Ventas',
                                            ])
                                            ->default('asesor_ventas')
                                            ->required()
                                            ->disabled(),

                                        TextInput::make('nombre_completo')
                                            ->label('Nombre Completo del Asesor')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('dni')
                                            ->label('DNI')
                                            ->required()
                                            ->mask('99999999')
                                            ->placeholder('12345678')
                                            ->rule('digits:8')
                                            ->unique('trabajadores', 'dni'),

                                        // ======= SWITCH PARA FECHA DE NACIMIENTO OPCIONAL =======
                                        Toggle::make('tiene_fecha_nacimiento')
                                            ->label('¿Tiene fecha de nacimiento?')
                                            ->default(true)
                                            ->live()
                                            ->columnSpanFull(),

                                        DatePicker::make('fecha_nacimiento')
                                            ->label('Fecha de Nacimiento')
                                            ->default(now()->subYears(25))
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->format('Y-m-d')
                                            ->maxDate(now())
                                            ->closeOnDateSelection(true)
                                            ->placeholder('dd/mm/yyyy')
                                            ->required(fn (Get $get) => $get('tiene_fecha_nacimiento') === true)
                                            ->disabled(fn (Get $get) => $get('tiene_fecha_nacimiento') === false)
                                            ->dehydrated(fn (Get $get) => $get('tiene_fecha_nacimiento') === true)
                                            ->rules(fn (Get $get) => $get('tiene_fecha_nacimiento') === true ? ['required', 'date'] : ['nullable', 'date'])
                                            ->afterStateHydrated(function ($state, callable $set) {
                                                if (empty($state)) {
                                                    $set('tiene_fecha_nacimiento', false);
                                                }
                                            }),

                                        TextInput::make('orden')
                                            ->label('Orden (posición en la lista)')
                                            ->numeric()
                                            ->default(999),

                                        Textarea::make('descripcion')
                                            ->label('Descripción / Nota')
                                            ->rows(2),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        $nuevoTrabajador = Trabajador::create([
                                            'tipo_cargo'       => 'asesor_ventas',
                                            'nombre_completo'  => strtoupper(trim($data['nombre_completo'])),
                                            'dni'              => $data['dni'] ?? null,
                                            'fecha_nacimiento' => $data['tiene_fecha_nacimiento'] ? ($data['fecha_nacimiento'] ?? null) : null,
                                            'descripcion'      => $data['descripcion'] ?? null,
                                            'orden'            => $data['orden'] ?? 999,
                                            'activo'           => true,
                                        ]);

                                        return $nuevoTrabajador->nombre_completo;
                                    })
                                    ->createOptionAction(fn ($action) => $action->label('+ Crear nuevo asesor')),
                            ])
                            ->columns(2),

                        Tab::make('Detalle y Monto')
                            ->schema([
                                Textarea::make('detalle')
                                    ->label('Detalle')
                                    ->required()
                                    ->rows(5)
                                    ->columnSpanFull(),

                                TextInput::make('monto')
                                    ->label('Monto')
                                    ->required()
                                    ->numeric()
                                    ->prefix('S/')
                                    ->rule('min:0'),

                                Select::make('banco')
                                    ->label('Banco')
                                    ->options([
                                        'EFECTIVO' => 'EFECTIVO',
                                        'CTA.CTE'  => 'CTA.CTE',
                                    ])
                                    ->nullable()
                                    ->placeholder('Seleccione...'),

                                Select::make('obs')
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
                    ->schema([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
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
                    ->options(fn () => Trabajador::asesores()
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

                TernaryFilter::make('tiene_contrato')
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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListIngresos::route('/'),
            'create' => CreateIngreso::route('/create'),
            'edit'   => EditIngreso::route('/{record}/edit'),
        ];
    }
}
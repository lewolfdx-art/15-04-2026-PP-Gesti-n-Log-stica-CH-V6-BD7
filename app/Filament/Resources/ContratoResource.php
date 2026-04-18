<?php

namespace App\Filament\Resources;

use App\Models\Contrato;
use Filament\Forms;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;

class ContratoResource extends Resource
{
    protected static ?string $model = Contrato::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Contratos / Ventas';
    protected static ?string $pluralLabel = 'Contratos';
    protected static ?string $modelLabel = 'Contrato';

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

                                Forms\Components\Textarea::make('ubicacion_referencia')
                                    ->label('Ubicaciones / Referencia')
                                    ->rows(2),

                                Forms\Components\Select::make('estructura')
                                    ->label('ESTRUCTURA')
                                    ->options([
                                        'CANAL' => 'CANAL', 'CIMIENTO' => 'CIMIENTO', 'COLUMNAS' => 'COLUMNAS',
                                        'FALSO PISO' => 'FALSO PISO', 'LOSA ALIGERADA' => 'LOSA ALIGERADA',
                                        'PAVIMENTO' => 'PAVIMENTO', 'PISO' => 'PISO',
                                        'PLACAS Y COLUMNAS' => 'PLACAS Y COLUMNAS', 'PLATEA' => 'PLATEA',
                                        'VEREDAS' => 'VEREDAS', 'VIGAS DE CIMENTACION' => 'VIGAS DE CIMENTACION',
                                        'ZAPATAS' => 'ZAPATAS', 'ZAPATAS C/CORTE' => 'ZAPATAS C/CORTE',
                                    ])
                                    ->required(),

                                Forms\Components\Textarea::make('nombre_cliente')
                                    ->label('NOMBRE DEL CLIENTE')
                                    ->rows(2),

                                Forms\Components\TextInput::make('nombre_vendedor')
                                    ->label('NOMBRE DEL VENDEDOR')
                                    ->required(),

                                Forms\Components\TextInput::make('numero_contrato')
                                    ->label('N° DE CONTRATO')
                                    ->prefix('C-')
                                    ->numeric()
                                    ->required()
                                    ->placeholder('296')
                                    ->maxLength(10)
                                    ->default('')
                                    ->dehydrateStateUsing(fn ($state) => $state ? 'C-' . $state : null),

                                Forms\Components\Select::make('tipo_concreto')
                                    ->label('TIPO DE CONCRETO')
                                    ->options(['145'=>'145','175'=>'175','210'=>'210','245'=>'245','280'=>'280'])
                                    ->required(),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Volúmenes y Bombeo')
                            ->schema([
                                Forms\Components\Select::make('bombeo')
                                    ->label('BOMBEO')
                                    ->options(['C/BOMBA' => 'C/BOMBA', 'S/BOMBA' => 'S/BOMBA', 'ESTACIONARIA' => 'ESTACIONARIA']),

                                Forms\Components\TextInput::make('bomba')
                                    ->label('BOMBA')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('bomba_adicional')
                                    ->label('BOMBA ADICIONAL')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('volumen_guia')
                                    ->label('GUIAS PARA EMPRESA')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('volumen_real')
                                    ->label('REAL')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('volumen_sobrante')
                                    ->label('SOBRANTE')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),
                            ])
                            ->columns(3),

                        Forms\Components\Tabs\Tab::make('Precios y Cálculos')
                            ->schema([
                                Forms\Components\TextInput::make('pu')
                                    ->label('P.U.')
                                    ->numeric()
                                    ->step(0.01)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('descuento_guias')
                                    ->label('DESC.GUIAS')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Toggle::make('aplicar_comision')
                                    ->label('Aplicar Comisión')
                                    ->default(true)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('comision')
                                    ->label('COMISION')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->disabled(fn (Get $get) => !$get('aplicar_comision')),

                                Forms\Components\TextInput::make('comision_igv')
                                    ->label('COMISION DE IGV')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('gastos_alimentos_bomba')
                                    ->label('BOMBA/ALIMENTOS')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('descuentos')
                                    ->label('DESCUENTOS')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('monto_total')
                                    ->label('MONTO TOTAL')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(true),

                                Forms\Components\TextInput::make('venta_neta')
                                    ->label('VENTA NETA')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(true),

                                Forms\Components\TextInput::make('pu_real')
                                    ->label('P. U. REAL')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(true),

                                Forms\Components\TextInput::make('total_para_empresa')
                                    ->label('TOTAL PARA LA EMPRESA')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(true),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Pago y Observaciones')
                            ->schema([
                                Forms\Components\Select::make('estado_pago')
                                    ->label('ESTADO')
                                    ->options(['PAGADO' => 'PAGADO', 'DEBE' => 'DEBE'])
                                    ->default('PAGADO')
                                    ->required(),

                                Forms\Components\Select::make('forma_pago')
                                    ->label('Forma de Pago')
                                    ->options([
                                        'EFECTIVO' => 'EFECTIVO',
                                        'DEPOSITO' => 'DEPOSITO',
                                        'DEBE' => 'DEBE',
                                        'PAGADO EN VALORIZACION' => 'PAGADO EN VALORIZACION',
                                        'SE PASO PARA CEMENTO' => 'SE PASO PARA CEMENTO',
                                        'SE PASO VALORIZACION' => 'SE PASO VALORIZACION',
                                    ]),

                                Forms\Components\Textarea::make('observacion')
                                    ->label('OBSERVACION')
                                    ->rows(3),

                                Forms\Components\Textarea::make('observaciones_adicionales')
                                    ->label('OBSERVACIONES')
                                    ->rows(4)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    private static function calcularTodo(Get $get, Set $set): void
    {
        $pu               = (float) ($get('pu') ?? 0);
        $volGuia          = (float) ($get('volumen_guia') ?? 0);
        $volReal          = (float) ($get('volumen_real') ?? 0);
        $bomba            = (float) ($get('bomba') ?? 0);
        $bombaAdicional   = (float) ($get('bomba_adicional') ?? 0);
        $descGuias        = (float) ($get('descuento_guias') ?? 0);
        $descuentos       = (float) ($get('descuentos') ?? 0);
        $gastosAlimentos  = (float) ($get('gastos_alimentos_bomba') ?? 0);
        $comisionIgv      = (float) ($get('comision_igv') ?? 0);
        $aplicarComision  = $get('aplicar_comision') ?? true;

        $montoTotal = ($volGuia * $pu) + $bombaAdicional + $bomba - $descGuias;
        $set('monto_total', round($montoTotal, 2));

        $comision = $aplicarComision ? ($volGuia - $volReal) * $pu : 0;
        $set('comision', round($comision, 2));

        $ventaNeta = $montoTotal - $comision - $descuentos - $gastosAlimentos - $comisionIgv;
        $set('venta_neta', round($ventaNeta, 2));

        $puReal = $volReal > 0 ? ($ventaNeta - $bomba) / $volReal : 0;
        $set('pu_real', round($puReal, 2));

        $totalEmpresa = $volGuia * $pu;
        $set('total_para_empresa', round($totalEmpresa, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('numero_contrato')
                    ->label('N° Contrato')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nombre_cliente')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('nombre_vendedor')
                    ->label('Vendedor')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('estructura')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tipo_concreto')
                    ->label('Tipo Concreto')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('bombeo')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('volumen_guia')
                    ->label('Vol. Guía')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('volumen_real')
                    ->label('Vol. Real')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('monto_total')
                    ->label('Monto Total')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('comision')
                    ->label('Comisión')
                    ->money('PEN')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state > 0 ? number_format($state, 2) : '-')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('venta_neta')
                    ->label('Venta Neta')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('pu_real')
                    ->label('P.U. Real')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\BadgeColumn::make('estado_pago')
                    ->label('Estado Pago')
                    ->colors([
                        'success' => 'PAGADO',
                        'danger'  => 'DEBE',
                    ])
                    ->toggleable(),

                Tables\Columns\TextColumn::make('observacion')
                    ->label('Observación')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('observaciones_adicionales')
                    ->label('Obs. Adicionales')
                    ->searchable()
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('fecha', 'desc')

            // ==================== BÚSQUEDA GLOBAL ====================
            ->searchable()   // Activa la barra de búsqueda global

            // ==================== FILTROS ====================
            ->filters([
                Tables\Filters\SelectFilter::make('estructura')
                    ->label('Estructura')
                    ->options([
                        'CANAL' => 'CANAL', 'CIMIENTO' => 'CIMIENTO', 'COLUMNAS' => 'COLUMNAS',
                        'FALSO PISO' => 'FALSO PISO', 'LOSA ALIGERADA' => 'LOSA ALIGERADA',
                        'PAVIMENTO' => 'PAVIMENTO', 'PISO' => 'PISO',
                        'PLACAS Y COLUMNAS' => 'PLACAS Y COLUMNAS', 'PLATEA' => 'PLATEA',
                        'VEREDAS' => 'VEREDAS', 'VIGAS DE CIMENTACION' => 'VIGAS DE CIMENTACION',
                        'ZAPATAS' => 'ZAPATAS', 'ZAPATAS C/CORTE' => 'ZAPATAS C/CORTE',
                    ]),

                Tables\Filters\SelectFilter::make('tipo_concreto')
                    ->label('Tipo de Concreto')
                    ->options(['145'=>'145','175'=>'175','210'=>'210','245'=>'245','280'=>'280']),

                Tables\Filters\SelectFilter::make('estado_pago')
                    ->label('Estado de Pago')
                    ->options(['PAGADO' => 'PAGADO', 'DEBE' => 'DEBE']),

                Tables\Filters\SelectFilter::make('bombeo')
                    ->label('Bombeo')
                    ->options(['C/BOMBA' => 'C/BOMBA', 'S/BOMBA' => 'S/BOMBA', 'ESTACIONARIA' => 'ESTACIONARIA']),

                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_desde')->label('Desde'),
                        Forms\Components\DatePicker::make('fecha_hasta')->label('Hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['fecha_desde'], fn ($q) => $q->whereDate('fecha', '>=', $data['fecha_desde']))
                            ->when($data['fecha_hasta'], fn ($q) => $q->whereDate('fecha', '<=', $data['fecha_hasta']));
                    }),
            ])

            ->actions([
                ViewAction::make()->label('Ver'),
                EditAction::make()->label('Editar'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\ContratoResource\Pages\ListContratos::route('/'),
            'create' => \App\Filament\Resources\ContratoResource\Pages\CreateContrato::route('/create'),
            'edit'   => \App\Filament\Resources\ContratoResource\Pages\EditContrato::route('/{record}/edit'),
            'view'   => \App\Filament\Resources\ContratoResource\Pages\ViewContrato::route('/{record}'),
        ];
    }
}
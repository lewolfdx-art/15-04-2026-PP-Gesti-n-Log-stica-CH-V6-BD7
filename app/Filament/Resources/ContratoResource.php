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
                                    ->required(),

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
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('bomba_adicional')
                                    ->label('BOMBA ADICIONAL')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('volumen_guia')
                                    ->label('GUIAS PARA EMPRESA')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('volumen_real')
                                    ->label('REAL')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('volumen_sobrante')
                                    ->label('SOBRANTE')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0),
                            ])
                            ->columns(3),

                        Forms\Components\Tabs\Tab::make('Precios y Cálculos')
                            ->schema([
                                Forms\Components\TextInput::make('pu')
                                    ->label('P.U.')
                                    ->numeric()
                                    ->step(0.01)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('descuento_guias')
                                    ->label('DESC.GUIAS')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Toggle::make('aplicar_comision')
                                    ->label('Aplicar Comisión')
                                    ->default(true)
                                    ->live()
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
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('gastos_alimentos_bomba')
                                    ->label('BOMBA/ALIMENTOS')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                Forms\Components\TextInput::make('descuentos')
                                    ->label('DESCUENTOS')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTodo($get, $set)),

                                // Campos calculados (solo muestran resultado)
                                Forms\Components\TextInput::make('monto_total')
                                    ->label('MONTO TOTAL')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\TextInput::make('venta_neta')
                                    ->label('VENTA NETA')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\TextInput::make('pu_real')
                                    ->label('P. U. REAL')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),

                                Forms\Components\TextInput::make('total_para_empresa')
                                    ->label('TOTAL PARA LA EMPRESA')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),
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

    // ====================== FUNCIÓN DE CÁLCULOS ======================
    private static function calcularTodo(Get $get, Set $set): void
    {
        $pu               = (float) $get('pu') ?? 0;
        $volGuia          = (float) $get('volumen_guia') ?? 0;
        $volReal          = (float) $get('volumen_real') ?? 0;
        $bomba            = (float) $get('bomba') ?? 0;
        $bombaAdicional   = (float) $get('bomba_adicional') ?? 0;
        $descGuias        = (float) $get('descuento_guias') ?? 0;
        $descuentos       = (float) $get('descuentos') ?? 0;
        $gastosAlimentos  = (float) $get('gastos_alimentos_bomba') ?? 0;
        $comisionIgv      = (float) $get('comision_igv') ?? 0;
        $aplicarComision  = $get('aplicar_comision') ?? false;

        // 1. Monto Total = (guias * pu) + bomba_adicional + bomba - desc_guias
        $montoTotal = ($volGuia * $pu) + $bombaAdicional + $bomba - $descGuias;
        $set('monto_total', round($montoTotal, 2));

        // 2. Comisión = (guias - real) * pu   (si el switch está activado)
        $comision = $aplicarComision ? ($volGuia - $volReal) * $pu : 0;
        $set('comision', round($comision, 2));

        // 3. Venta Neta = monto_total - comision - descuentos - bomba/alimentos - comision_igv
        $ventaNeta = $montoTotal - $comision - $descuentos - $gastosAlimentos - $comisionIgv;
        $set('venta_neta', round($ventaNeta, 2));

        // 4. P.U. Real = (venta_neta - bomba) / real
        $puReal = $volReal > 0 ? ($ventaNeta - $bomba) / $volReal : 0;
        $set('pu_real', round($puReal, 2));

        // 5. Total para la empresa = guias * pu   (usando volumen_guia como "para empresa")
        $totalEmpresa = $volGuia * $pu;
        $set('total_para_empresa', round($totalEmpresa, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('estructura'),
                Tables\Columns\TextColumn::make('nombre_vendedor'),
                Tables\Columns\TextColumn::make('numero_contrato'),
                Tables\Columns\TextColumn::make('venta_neta')->money('PEN'),
                Tables\Columns\BadgeColumn::make('estado_pago')
                    ->colors(['success' => 'PAGADO', 'danger' => 'DEBE']),
            ])
            ->defaultSort('fecha', 'desc')
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\ContratoResource\Pages\ListContratos::route('/'),
            'create' => \App\Filament\Resources\ContratoResource\Pages\CreateContrato::route('/create'),
            'edit'   => \App\Filament\Resources\ContratoResource\Pages\EditContrato::route('/{record}/edit'),
        ];
    }
}
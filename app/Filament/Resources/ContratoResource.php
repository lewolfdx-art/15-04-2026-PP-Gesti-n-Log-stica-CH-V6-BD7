<?php

namespace App\Filament\Resources;

use App\Models\Contrato;
use App\Models\DatoOperacion;
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
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

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

                                // ==================== VENDEDOR CON CREACIÓN EN TIEMPO REAL ====================
                                Forms\Components\Select::make('nombre_vendedor')
                                ->label('NOMBRE DEL VENDEDOR')
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
                                        ->disabled(), // Para que siempre se cree como asesor
                            
                                    Forms\Components\TextInput::make('nombre_completo')
                                        ->label('Nombre Completo del Asesor')
                                        ->required()
                                        ->maxLength(255),
                            
                                    Forms\Components\TextInput::make('dni')
                                        ->label('DNI')
                                        ->required()
                                        ->mask('99999999')
                                        ->rule('digits:8')
                                        ->unique('trabajadores', 'dni'),
                            
                                    Forms\Components\DatePicker::make('fecha_nacimiento')
                                        ->label('Fecha de Nacimiento')
                                        ->default(now()->subYears(25))
                                        ->required(),
                            
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
                                // ====================================================================

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

                Tables\Columns\TextColumn::make('monto_total')
                    ->label('Monto Total')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('venta_neta')
                    ->label('Venta Neta')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('estado_pago')
                    ->label('Estado Pago')
                    ->colors([
                        'success' => 'PAGADO',
                        'danger'  => 'DEBE',
                    ])
                    ->toggleable(),
            ])
            ->defaultSort('fecha', 'desc')
            ->searchable()
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
                Tables\Actions\ViewAction::make()->label('Ver'),
                Tables\Actions\EditAction::make()->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Eliminar seleccionados'),
            
                    // Exportación con selección de columnas
                    Tables\Actions\BulkAction::make('export_excel')
                        ->label('Exportar a Excel')
                        ->color('success')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->form([
                            Forms\Components\CheckboxList::make('columns')
                                ->label('Selecciona las columnas que deseas exportar')
                                ->options([
                                    'fecha'                     => 'Fecha',
                                    'numero_contrato'           => 'N° Contrato',
                                    'nombre_cliente'            => 'Cliente',
                                    'nombre_vendedor'           => 'Vendedor',
                                    'estructura'                => 'Estructura',
                                    'tipo_concreto'             => 'Tipo Concreto',
                                    'ubicacion_referencia'      => 'Ubicación / Referencia',
                                    'bombeo'                    => 'Bombeo',
                                    'volumen_guia'              => 'Vol. Guía (m³)',
                                    'volumen_real'              => 'Vol. Real (m³)',
                                    'volumen_sobrante'          => 'Vol. Sobrante (m³)',
                                    'bomba'                     => 'Bomba (S/)',
                                    'bomba_adicional'           => 'Bomba Adicional (S/)',
                                    'pu'                        => 'P.U. (S/)',
                                    'pu_real'                   => 'P.U. Real (S/)',
                                    'monto_total'               => 'Monto Total (S/)',
                                    'venta_neta'                => 'Venta Neta (S/)',
                                    'total_para_empresa'        => 'Total Empresa (S/)',
                                    'descuento_guias'           => 'Desc. Guías (S/)',
                                    'descuentos'                => 'Descuentos (S/)',
                                    'gastos_alimentos_bomba'    => 'Gastos Bomba (S/)',
                                    'comision'                  => 'Comisión (S/)',
                                    'comision_igv'              => 'Com. IGV (S/)',
                                    'estado_pago'               => 'Estado Pago',
                                    'forma_pago'                => 'Forma Pago',
                                    'observacion'               => 'Observación',
                                    'observaciones_adicionales' => 'Observaciones Adicionales',
                                    'created_at'                => 'Fecha Registro',
                                ])
                                ->default([
                                    'fecha', 'numero_contrato', 'nombre_cliente', 'nombre_vendedor',
                                    'estructura', 'tipo_concreto', 'bombeo', 'monto_total', 
                                    'venta_neta', 'estado_pago'
                                ])
                                ->columns(2)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            
                            if ($records->isEmpty()) {
                                Notification::make()
                                    ->warning()
                                    ->title('Sin registros')
                                    ->body('Por favor selecciona al menos un contrato para exportar.')
                                    ->send();
                                return;
                            }
            
                            $filename = 'contratos_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            
                            Notification::make()
                                ->success()
                                ->title('Exportación completada ✅')
                                ->body('Se han exportado ' . $records->count() . ' registros. El archivo se está descargando...')
                                ->send();
            
                            return Excel::download(
                                new \App\Exports\ContratoExport($records, $data['columns'] ?? []), 
                                $filename
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            
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
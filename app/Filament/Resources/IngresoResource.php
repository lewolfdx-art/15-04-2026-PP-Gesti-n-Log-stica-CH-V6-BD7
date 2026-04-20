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

                                // Asesor con creación en tiempo real
                                Forms\Components\Select::make('asesor')
                                    ->label('Asesor')
                                    ->options(function () {
                                        return DatoOperacion::where('tipo', 'asesor')
                                            ->where('activo', true)
                                            ->orderBy('orden')
                                            ->pluck('valor', 'valor');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('valor')
                                            ->label('Nombre Completo del Asesor')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('orden')
                                            ->label('Orden')
                                            ->numeric()
                                            ->default(999),

                                        Forms\Components\Textarea::make('descripcion')
                                            ->label('Descripción / Nota')
                                            ->rows(2),
                                    ])
                                    ->createOptionUsing(function (array $data) {
                                        $nuevo = DatoOperacion::create([
                                            'tipo'        => 'asesor',
                                            'valor'       => strtoupper(trim($data['valor'])),
                                            'descripcion' => $data['descripcion'] ?? null,
                                            'orden'       => $data['orden'] ?? 999,
                                            'activo'      => true,
                                        ]);
                                        return $nuevo->valor;
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
                    ->sortable(),

                TextColumn::make('modo_pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'CAN-CONTRATO' => 'success',
                        'ADEL-CONTRATO' => 'warning',
                        'IN-OTROS' => 'info',
                    }),

                TextColumn::make('numero_contrato')
                    ->label('N° Contrato'),

                TextColumn::make('asesor')
                    ->searchable(),

                TextColumn::make('detalle')
                    ->wrap()
                    ->limit(60),

                TextColumn::make('monto')
                    ->money('PEN')
                    ->alignEnd()
                    ->summarize(Sum::make()->label('TOTAL')),

                TextColumn::make('banco'),
                TextColumn::make('obs')->label('OBS.'),
            ])
            ->defaultSort('fecha', 'desc')
            ->filters([
                Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde'),
                        Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn($q) => $q->where('fecha', '>=', $data['desde']))
                            ->when($data['hasta'], fn($q) => $q->where('fecha', '<=', $data['hasta']));
                    }),

                SelectFilter::make('modo_pago'),
                SelectFilter::make('asesor'),
                SelectFilter::make('banco'),
                SelectFilter::make('obs'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIngresos::route('/'),
            'create' => Pages\CreateIngreso::route('/create'),
            'edit' => Pages\EditIngreso::route('/{record}/edit'),
        ];
    }
}
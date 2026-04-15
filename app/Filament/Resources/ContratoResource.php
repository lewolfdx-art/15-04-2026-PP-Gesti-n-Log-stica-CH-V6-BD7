<?php

namespace App\Filament\Resources;

use App\Models\Contrato;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContratoResource extends Resource
{
    protected static ?string $model = Contrato::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Contratos';
    protected static ?string $pluralLabel = 'Contratos';
    protected static ?string $modelLabel = 'Contrato';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Datos Generales')
                            ->schema([
                                Forms\Components\DatePicker::make('fecha')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\TextInput::make('numero_contrato')
                                    ->label('N° de Contrato')
                                    ->required()
                                    ->maxLength(50),

                                Forms\Components\TextInput::make('nombre_vendedor')
                                    ->label('Vendedor')
                                    ->required()
                                    ->maxLength(100),

                                Forms\Components\TextInput::make('estructura')
                                    ->label('Estructura')
                                    ->required()
                                    ->maxLength(100),

                                Forms\Components\TextInput::make('tipo_concreto')
                                    ->label('Tipo de Concreto')
                                    ->required()
                                    ->numeric(),

                                Forms\Components\Textarea::make('nombre_cliente')
                                    ->label('Cliente')
                                    ->rows(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Volúmenes')
                            ->schema([
                                Forms\Components\TextInput::make('volumen_guia')
                                    ->label('Vol. Guía (m³)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0),

                                Forms\Components\TextInput::make('volumen_real')
                                    ->label('Vol. Real (m³)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->default(0),

                                Forms\Components\TextInput::make('bombeo')
                                    ->label('Bombeo'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Precios')
                            ->schema([
                                Forms\Components\TextInput::make('pu')
                                    ->label('P.U.')
                                    ->numeric()
                                    ->step(0.01)
                                    ->required(),

                                Forms\Components\TextInput::make('monto_total')
                                    ->label('Monto Total')
                                    ->numeric()
                                    ->step(0.01)
                                    ->required(),

                                Forms\Components\TextInput::make('venta_neta')
                                    ->label('Venta Neta')
                                    ->numeric()
                                    ->step(0.01)
                                    ->required(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Pago y Observaciones')
                            ->schema([
                                Forms\Components\Select::make('estado_pago')
                                    ->options([
                                        'PAGADO' => 'PAGADO',
                                        'DEBE'   => 'DEBE',
                                    ])
                                    ->default('PAGADO')
                                    ->required(),

                                Forms\Components\TextInput::make('forma_pago')
                                    ->label('Forma de Pago'),

                                Forms\Components\Textarea::make('observacion')
                                    ->label('Observación')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('numero_contrato')
                    ->label('Contrato')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre_vendedor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estructura')
                    ->searchable(),
                Tables\Columns\TextColumn::make('volumen_real')
                    ->label('Vol. Real'),
                Tables\Columns\TextColumn::make('venta_neta')
                    ->money('PEN')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('estado_pago')
                    ->colors([
                        'success' => 'PAGADO',
                        'danger'  => 'DEBE',
                    ]),
            ])
            ->defaultSort('fecha', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
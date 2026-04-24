<?php

namespace App\Filament\Resources;

use App\Models\Gasto;
use App\Models\GastoCategoria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;

    protected static ?string $navigationIcon = 'heroicon-o-minus-circle';
    protected static ?string $navigationLabel = 'Gastos';
    protected static ?string $pluralLabel = 'Gastos';
    protected static ?string $modelLabel = 'Gasto';
    protected static ?string $navigationGroup = 'Finanzas';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make()
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Información Principal')
                            ->schema([
                                Forms\Components\DatePicker::make('fecha')
                                    ->label('FECHA')
                                    ->default(now())
                                    ->required()
                                    ->displayFormat('d/m/Y'),

                                // CATEGORÍA = grupo
                                Forms\Components\Select::make('grupo')
                                    ->label('CATEGORÍA')
                                    ->options(GastoCategoria::distinct()->pluck('grupo', 'grupo'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->placeholder('Seleccione una categoría'),

                                // SUBCATEGORÍA = nombre (dependiente del grupo)
                                Forms\Components\Select::make('subcategoria')
                                    ->label('SUBCATEGORÍA')
                                    ->options(function (Get $get) {
                                        $grupo = $get('grupo');
                                        if (!$grupo) {
                                            return [];
                                        }
                                        return GastoCategoria::where('grupo', $grupo)
                                            ->pluck('nombre', 'nombre');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->placeholder('Seleccione primero una categoría'),

                                Forms\Components\TextInput::make('responsable')
                                    ->label('RESPONSABLE')
                                    ->maxLength(255)
                                    ->placeholder('Ej: MIGUEL ALBERTO, GYNA, JACSON'),
                            ])
                            ->columns(2),

                        Forms\Components\Tabs\Tab::make('Detalle del Gasto')
                            ->schema([
                                Forms\Components\Textarea::make('detalle')
                                    ->label('DETALLE')
                                    ->rows(3)
                                    ->required()
                                    ->placeholder('Descripción detallada del gasto...'),

                                Forms\Components\TextInput::make('importe')
                                    ->label('IMPORTE (S/)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->required()
                                    ->prefix('S/')
                                    ->default(0),

                                Forms\Components\Select::make('obs')
                                    ->label('OBS. / FORMA DE PAGO')
                                    ->options([
                                        'EFECTIVO' => 'EFECTIVO',
                                        'CTA.CTE'  => 'CTA.CTE',
                                    ])
                                    ->nullable()
                                    ->placeholder('Seleccione forma de pago'),
                            ])
                            ->columns(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('fecha')
                    ->label('FECHA')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('grupo')
                    ->label('CATEGORÍA')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('subcategoria')
                    ->label('SUBCATEGORÍA')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('responsable')
                    ->label('RESPONSABLE')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('detalle')
                    ->label('DETALLE')
                    ->limit(60)
                    ->tooltip(fn (Gasto $record) => $record->detalle)
                    ->searchable(),

                Tables\Columns\TextColumn::make('importe')
                    ->label('IMPORTE')
                    ->money('PEN')
                    ->sortable()
                    ->alignRight()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('obs')
                    ->label('OBS.')
                    ->searchable()
                    ->toggleable(),
            ])
            ->defaultSort('fecha', 'desc')
            ->searchable()
            ->filters([
                Tables\Filters\SelectFilter::make('grupo')
                    ->label('Categoría')
                    ->options(GastoCategoria::distinct()->pluck('grupo', 'grupo')),

                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('fecha_desde'),
                        Forms\Components\DatePicker::make('fecha_hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['fecha_desde'], fn ($q) => $q->whereDate('fecha', '>=', $data['fecha_desde']))
                            ->when($data['fecha_hasta'], fn ($q) => $q->whereDate('fecha', '<=', $data['fecha_hasta']));
                    }),
            ])
            ->actions([
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar')->color('danger'),
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
            'index'  => \App\Filament\Resources\GastoResource\Pages\ListGastos::route('/'),
            'create' => \App\Filament\Resources\GastoResource\Pages\CreateGasto::route('/create'),
            'edit'   => \App\Filament\Resources\GastoResource\Pages\EditGasto::route('/{record}/edit'),
            'view'   => \App\Filament\Resources\GastoResource\Pages\ViewGasto::route('/{record}'),
        ];
    }
}
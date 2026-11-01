<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\GastoResource\Pages\ListGastos;
use App\Filament\Resources\GastoResource\Pages\CreateGasto;
use App\Filament\Resources\GastoResource\Pages\EditGasto;
use App\Filament\Resources\GastoResource\Pages\ViewGasto;
use App\Models\Gasto;
use App\Models\GastoCategoria;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GastoResource extends Resource
{
    protected static ?string $model = Gasto::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Finanzas';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-arrow-trending-down';
    protected static ?string $navigationLabel = 'Gastos';
    protected static ?string $modelLabel = 'Gasto';
    protected static ?string $pluralModelLabel = 'Gastos';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'gastos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->tabs([
                        Tab::make('Información Principal')
                            ->schema([
                                DatePicker::make('fecha')
                                    ->label('FECHA')
                                    ->default(now())
                                    ->required()
                                    ->displayFormat('d/m/Y'),

                                // CATEGORÍA = grupo
                                Select::make('grupo')
                                    ->label('CATEGORÍA')
                                    ->options(GastoCategoria::distinct()->pluck('grupo', 'grupo'))
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->placeholder('Seleccione una categoría'),

                                // SUBCATEGORÍA = nombre (dependiente del grupo)
                                Select::make('subcategoria')
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

                                TextInput::make('responsable')
                                    ->label('RESPONSABLE')
                                    ->maxLength(255)
                                    ->placeholder('Ej: MIGUEL ALBERTO, GYNA, JACSON'),
                            ])
                            ->columns(2),

                        Tab::make('Detalle del Gasto')
                            ->schema([
                                Textarea::make('detalle')
                                    ->label('DETALLE')
                                    ->rows(3)
                                    ->required()
                                    ->placeholder('Descripción detallada del gasto...'),

                                TextInput::make('importe')
                                    ->label('IMPORTE (S/)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->required()
                                    ->prefix('S/')
                                    ->default(0),

                                Select::make('obs')
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
                TextColumn::make('fecha')
                    ->label('FECHA')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('grupo')
                    ->label('CATEGORÍA')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('subcategoria')
                    ->label('SUBCATEGORÍA')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('responsable')
                    ->label('RESPONSABLE')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('detalle')
                    ->label('DETALLE')
                    ->limit(60)
                    ->tooltip(fn (Gasto $record) => $record->detalle)
                    ->searchable(),

                TextColumn::make('importe')
                    ->label('IMPORTE')
                    ->money('PEN')
                    ->sortable()
                    ->alignRight()
                    ->toggleable(),

                TextColumn::make('obs')
                    ->label('OBS.')
                    ->searchable()
                    ->toggleable(),
            ])
            ->defaultSort('fecha', 'desc')
            ->searchable()
            ->filters([
                SelectFilter::make('grupo')
                    ->label('Categoría')
                    ->options(GastoCategoria::distinct()->pluck('grupo', 'grupo')),

                Filter::make('fecha')
                    ->schema([
                        DatePicker::make('fecha_desde'),
                        DatePicker::make('fecha_hasta'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['fecha_desde'], fn ($q) => $q->whereDate('fecha', '>=', $data['fecha_desde']))
                            ->when($data['fecha_hasta'], fn ($q) => $q->whereDate('fecha', '<=', $data['fecha_hasta']));
                    }),
            ])
            ->recordActions([
                EditAction::make()->label('Editar'),
                DeleteAction::make()->label('Eliminar')->color('danger'),
            ])
            
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListGastos::route('/'),
            'create' => CreateGasto::route('/create'),
            'edit'   => EditGasto::route('/{record}/edit'),
            'view'   => ViewGasto::route('/{record}'),
        ];
    }
}
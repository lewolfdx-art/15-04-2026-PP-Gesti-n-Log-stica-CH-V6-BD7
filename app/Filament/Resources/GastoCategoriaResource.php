<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\GastoCategoriaResource\Pages\ListGastoCategorias;
use App\Filament\Resources\GastoCategoriaResource\Pages\CreateGastoCategoria;
use App\Filament\Resources\GastoCategoriaResource\Pages\EditGastoCategoria;
use App\Filament\Resources\GastoCategoriaResource\Pages;
use App\Models\GastoCategoria;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GastoCategoriaResource extends Resource
{
    protected static ?string $model = GastoCategoria::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Operaciones'; 
    protected static ?string $pluralLabel = 'Categorías de Gastos';
    protected static ?string $singularLabel = 'Categoría de Gasto';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('grupo')
                    ->required()
                    ->label('Grupo Principal')
                    ->maxLength(255),

                TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre de la Categoría'),

                Select::make('parent_id')
                    ->relationship('parent', 'nombre')
                    ->label('Categoría Padre')
                    ->searchable()
                    ->preload(),

                TextInput::make('orden')
                    ->numeric()
                    ->default(0),

                Toggle::make('activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('grupo')
                    ->badge()
                    ->sortable()
                    ->searchable(),                    // ← Búsqueda

                TextColumn::make('nombre')
                    ->searchable()                     // ← Búsqueda principal
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('parent.nombre')
                    ->label('Categoría Padre')
                    ->searchable()                     // ← También busca en padre
                    ->toggleable(),

                TextColumn::make('orden')
                    ->sortable()
                    ->alignCenter(),

                ToggleColumn::make('activo'),
            ])
            ->defaultSort('grupo')
            ->defaultSort('orden', 'asc')
            // ==================== MOTOR DE BÚSQUEDA GLOBAL ====================
            ->searchable()           // Activa la barra de búsqueda global
            ->filters([
                SelectFilter::make('grupo')
                    ->options([
                        'MATERIA PRIMA'     => 'Materia Prima',
                        'COMBUSTIBLE'       => 'Combustible',
                        'GASTOS FIJOS'      => 'Gastos Fijos',
                        'GASTOS PERSONAL'   => 'Gastos Personal',
                        'EQUIPOS ALQUILER'  => 'Equipos Alquiler',
                        'FLETES'            => 'Fletes',
                        'PRESTAMOS'         => 'Préstamos',
                        'SEGUROS IMPUESTOS' => 'Seguros e Impuestos',
                        'COMISIONES VENTAS' => 'Comisiones y Ventas',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index'  => ListGastoCategorias::route('/'),
            'create' => CreateGastoCategoria::route('/create'),
            'edit'   => EditGastoCategoria::route('/{record}/edit'),
        ];
    }
}
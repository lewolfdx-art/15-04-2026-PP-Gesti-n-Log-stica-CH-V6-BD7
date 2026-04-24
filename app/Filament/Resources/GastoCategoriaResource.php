<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GastoCategoriaResource\Pages;
use App\Models\GastoCategoria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GastoCategoriaResource extends Resource
{
    protected static ?string $model = GastoCategoria::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Finanzas'; 
    protected static ?string $pluralLabel = 'Categorías de Gastos';
    protected static ?string $singularLabel = 'Categoría de Gasto';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('grupo')
                    ->required()
                    ->label('Grupo Principal')
                    ->maxLength(255),

                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255)
                    ->label('Nombre de la Categoría'),

                Forms\Components\Select::make('parent_id')
                    ->relationship('parent', 'nombre')
                    ->label('Categoría Padre')
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('orden')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grupo')
                    ->badge()
                    ->sortable()
                    ->searchable(),                    // ← Búsqueda

                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()                     // ← Búsqueda principal
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('parent.nombre')
                    ->label('Categoría Padre')
                    ->searchable()                     // ← También busca en padre
                    ->toggleable(),

                Tables\Columns\TextColumn::make('orden')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('activo'),
            ])
            ->defaultSort('grupo')
            ->defaultSort('orden', 'asc')
            // ==================== MOTOR DE BÚSQUEDA GLOBAL ====================
            ->searchable()           // Activa la barra de búsqueda global
            ->filters([
                Tables\Filters\SelectFilter::make('grupo')
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index'  => Pages\ListGastoCategorias::route('/'),
            'create' => Pages\CreateGastoCategoria::route('/create'),
            'edit'   => Pages\EditGastoCategoria::route('/{record}/edit'),
        ];
    }
}
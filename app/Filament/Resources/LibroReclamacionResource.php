<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LibroReclamacionResource\Pages;
use App\Filament\Resources\LibroReclamacionResource\RelationManagers;
use App\Models\LibroReclamacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LibroReclamacionResource extends Resource
{
    protected static ?string $model = LibroReclamacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Gestión de Contenido';
    protected static ?int $navigationSort = 6;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLibroReclamacions::route('/'),
            'create' => Pages\CreateLibroReclamacion::route('/create'),
            'edit' => Pages\EditLibroReclamacion::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NosotrosResource\Pages;
use App\Filament\Resources\NosotrosResource\RelationManagers;
use App\Models\Nosotros;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NosotrosResource extends Resource
{
    protected static ?string $model = Nosotros::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            'index' => Pages\ListNosotros::route('/'),
            'create' => Pages\CreateNosotros::route('/create'),
            'edit' => Pages\EditNosotros::route('/{record}/edit'),
        ];
    }
}

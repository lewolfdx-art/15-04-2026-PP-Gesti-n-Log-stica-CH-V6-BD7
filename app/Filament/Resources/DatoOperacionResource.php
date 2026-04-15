<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DatoOperacionResource\Pages;
use App\Models\DatoOperacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DatoOperacionResource extends Resource
{
    protected static ?string $model = DatoOperacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Gestión de Gastos';
    protected static ?string $pluralLabel = 'Datos de Operación';
    protected static ?string $singularLabel = 'Dato de Operación';
    protected static ?string $recordTitleAttribute = 'valor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipo')
                    ->options([
                        'modo_pago'     => '🔸 Modo de Pago',
                        'banco'         => '🏦 Banco',
                        'asesor'        => '👤 Asesor',
                        'cancelacion'   => '✅ Cancelación',
                        'estado'        => '📊 Estado',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\TextInput::make('valor')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('descripcion')
                    ->rows(3)
                    ->columnSpanFull(),

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
                Tables\Columns\TextColumn::make('tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'modo_pago'     => 'info',      // Azul
                        'banco'         => 'success',   // Verde
                        'asesor'        => 'warning',   // Amarillo/Naranja
                        'cancelacion'   => 'danger',    // Rojo
                        'estado'        => 'gray',      // Gris
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'modo_pago'     => 'Modo de Pago',
                        'banco'         => 'Banco',
                        'asesor'        => 'Asesor',
                        'cancelacion'   => 'Cancelación',
                        'estado'        => 'Estado',
                        default         => ucfirst($state),
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('valor')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('descripcion')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('orden')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('activo'),
            ])
            ->defaultSort('tipo')
            ->defaultSort('orden', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->options([
                        'modo_pago'   => 'Modo de Pago',
                        'banco'       => 'Banco',
                        'asesor'      => 'Asesor',
                        'cancelacion' => 'Cancelación',
                        'estado'      => 'Estado',
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
            'index'  => Pages\ListDatoOperacions::route('/'),
            'create' => Pages\CreateDatoOperacion::route('/create'),
            'edit'   => Pages\EditDatoOperacion::route('/{record}/edit'),
        ];
    }
}
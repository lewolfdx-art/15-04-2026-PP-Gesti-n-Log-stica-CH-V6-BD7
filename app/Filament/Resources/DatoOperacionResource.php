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
    protected static ?string $navigationGroup = 'Gestión de Operaciones';
    protected static ?string $pluralLabel = 'Datos de Operación';
    protected static ?string $singularLabel = 'Dato de Operación';
    protected static ?string $recordTitleAttribute = 'valor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'modo_pago'   => '🔸 Modo de Pago',
                        'banco'       => '🏦 Banco',
                        'cancelacion' => '✅ Cancelación',
                        'estado'      => '📊 Estado',
                    ])
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->live(),

                Forms\Components\TextInput::make('valor')
                    ->label('Valor / Nombre')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción / Observación')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => 
                        match($state) {
                            'modo_pago'   => 'Modo de Pago',
                            'banco'       => 'Banco',
                            'cancelacion' => 'Cancelación',
                            'estado'      => 'Estado',
                            default       => str_replace('_', ' ', ucwords($state)),
                        }
                    )
                    ->color(fn (string $state): string => 
                        match($state) {
                            'estado'      => 'success',
                            'banco'       => 'info',
                            'modo_pago'   => 'danger',
                            'cancelacion' => 'gray',
                            default       => 'primary',
                        }
                    )
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor / Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('orden')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\ToggleColumn::make('activo')
                    ->label('Activo'),
            ])
            ->defaultSort('orden', 'asc')
            ->searchable()
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'modo_pago'   => 'Modo de Pago',
                        'banco'       => 'Banco',
                        'cancelacion' => 'Cancelación',
                        'estado'      => 'Estado',
                    ])
                    ->multiple()
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('activo'),
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
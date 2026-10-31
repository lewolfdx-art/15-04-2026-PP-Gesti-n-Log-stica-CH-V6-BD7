<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\DatoOperacionResource\Pages\ListDatoOperacions;
use App\Filament\Resources\DatoOperacionResource\Pages\CreateDatoOperacion;
use App\Filament\Resources\DatoOperacionResource\Pages\EditDatoOperacion;
use App\Filament\Resources\DatoOperacionResource\Pages;
use App\Models\DatoOperacion;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DatoOperacionResource extends Resource
{
    protected static ?string $model = DatoOperacion::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Operaciones';
    protected static ?string $pluralLabel = 'Datos de Operación';
    protected static ?string $singularLabel = 'Dato de Operación';
    protected static ?string $recordTitleAttribute = 'valor';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo')
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

                TextInput::make('valor')
                    ->label('Valor / Nombre')
                    ->required()
                    ->maxLength(255),

                Textarea::make('descripcion')
                    ->label('Descripción / Observación')
                    ->rows(3)
                    ->columnSpanFull(),

                TextInput::make('orden')
                    ->label('Orden')
                    ->numeric()
                    ->default(0),

                Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tipo')
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

                TextColumn::make('valor')
                    ->label('Valor / Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(80)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('orden')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                ToggleColumn::make('activo')
                    ->label('Activo'),
            ])
            ->defaultSort('orden', 'asc')
            ->searchable()
            ->filters([
                SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'modo_pago'   => 'Modo de Pago',
                        'banco'       => 'Banco',
                        'cancelacion' => 'Cancelación',
                        'estado'      => 'Estado',
                    ])
                    ->multiple()
                    ->searchable(),

                TernaryFilter::make('activo'),
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
            'index'  => ListDatoOperacions::route('/'),
            'create' => CreateDatoOperacion::route('/create'),
            'edit'   => EditDatoOperacion::route('/{record}/edit'),
        ];
    }
}
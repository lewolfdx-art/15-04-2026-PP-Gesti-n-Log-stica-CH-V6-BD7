<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehiculoResource\Pages\CreateVehiculo;
use App\Filament\Resources\VehiculoResource\Pages\EditVehiculo;
use App\Filament\Resources\VehiculoResource\Pages\ListVehiculos;
use App\Models\Vehiculo;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Mixers y bombas. Se asignan a los vaciados en la Programación, que avisa si uno ya está ocupado.
 */
class VehiculoResource extends Resource
{
    protected static ?string $model = Vehiculo::class;

    // El programador de vaciados consulta la flota; solo los administradores la modifican
    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin', 'coordinador_de_vaciados']) ?? false;
    }

    private static function esAdministrador(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function canCreate(): bool
    {
        return static::esAdministrador();
    }

    public static function canEdit(Model $record): bool
    {
        return static::esAdministrador();
    }

    public static function canDelete(Model $record): bool
    {
        return static::esAdministrador();
    }

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Operaciones';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Vehículos';
    protected static ?string $modelLabel = 'Vehículo';
    protected static ?string $pluralModelLabel = 'Vehículos';
    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'vehiculos';

    protected static ?string $recordTitleAttribute = 'codigo';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('codigo')
                    ->label('Código')
                    ->placeholder('Ej: MIXER-01')
                    ->required()
                    ->maxLength(30)
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn (?string $state) => strtoupper(trim((string) $state))),

                Select::make('tipo')
                    ->label('Tipo')
                    ->options(Vehiculo::TIPOS)
                    ->default('mixer')
                    ->required()
                    ->native(false),

                TextInput::make('placa')
                    ->label('Placa')
                    ->maxLength(15)
                    ->dehydrateStateUsing(fn (?string $state) => $state ? strtoupper(trim($state)) : null),

                TextInput::make('capacidad_m3')
                    ->label('Capacidad (m³)')
                    ->numeric()
                    ->minValue(0),

                Toggle::make('activo')
                    ->label('Activo')
                    ->helperText('Solo los vehículos activos se pueden asignar a un vaciado.')
                    ->default(true),

                Textarea::make('observacion')
                    ->label('Observación')
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('codigo')->label('Código')->searchable()->sortable()->weight('bold'),
                TextColumn::make('tipo')->label('Tipo')->badge()
                    ->formatStateUsing(fn (string $state): string => Vehiculo::TIPOS[$state] ?? $state)
                    ->icon(fn (string $state): ?string => Vehiculo::TIPO_ICONOS[$state] ?? null),
                TextColumn::make('placa')->label('Placa')->searchable()->placeholder('—'),
                TextColumn::make('capacidad_m3')->label('Capacidad')->suffix(' m³')->placeholder('—'),
                TextColumn::make('contratos_count')->label('Vaciados')->counts('contratos')->badge()->alignCenter(),
                ToggleColumn::make('activo')->label('Activo')->disabled(fn (): bool => ! static::esAdministrador()),
            ])
            ->defaultSort('codigo')
            ->filters([
                SelectFilter::make('tipo')->label('Tipo')->options(Vehiculo::TIPOS),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Sin vehículos registrados')
            ->emptyStateDescription('Registra tus mixers y bombas para asignarlos al programar los vaciados.');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListVehiculos::route('/'),
            'create' => CreateVehiculo::route('/create'),
            'edit'   => EditVehiculo::route('/{record}/edit'),
        ];
    }
}

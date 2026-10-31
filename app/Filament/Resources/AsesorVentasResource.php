<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsesorVentasResource\Pages\CreateAsesorVentas;
use App\Filament\Resources\AsesorVentasResource\Pages\EditAsesorVentas;
use App\Filament\Resources\AsesorVentasResource\Pages\ListAsesorVentas;
use App\Filament\Resources\Concerns\CreaAsesorDeVentas;
use App\Models\Contrato;
use App\Models\Trabajador;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Asesores de ventas: comparten la tabla `trabajadores` (tipo_cargo = asesor_ventas)
 * pero tienen su propio módulo porque cumplen un rol comercial distinto al personal.
 */
class AsesorVentasResource extends Resource
{
    use CreaAsesorDeVentas;

    protected static ?string $model = Trabajador::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Comercial';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Asesores de ventas';
    protected static ?string $modelLabel = 'Asesor de ventas';
    protected static ?string $pluralModelLabel = 'Asesores de ventas';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'asesores-ventas';

    protected static ?string $recordTitleAttribute = 'nombre_completo';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->asesores();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...static::formularioCrearAsesor(),

                Toggle::make('activo')
                    ->label('Activo')
                    ->helperText('Solo los asesores activos aparecen al registrar contratos e ingresos.')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre_completo')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('dni')
                    ->label('DNI')
                    ->searchable(),

                TextColumn::make('fecha_nacimiento')
                    ->label('Fecha Nac.')
                    ->date('d/m/Y')
                    ->toggleable(),

                // Los contratos guardan el nombre del vendedor como texto
                TextColumn::make('contratos')
                    ->label('Contratos')
                    ->state(fn (Trabajador $record): int => Contrato::where('nombre_vendedor', $record->nombre_completo)->count())
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray')
                    ->alignCenter(),

                TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('orden')
                    ->sortable()
                    ->alignCenter(),

                ToggleColumn::make('activo'),
            ])
            ->defaultSort('orden', 'asc')
            ->filters([
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
            'index'  => ListAsesorVentas::route('/'),
            'create' => CreateAsesorVentas::route('/create'),
            'edit'   => EditAsesorVentas::route('/{record}/edit'),
        ];
    }
}

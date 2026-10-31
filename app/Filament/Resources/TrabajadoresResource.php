<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\TrabajadoresResource\Pages\ListTrabajadores;
use App\Filament\Resources\TrabajadoresResource\Pages\CreateTrabajadores;
use App\Filament\Resources\TrabajadoresResource\Pages\EditTrabajadores;
use App\Filament\Resources\TrabajadoresResource\Pages;
use App\Models\Trabajador;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrabajadoresResource extends Resource
{
    protected static ?string $model = Trabajador::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';
    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Personal';
    protected static ?string $pluralLabel = 'Trabajadores y Asesores';
    protected static ?string $singularLabel = 'Trabajador';
    protected static ?string $recordTitleAttribute = 'nombre_completo';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tipo_cargo')
                    ->label('Cargo / Tipo')
                    ->options([
                        'asesor_ventas'                => '💼 Asesor de Ventas',
                        'operador_planta'              => '👷 Operador de Planta',
                        'operador_retroexcavadora'     => '🚜 Operador de Retroexcavadora',
                        'ayudante_planta'              => '👷 Ayudante de Planta',
                        'operador_bomba_telescopica'   => '🚧 Operador de Bomba Telescópica',
                        'ayudante_manguerero_bt'       => '👷 Ayudante Manguerero B.T',
                        'ayudante_vibrador_bt'         => '👷 Ayudante Vibrador B.T',
                        'jefe_mantenimiento'           => '🛠️ Jefe de Mantenimiento',
                        'operador_mixer'               => '🚛 Operador de Mixer',
                        'operador_trayler'             => '🚛 Operador de Trayler',
                        'ayudante_bomba'               => '👷 Ayudante de Bomba',
                        'asistente_calidad'            => '📋 Asistente de Calidad',
                        'administracion'               => '🏢 Administración',
                        'asistente_administrativo'     => '📑 Asistente Administrativo',
                        'planeamiento'                 => '📊 Planeamiento',
                        'seguridad_noche'              => '🌙 Seguridad de Noche',
                        'contador'                     => '💰 Contador',
                        'personal_curado'              => '👷 Personal de Curado',
                    ])
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->live(),

                TextInput::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(255),

                TextInput::make('dni')
                    ->label('DNI')
                    ->required()
                    ->mask('99999999')
                    ->placeholder('12345678')
                    ->rule('digits:8')
                    ->unique(ignoreRecord: true),

                // SWITCH PARA HABILITAR/DESHABILITAR FECHA DE NACIMIENTO
                Toggle::make('tiene_fecha_nacimiento')
                    ->label('¿Tiene fecha de nacimiento?')
                    ->default(true)
                    ->live()
                    ->columnSpanFull(),

                DatePicker::make('fecha_nacimiento')
                    ->label('Fecha de Nacimiento')
                    ->default(now())
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->maxDate(now())
                    ->closeOnDateSelection(true)
                    ->placeholder('dd/mm/yyyy')
                    ->required(fn (Get $get) => $get('tiene_fecha_nacimiento') === true)
                    ->disabled(fn (Get $get) => $get('tiene_fecha_nacimiento') === false)
                    ->dehydrated(fn (Get $get) => $get('tiene_fecha_nacimiento') === true)
                    ->rules(fn (Get $get) => $get('tiene_fecha_nacimiento') === true ? ['required', 'date'] : ['nullable', 'date'])
                    ->afterStateHydrated(function ($state, callable $set) {
                        if (empty($state)) {
                            $set('tiene_fecha_nacimiento', false);
                        }
                    }),

                Textarea::make('descripcion')
                    ->label('Descripción / Observaciones')
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
                TextColumn::make('tipo_cargo')
                    ->label('Cargo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'asesor_ventas'                => '💼 Asesor de Ventas',
                        'operador_planta'              => '👷 Operador de Planta',
                        'operador_retroexcavadora'     => '🚜 Operador de Retroexcavadora',
                        'ayudante_planta'              => '👷 Ayudante de Planta',
                        'operador_bomba_telescopica'   => '🚧 Operador Bomba Telescópica',
                        'ayudante_manguerero_bt'       => '👷 Ayudante Manguerero',
                        'ayudante_vibrador_bt'         => '👷 Ayudante Vibrador',
                        'jefe_mantenimiento'           => '🛠️ Jefe de Mantenimiento',
                        'operador_mixer'               => '🚛 Operador de Mixer',
                        'operador_trayler'             => '🚛 Operador de Trayler',
                        'ayudante_bomba'               => '👷 Ayudante de Bomba',
                        'asistente_calidad'            => '📋 Asistente de Calidad',
                        'administracion'               => '🏢 Administración',
                        'asistente_administrativo'     => '📑 Asistente Administrativo',
                        'planeamiento'                 => '📊 Planeamiento',
                        'seguridad_noche'              => '🌙 Seguridad Noche',
                        'contador'                     => '💰 Contador',
                        'personal_curado'              => '👷 Personal de Curado',
                        default => str_replace('_', ' ', ucwords($state)),
                    })
                    ->color('primary')
                    ->sortable(),

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
                    ->date('d/m/Y'),

                TextColumn::make('descripcion')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('orden')
                    ->sortable()
                    ->alignCenter(),

                ToggleColumn::make('activo'),
            ])
            ->defaultSort('orden', 'asc')
            ->searchable()
            ->filters([
                SelectFilter::make('tipo_cargo')
                    ->label('Cargo')
                    ->options([
                        'asesor_ventas' => '💼 Asesor de Ventas',
                        'operador_planta' => '👷 Operador de Planta',
                        // puedes agregar más aquí si quieres
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
            'index'  => ListTrabajadores::route('/'),
            'create' => CreateTrabajadores::route('/create'),
            'edit'   => EditTrabajadores::route('/{record}/edit'),
        ];
    }
}
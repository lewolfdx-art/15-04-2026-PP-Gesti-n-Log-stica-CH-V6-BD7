<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrabajadoresResource\Pages;
use App\Models\Trabajador;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrabajadoresResource extends Resource
{
    protected static ?string $model = Trabajador::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Gestión de Personal';
    protected static ?string $pluralLabel = 'Trabajadores y Asesores';
    protected static ?string $singularLabel = 'Trabajador';
    protected static ?string $recordTitleAttribute = 'nombre_completo';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipo_cargo')
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

                Forms\Components\TextInput::make('nombre_completo')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('dni')
                    ->label('DNI')
                    ->required()
                    ->mask('99999999')                    // ← Esta es la mejor solución en Filament
                    ->placeholder('12345678')
                    ->rule('digits:8')
                    ->unique(ignoreRecord: true),

                Forms\Components\DatePicker::make('fecha_nacimiento')
                    ->label('Fecha de Nacimiento')
                    ->default(now())
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->maxDate(now())
                    ->closeOnDateSelection(true)
                    ->placeholder('dd/mm/yyyy'),

                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción / Observaciones')
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
                Tables\Columns\TextColumn::make('tipo_cargo')
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

                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('dni')
                    ->label('DNI')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fecha_nacimiento')
                    ->label('Fecha Nac.')
                    ->date('d/m/Y'),

                Tables\Columns\TextColumn::make('descripcion')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('orden')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\ToggleColumn::make('activo'),
            ])
            ->defaultSort('orden', 'asc')
            ->searchable()
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_cargo')
                    ->label('Cargo')
                    ->options([
                        'asesor_ventas' => '💼 Asesor de Ventas',
                        'operador_planta' => '👷 Operador de Planta',
                        // puedes agregar más aquí si quieres
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
            'index'  => Pages\ListTrabajadores::route('/'),
            'create' => Pages\CreateTrabajadores::route('/create'),
            'edit'   => Pages\EditTrabajadores::route('/{record}/edit'),
        ];
    }
}
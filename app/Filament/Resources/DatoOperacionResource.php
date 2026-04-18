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
                    ->label('Tipo')
                    ->options([
                        // ==================== TIPOS GENERALES ====================
                        'modo_pago'     => '🔸 Modo de Pago',
                        'banco'         => '🏦 Banco',
                        'asesor'        => '👤 Asesor',
                        'cancelacion'   => '✅ Cancelación',
                        'estado'        => '📊 Estado',

                        // ==================== CARGOS / PERSONAL ====================
                        'operador_planta'              => '👷 OPERADOR DE PLANTA',
                        'operador_retroexcavadora'     => '👷 OPERADOR DE RETROEXCAVADORA',
                        'ayudante_planta'              => '👷 AYUDANTE DE PLANTA',
                        'operador_bomba_telescopica'   => '👷 OPERADOR DE BOMBA TELESCOPICA',
                        'ayudante_manguerero_bt'       => '👷 AYUDANTE DE MANGUERERO DE B.T',
                        'ayudante_vibrador_bt'         => '👷 AYUDANTE VIBRADOR DE BOMBA TELESCOPICA',
                        'jefe_mantenimiento'           => '🛠️ JEFE DE MANTENIMIENTO',
                        'operador_mixer'               => '👷 OPERADOR DE MIXER',
                        'operador_trayler'             => '👷 OPERADOR DE TRAYLER',
                        'ayudante_bomba'               => '👷 AYUDANTE DE BOMBA',
                        'asesor_ventas'                => '👷 ASESOR DE VENTAS',
                        'asistente_calidad'            => '👷 ASISTENTE DE CALIDAD',
                        'administracion'               => '📈 ADMINISTRACIÓN',
                        'asistente_administrativo'     => '📝 ASISTENTE ADMINISTRATIVO',
                        'planeamiento'                 => '👷 PLANEAMIENTO',
                        'seguridad_noche'              => '💂 SEGURIDAD DE NOCHE',
                        'contador'                     => '🧑‍⚖️ CONTADOR',
                        'personal_curado'              => '👷 PERSONAL DE CURADO',
                    ])
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->live(),

                Forms\Components\Toggle::make('es_persona')
                    ->label('Es Persona (Activar DNI y Fecha de Nacimiento)')
                    ->default(false)
                    ->live()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('valor')
                    ->label('Nombre Completo')
                    ->required()
                    ->maxLength(255),

                // === Campos que aparecen solo si el toggle está activado ===
                Forms\Components\TextInput::make('documento')
                    ->label('Documento de Identidad (DNI)')
                    ->visible(fn (Forms\Get $get) => $get('es_persona'))
                    ->maxLength(20),

                Forms\Components\DatePicker::make('fecha_nacimiento')
                    ->label('Fecha de Nacimiento')
                    ->visible(fn (Forms\Get $get) => $get('es_persona'))
                    ->native(false),

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
                        'modo_pago'     => 'Modo de Pago',
                        'banco'         => 'Banco',
                        'asesor'        => 'Asesor',
                        'cancelacion'   => 'Cancelación',
                        'estado'        => 'Estado',
            
                        // Cargos / Personal (puedes mantener los emojis si quieres)
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
                        'asesor_ventas'                => '💼 Asesor de Ventas',
                        'asistente_calidad'            => '📋 Asistente de Calidad',
                        'administracion'               => '🏢 Administración',
                        'asistente_administrativo'     => '📑 Asistente Administrativo',
                        'planeamiento'                 => '📊 Planeamiento',
                        'seguridad_noche'              => '🌙 Seguridad de Noche',
                        'contador'                     => '💰 Contador',
                        'personal_curado'              => '👷 Personal de Curado',
            
                        default => str_replace('_', ' ', ucwords($state)),
                    }
                )
                ->color(fn (string $state): string => 
                    match($state) {
                        'estado'      => 'success',     // Verde
                        'banco'       => 'info',        // Azul
                        'modo_pago'   => 'danger',      // Rojo
                        'cancelacion' => 'gray',        // Gris
                        'asesor'      => 'warning',     // Amarillo / Naranja (opcional)
                        default       => 'primary',     // Azul por defecto para los cargos
                    }
                )
                ->sortable()
                ->toggleable(),

                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor / Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('documento')
                    ->label('DNI')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('fecha_nacimiento')
                    ->label('Fecha Nac.')
                    ->date('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(60)
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('orden')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\ToggleColumn::make('activo')
                    ->label('Activo')
                    ->toggleable(),
            ])
            ->defaultSort('orden', 'asc')
            ->searchable()
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([

                        // OTROS TIPOS
                        'modo_pago'     => 'Modo de Pago',
                        'banco'         => 'Banco',
                        'asesor'        => 'Asesor',
                        'cancelacion'   => 'Cancelación',
                        'estado'        => 'Estado',                        

                        // CARGOS
                        'operador_planta'              => '👷 OPERADOR DE PLANTA',
                        'operador_retroexcavadora'     => '🚜 OPERADOR DE RETROEXCAVADORA',
                        'ayudante_planta'              => '👷 AYUDANTE DE PLANTA',
                        'operador_bomba_telescopica'   => '🚧 OPERADOR DE BOMBA TELESCOPICA',
                        'ayudante_manguerero_bt'       => '👷 AYUDANTE DE MANGUERERO DE B.T',
                        'ayudante_vibrador_bt'         => '👷 AYUDANTE VIBRADOR DE BOMBA TELESCOPICA',
                        'jefe_mantenimiento'           => '🛠️ JEFE DE MANTENIMIENTO',
                        'operador_mixer'               => '🚛 OPERADOR DE MIXER',
                        'operador_trayler'             => '🚛 OPERADOR DE TRAYLER',
                        'ayudante_bomba'               => '👷 AYUDANTE DE BOMBA',
                        'asesor_ventas'                => '💼 ASESOR DE VENTAS',
                        'asistente_calidad'            => '📋 ASISTENTE DE CALIDAD',
                        'administracion'               => '🏢 ADMINISTRACIÓN',
                        'asistente_administrativo'     => '📑 ASISTENTE ADMINISTRATIVO',
                        'planeamiento'                 => '📊 PLANEAMIENTO',
                        'seguridad_noche'              => '🌙 SEGURIDAD DE NOCHE',
                        'contador'                     => '💰 CONTADOR',
                        'personal_curado'              => '👷 PERSONAL DE CURADO',


                    ])
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo')
                    ->trueLabel('Solo Activos')
                    ->falseLabel('Solo Inactivos')
                    ->placeholder('Todos'),
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
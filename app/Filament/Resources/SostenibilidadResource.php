<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\SostenibilidadResource\Pages\ListSostenibilidads;
use App\Filament\Resources\SostenibilidadResource\Pages\CreateSostenibilidad;
use App\Filament\Resources\SostenibilidadResource\Pages\EditSostenibilidad;
use App\Filament\Resources\SostenibilidadResource\Pages;
use App\Models\Sostenibilidad;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;

class SostenibilidadResource extends Resource
{
    protected static ?string $model = Sostenibilidad::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Gestión de Contenido';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-globe-americas';
    protected static ?string $navigationLabel = 'Sostenibilidad';
    protected static ?string $modelLabel = 'Sostenibilidad';
    protected static ?string $pluralModelLabel = 'Sostenibilidad';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'sostenibilidad';
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Configuración de Sostenibilidad')
                    ->tabs([
                        // ===================== HERO =====================
                        Tab::make('Hero Principal')
                            ->schema([
                                TextInput::make('hero_title')
                                    ->label('Título Principal')
                                    ->default('COMPROMISO CON LA SOSTENIBILIDAD')
                                    ->maxLength(255),
                                
                                FileUpload::make('hero_image')
                                    ->label('Imagen Hero')
                                    ->image()
                                    ->directory('sostenibilidad')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                                
                                RichEditor::make('hero_description')
                                    ->label('Texto Introductorio')
                                    ->default('Habla sobre el compromiso de la empresa con la sostenibilidad, objetivos de desarrollo sostenible (ODS), ISO 14001, etc.')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList']),
                                
                                Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Solo un registro puede estar activo a la vez'),
                            ]),
                        
                        // ===================== TRES BLOQUES TEMÁTICOS =====================
                        Tab::make('Política Ambiental')
                            ->schema([
                                TextInput::make('politica_ambiental_title')
                                    ->label('Título')
                                    ->default('Política Ambiental')
                                    ->maxLength(255),
                                FileUpload::make('politica_ambiental_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('sostenibilidad')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1'),
                                Repeater::make('politica_ambiental_list')
                                    ->label('Lista de puntos')
                                    ->schema([
                                        TextInput::make('item')->label('Punto')->required(),
                                    ])
                                    ->default([
                                        ['item' => 'Reducción de emisiones de CO2'],
                                        ['item' => 'Gestión responsable de residuos'],
                                        ['item' => 'Uso eficiente del agua'],
                                        ['item' => 'Certificación ISO 14001'],
                                    ]),
                            ]),
                        
                        Tab::make('Seguridad y Salud')
                            ->schema([
                                TextInput::make('seguridad_salud_title')
                                    ->label('Título')
                                    ->default('Seguridad y Salud Ocupacional')
                                    ->maxLength(255),
                                FileUpload::make('seguridad_salud_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('sostenibilidad')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1'),
                                Repeater::make('seguridad_salud_list')
                                    ->label('Lista de puntos')
                                    ->schema([
                                        TextInput::make('item')->label('Punto')->required(),
                                    ])
                                    ->default([
                                        ['item' => 'Capacitación continua en seguridad'],
                                        ['item' => 'Equipos de protección certificados'],
                                        ['item' => 'Protocolos de emergencia'],
                                        ['item' => 'Auditorías periódicas'],
                                    ]),
                            ]),
                        
                        Tab::make('Responsabilidad Social')
                            ->schema([
                                TextInput::make('responsabilidad_social_title')
                                    ->label('Título')
                                    ->default('Responsabilidad Social')
                                    ->maxLength(255),
                                FileUpload::make('responsabilidad_social_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('sostenibilidad')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1'),
                                Repeater::make('responsabilidad_social_list')
                                    ->label('Lista de puntos')
                                    ->schema([
                                        TextInput::make('item')->label('Punto')->required(),
                                    ])
                                    ->default([
                                        ['item' => 'Apoyo a comunidades locales'],
                                        ['item' => 'Programas de voluntariado'],
                                        ['item' => 'Transparencia y ética'],
                                        ['item' => 'Desarrollo sostenible'],
                                    ]),
                            ]),
                        
                        // ===================== SEGURIDAD EN EL TRABAJO =====================
                        Tab::make('Seguridad en el Trabajo')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Repeater::make('epp_list')
                                            ->label('Equipos de Protección Personal (EPP)')
                                            ->schema([
                                                TextInput::make('item')->label('Equipo')->required(),
                                            ])
                                            ->default([
                                                ['item' => 'Casco de seguridad'],
                                                ['item' => 'Chaleco reflectante'],
                                                ['item' => 'Botas punta de acero'],
                                                ['item' => 'Guantes de protección'],
                                                ['item' => 'Gafas de seguridad'],
                                                ['item' => 'Arnés de seguridad'],
                                            ]),
                                        
                                        Repeater::make('protocolos_list')
                                            ->label('Otros Protocolos')
                                            ->schema([
                                                TextInput::make('item')->label('Protocolo')->required(),
                                            ])
                                            ->default([
                                                ['item' => 'Señalización de áreas'],
                                                ['item' => 'Permisos de trabajo en altura'],
                                                ['item' => 'Procedimientos de bloqueo/etiquetado'],
                                                ['item' => 'Inspección de herramientas'],
                                            ]),
                                    ]),
                            ]),
                        
                        // ===================== SUBMÓDULOS ERP =====================
                        Tab::make('Submódulos Gestión Seguridad')
                            ->schema([
                                Repeater::make('submodulos_list')
                                    ->label('Submódulos de Gestión de Seguridad (ERP)')
                                    ->schema([
                                        TextInput::make('item')->label('Módulo')->required(),
                                    ])
                                    ->default([
                                        ['item' => 'Capacitación'],
                                        ['item' => 'Gestión Estratégica'],
                                        ['item' => 'Promoción por Áreas'],
                                    ]),
                            ]),
                        
                        // ===================== RECURSOS ACADÉMICOS =====================
                        Tab::make('Recursos Académicos')
                            ->schema([
                                Repeater::make('recursos_academicos')
                                    ->label('Recursos Académicos y Normativos')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Título del recurso')
                                            ->required(),
                                        TextInput::make('source')
                                            ->label('Fuente')
                                            ->required(),
                                        TextInput::make('link')
                                            ->label('Enlace (opcional)')
                                            ->url(),
                                    ])
                                    ->columns(2)
                                    ->default([
                                        ['title' => 'Guía Técnica de Concreto y Normativa - AENOR - 2023', 'source' => 'AENOR'],
                                        ['title' => 'NTP 339.001 - Concreto Armado', 'source' => 'INACAL'],
                                    ]),
                            ]),
                        
                        // ===================== PROYECTOS REALIZADOS =====================
                        Tab::make('Proyectos Realizados')
                            ->schema([
                                Repeater::make('proyectos_realizados')
                                    ->label('Proyectos Realizados')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Título del proyecto')
                                            ->required(),
                                        Textarea::make('description')
                                            ->label('Descripción / Ubicación')
                                            ->rows(2),
                                        FileUpload::make('image')
                                            ->label('Imagen')
                                            ->image()
                                            ->directory('sostenibilidad/proyectos'),
                                    ])
                                    ->columns(1)
                                    ->default([
                                        ['title' => 'Centro de Salud Militar N°31', 'description' => 'Huancayo – Obra hospitalaria estratégica.'],
                                        ['title' => 'Multifamiliar Nature Edge', 'description' => 'Residencial de 14 pisos – Alcor Inmobiliaria.'],
                                        ['title' => 'Canalización Río Chilca', 'description' => 'Tramo Jr. Libertad – Av. Huancavelica con concreto hidráulico.'],
                                        ['title' => 'Residencial El Trébol', 'description' => 'Proyecto multifamiliar de departamentos en Huancayo.'],
                                    ]),
                            ]),
                        
                        // ===================== RECOMENDACIONES TÉCNICAS =====================
                        Tab::make('Recomendaciones Técnicas')
                            ->schema([
                                Repeater::make('recomendaciones_tecnicas')
                                    ->label('Recomendaciones Técnicas para Construcción')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Título')
                                            ->required(),
                                        RichEditor::make('description')
                                            ->label('Contenido')
                                            ->required()
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList']),
                                        FileUpload::make('image')
                                            ->label('Imagen')
                                            ->image()
                                            ->directory('sostenibilidad/recomendaciones'),
                                    ])
                                    ->columns(1)
                                    ->default([
                                        ['title' => 'Uso de materiales certificados', 'description' => 'Texto sobre la calidad de los concretos premezclados, normas NTP, ISO 9001...'],
                                        ['title' => 'Control del tiempo de vida del concreto fresco', 'description' => 'Texto sobre el tiempo de trabajabilidad del concreto (slump)...'],
                                        ['title' => 'Control de calidad técnico en obra', 'description' => 'Toma de muestras (NTP 339.012), Ensayo de slump, Método de probetas...'],
                                        ['title' => 'Diseño óptimo de mezcla según estructura', 'description' => 'Resistencia (f’c) requerida (210, 280, 350 kg/cm²)...'],
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->activeTab(0),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hero_title')
                    ->label('Título')
                    ->searchable()
                    ->limit(30),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Estado'),
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
            'index' => ListSostenibilidads::route('/'),
            'create' => CreateSostenibilidad::route('/create'),
            'edit' => EditSostenibilidad::route('/{record}/edit'),
        ];
    }
}
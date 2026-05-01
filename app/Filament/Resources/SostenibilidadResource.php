<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SostenibilidadResource\Pages;
use App\Models\Sostenibilidad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;

class SostenibilidadResource extends Resource
{
    protected static ?string $model = Sostenibilidad::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Gestión de Contenido';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Sostenibilidad';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Configuración de Sostenibilidad')
                    ->tabs([
                        // ===================== HERO =====================
                        Tabs\Tab::make('Hero Principal')
                            ->schema([
                                Forms\Components\TextInput::make('hero_title')
                                    ->label('Título Principal')
                                    ->default('COMPROMISO CON LA SOSTENIBILIDAD')
                                    ->maxLength(255),
                                
                                Forms\Components\FileUpload::make('hero_image')
                                    ->label('Imagen Hero')
                                    ->image()
                                    ->directory('sostenibilidad')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9'),
                                
                                Forms\Components\RichEditor::make('hero_description')
                                    ->label('Texto Introductorio')
                                    ->default('Habla sobre el compromiso de la empresa con la sostenibilidad, objetivos de desarrollo sostenible (ODS), ISO 14001, etc.')
                                    ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList']),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Activo')
                                    ->default(true)
                                    ->helperText('Solo un registro puede estar activo a la vez'),
                            ]),
                        
                        // ===================== TRES BLOQUES TEMÁTICOS =====================
                        Tabs\Tab::make('Política Ambiental')
                            ->schema([
                                Forms\Components\TextInput::make('politica_ambiental_title')
                                    ->label('Título')
                                    ->default('Política Ambiental')
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('politica_ambiental_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('sostenibilidad')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1'),
                                Repeater::make('politica_ambiental_list')
                                    ->label('Lista de puntos')
                                    ->schema([
                                        Forms\Components\TextInput::make('item')->label('Punto')->required(),
                                    ])
                                    ->default([
                                        ['item' => 'Reducción de emisiones de CO2'],
                                        ['item' => 'Gestión responsable de residuos'],
                                        ['item' => 'Uso eficiente del agua'],
                                        ['item' => 'Certificación ISO 14001'],
                                    ]),
                            ]),
                        
                        Tabs\Tab::make('Seguridad y Salud')
                            ->schema([
                                Forms\Components\TextInput::make('seguridad_salud_title')
                                    ->label('Título')
                                    ->default('Seguridad y Salud Ocupacional')
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('seguridad_salud_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('sostenibilidad')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1'),
                                Repeater::make('seguridad_salud_list')
                                    ->label('Lista de puntos')
                                    ->schema([
                                        Forms\Components\TextInput::make('item')->label('Punto')->required(),
                                    ])
                                    ->default([
                                        ['item' => 'Capacitación continua en seguridad'],
                                        ['item' => 'Equipos de protección certificados'],
                                        ['item' => 'Protocolos de emergencia'],
                                        ['item' => 'Auditorías periódicas'],
                                    ]),
                            ]),
                        
                        Tabs\Tab::make('Responsabilidad Social')
                            ->schema([
                                Forms\Components\TextInput::make('responsabilidad_social_title')
                                    ->label('Título')
                                    ->default('Responsabilidad Social')
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('responsabilidad_social_image')
                                    ->label('Imagen')
                                    ->image()
                                    ->directory('sostenibilidad')
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('1:1'),
                                Repeater::make('responsabilidad_social_list')
                                    ->label('Lista de puntos')
                                    ->schema([
                                        Forms\Components\TextInput::make('item')->label('Punto')->required(),
                                    ])
                                    ->default([
                                        ['item' => 'Apoyo a comunidades locales'],
                                        ['item' => 'Programas de voluntariado'],
                                        ['item' => 'Transparencia y ética'],
                                        ['item' => 'Desarrollo sostenible'],
                                    ]),
                            ]),
                        
                        // ===================== SEGURIDAD EN EL TRABAJO =====================
                        Tabs\Tab::make('Seguridad en el Trabajo')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Repeater::make('epp_list')
                                            ->label('Equipos de Protección Personal (EPP)')
                                            ->schema([
                                                Forms\Components\TextInput::make('item')->label('Equipo')->required(),
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
                                                Forms\Components\TextInput::make('item')->label('Protocolo')->required(),
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
                        Tabs\Tab::make('Submódulos Gestión Seguridad')
                            ->schema([
                                Repeater::make('submodulos_list')
                                    ->label('Submódulos de Gestión de Seguridad (ERP)')
                                    ->schema([
                                        Forms\Components\TextInput::make('item')->label('Módulo')->required(),
                                    ])
                                    ->default([
                                        ['item' => 'Capacitación'],
                                        ['item' => 'Gestión Estratégica'],
                                        ['item' => 'Promoción por Áreas'],
                                    ]),
                            ]),
                        
                        // ===================== RECURSOS ACADÉMICOS =====================
                        Tabs\Tab::make('Recursos Académicos')
                            ->schema([
                                Repeater::make('recursos_academicos')
                                    ->label('Recursos Académicos y Normativos')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título del recurso')
                                            ->required(),
                                        Forms\Components\TextInput::make('source')
                                            ->label('Fuente')
                                            ->required(),
                                        Forms\Components\TextInput::make('link')
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
                        Tabs\Tab::make('Proyectos Realizados')
                            ->schema([
                                Repeater::make('proyectos_realizados')
                                    ->label('Proyectos Realizados')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título del proyecto')
                                            ->required(),
                                        Forms\Components\Textarea::make('description')
                                            ->label('Descripción / Ubicación')
                                            ->rows(2),
                                        Forms\Components\FileUpload::make('image')
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
                        Tabs\Tab::make('Recomendaciones Técnicas')
                            ->schema([
                                Repeater::make('recomendaciones_tecnicas')
                                    ->label('Recomendaciones Técnicas para Construcción')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título')
                                            ->required(),
                                        Forms\Components\RichEditor::make('description')
                                            ->label('Contenido')
                                            ->required()
                                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList']),
                                        Forms\Components\FileUpload::make('image')
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
                Tables\Columns\TextColumn::make('hero_title')
                    ->label('Título')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado'),
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
            'index' => Pages\ListSostenibilidads::route('/'),
            'create' => Pages\CreateSostenibilidad::route('/create'),
            'edit' => Pages\EditSostenibilidad::route('/{record}/edit'),
        ];
    }
}